<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\PaymentTransaction;
use App\Services\PaystackPayments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    /** Record the money for a pay-on-delivery order, collected in cash or Mobile Money at handover. */
    public function markCashReceived(Request $request, Order $order, PaystackPayments $payments)
    {
        $request->validate([
            'collection_method' => 'required|in:cash,mobile_money',
            'collection_reference' => 'nullable|string|max:120',
        ]);

        $recorded = DB::transaction(function () use ($request, $order, $payments) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (! $order->awaitingCash()) {
                return false;
            }

            PaymentTransaction::create([
                'order_id' => $order->id,
                'provider' => $request->collection_method,
                'reference' => 'COD-'.$order->id.'-'.Str::upper(Str::random(10)),
                'status' => 'paid',
                'amount' => $order->total_price,
                'currency' => config('services.paystack.currency', 'GHS'),
                'gateway_response' => [
                    'recorded_by' => auth()->id(),
                    'collection_reference' => $request->input('collection_reference'),
                ],
                'paid_at' => now(),
            ]);

            $order->update(['paid_at' => now()]);
            $payments->awardReferralPoints($order);

            return true;
        });

        return $recorded
            ? redirect()->back()->with('success', 'Payment recorded. You can now mark the order as completed.')
            : redirect()->back()->with('error', 'This order is not waiting for a pay-on-delivery payment.');
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

        if ($newStatus === 'completed' && $order->awaitingCash()) {
            return redirect()->back()->with('error', 'This is a pay-on-delivery order. Record the payment with "Cash received" before completing it.');
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
