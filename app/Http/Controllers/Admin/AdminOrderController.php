<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductReview;
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
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        $wasCompleted = $order->status === 'completed';
        $order->update(['status' => $request->status]);

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
