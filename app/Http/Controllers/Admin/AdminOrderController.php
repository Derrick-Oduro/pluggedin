<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductReview;
use App\Notifications\OrderStatusUpdatedNotification;
use App\Notifications\ReviewReminderNotification;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items'])->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'statusHistories.actor']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready_for_pickup,shipped,completed,cancelled,refunded',
            'tracking_number' => 'nullable|string|max:160',
        ]);

        $previousStatus = $order->status;
        $newStatus = $request->status;

        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['ready_for_pickup', 'shipped', 'cancelled'],
            'ready_for_pickup' => ['completed', 'cancelled'],
            'shipped' => ['completed'],
            'completed' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
        ];

        abort_unless($newStatus === $previousStatus || in_array($newStatus, $allowedTransitions[$previousStatus] ?? [], true), 422, 'Invalid order status transition.');

        if ($newStatus === 'cancelled') {
            $order->cancelAndRestoreInventory(auth()->id(), $request->input('reason'));

            return redirect()->back()->with('success', 'Order cancelled and inventory restored.');
        }

        $wasCompleted = $previousStatus === 'completed';
        $order->update([
            'status' => $newStatus,
            'tracking_number' => $request->input('tracking_number'),
            'shipped_at' => $newStatus === 'shipped' ? now() : $order->shipped_at,
            'completed_at' => $newStatus === 'completed' ? now() : $order->completed_at,
        ]);

        if ($previousStatus !== $newStatus) {
            $order->loadMissing('user');
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'changed_by' => auth()->id(),
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
            ]);
            $order->user?->notify(new OrderStatusUpdatedNotification($order->id, $previousStatus, $newStatus));
        }

        if (! $wasCompleted && $order->status === 'completed') {
            $order->loadMissing('items.product', 'user');

            $reviewedProductIds = ProductReview::query()
                ->where('user_id', $order->user_id)
                ->whereIn('product_id', $order->items->pluck('product_id'))
                ->pluck('product_id');

            $itemsToReview = $order->items
                ->filter(fn ($item) => $item->product && ! $reviewedProductIds->contains($item->product_id))
                ->unique('product_id')
                ->count();

            if ($itemsToReview > 0) {
                $order->user->notify(new ReviewReminderNotification($order->id, $itemsToReview));
            }
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}
