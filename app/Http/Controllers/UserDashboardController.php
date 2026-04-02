<?php

namespace App\Http\Controllers;

use App\Models\Order;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $uploadedProducts = $user->uploadedProducts()->latest()->take(6)->get();
        $orders = Order::with('items.product')->where('user_id', $user->id)->latest()->take(5)->get();
        $reviews = $user->reviews()->with('product')->latest()->take(5)->get();
        $pointsTransactions = $user->pointsTransactions()->latest()->take(8)->get();
        $referralLinks = $user->referralLinks()->with('product')->latest()->get();

        return view('dashboard', [
            'uploadLimit' => $user->monthlyUploadLimit(),
            'uploadsUsed' => $user->monthlyProductUploadCount(),
            'uploadsRemaining' => $user->remainingMonthlyUploads(),
            'uploadedProducts' => $uploadedProducts,
            'orders' => $orders,
            'reviews' => $reviews,
            'pointsBalance' => $user->points_balance,
            'pointsTransactions' => $pointsTransactions,
            'referralLinks' => $referralLinks,
        ]);
    }
}
