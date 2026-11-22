<?php

namespace App\Http\Controllers;

use App\Models\BookingStatusHistory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $orders = $user->orders()->with('items.product')->latest()->take(5)->get();
        $openOrders = $user->orders()->whereNotIn('status', ['completed', 'cancelled', 'refunded']);

        // Things the buyer has to act on, shown at the top.
        $unpaidOrders = $user->orders()->where('status', 'pending')->where('payment_method', Order::PAYSTACK)->latest()->get();
        $cashDueOrders = $user->orders()
            ->where('payment_method', Order::CASH_ON_DELIVERY)
            ->whereNull('paid_at')
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->latest()
            ->get();

        $reviewedProductIds = $user->reviews()->pluck('product_id');
        $pendingReviewItems = OrderItem::query()
            ->with(['order', 'product'])
            ->whereHas('order', fn ($query) => $query->where('user_id', $user->id)->whereIn('status', ['confirmed', 'completed']))
            ->latest()
            ->get()
            ->filter(fn (OrderItem $item) => $item->product && ! $reviewedProductIds->contains($item->product_id))
            ->unique('product_id')
            ->take(4)
            ->values();

        $uploadedProducts = $user->uploadedProducts()->latest()->take(4)->get();
        $rejectedUploads = $user->uploadedProducts()->where('status', 'rejected')->latest()->take(3)->get();

        // One feed for order and booking updates, newest first.
        $activity = OrderStatusHistory::query()
            ->with('order')
            ->whereHas('order', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($update) => [
                'kind' => 'Order',
                'label' => 'Order #'.$update->order_id,
                'status' => $update->to_status,
                'url' => route('orders.show', $update->order_id),
                'at' => $update->created_at,
            ])
            ->concat(
                BookingStatusHistory::query()
                    ->whereHas('booking', fn ($query) => $query->where('user_id', $user->id))
                    ->latest()
                    ->take(8)
                    ->get()
                    ->map(fn ($update) => [
                        'kind' => 'Repair',
                        'label' => 'Booking #'.$update->booking_id,
                        'status' => $update->to_status,
                        'url' => route('bookings.show', $update->booking_id),
                        'at' => $update->created_at,
                    ])
            )
            ->sortByDesc('at')
            ->take(6)
            ->values();

        return view('dashboard', [
            'firstName' => \Illuminate\Support\Str::before($user->name, ' '),
            'orders' => $orders,
            'openOrdersCount' => (clone $openOrders)->count(),
            'unpaidOrders' => $unpaidOrders,
            'cashDueOrders' => $cashDueOrders,
            'pendingReviewItems' => $pendingReviewItems,
            'savedCount' => $user->wishlistItems()->count(),
            'cartCount' => $user->cartItems()->count(),
            'unreadCount' => $user->unreadNotifications()->count(),
            'activeBookingsCount' => $user->bookings()->whereNotIn('status', ['completed', 'cancelled', 'rejected'])->count(),
            'uploadLimit' => $user->monthlyUploadLimit(),
            'uploadsUsed' => $user->monthlyProductUploadCount(),
            'uploadsRemaining' => $user->remainingMonthlyUploads(),
            'uploadedProducts' => $uploadedProducts,
            'pendingUploadsCount' => $user->uploadedProducts()->where('status', 'pending')->count(),
            'liveUploadsCount' => $user->uploadedProducts()->where('status', 'approved')->count(),
            'rejectedUploads' => $rejectedUploads,
            'pointsBalance' => (int) $user->points_balance,
            'pointsThreshold' => (int) config('marketplace.redeemable_points_threshold', 100),
            'pointsTransactions' => $user->pointsTransactions()->latest()->take(4)->get(),
            'referralLinks' => $user->referralLinks()->with('product')->orderByDesc('clicks')->take(3)->get(),
            'activity' => $activity,
        ]);
    }
}
