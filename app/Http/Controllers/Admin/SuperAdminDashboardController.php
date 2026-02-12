<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::role(['admin', 'super-admin'])->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_price'),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_orders = Order::with('user')->latest()->take(5)->get();

        return view('superadmin.dashboard', compact('stats', 'recent_users', 'recent_orders'));
    }
}
