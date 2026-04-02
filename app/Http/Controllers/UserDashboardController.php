<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $uploadedProducts = $user->uploadedProducts()->latest()->take(6)->get();
        $orders = Order::with('items.product')->where('user_id', $user->id)->latest()->take(5)->get();
        $reviews = $user->reviews()->with('product')->latest()->take(5)->get();
        $reviewedProductIds = $user->reviews()->pluck('product_id');
        $pendingReviewItems = OrderItem::query()
            ->with(['order', 'product'])
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['confirmed', 'completed']);
            })
            ->latest()
            ->get()
            ->filter(function (OrderItem $item) use ($reviewedProductIds) {
                return $item->product && ! $reviewedProductIds->contains($item->product_id);
            })
            ->unique('product_id')
            ->take(5)
            ->values();
        $pointsTransactions = $user->pointsTransactions()->latest()->take(8)->get();
        $referralLinks = $user->referralLinks()->with('product')->latest()->get();

        return view('dashboard', [
            'uploadLimit' => $user->monthlyUploadLimit(),
            'uploadsUsed' => $user->monthlyProductUploadCount(),
            'uploadsRemaining' => $user->remainingMonthlyUploads(),
            'uploadedProducts' => $uploadedProducts,
            'orders' => $orders,
            'reviews' => $reviews,
            'pendingReviewItems' => $pendingReviewItems,
            'pointsBalance' => $user->points_balance,
            'pointsTransactions' => $pointsTransactions,
            'referralLinks' => $referralLinks,
        ]);
    }
}
