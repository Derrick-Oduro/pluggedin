<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Booking;
use App\Models\ReferralLink;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_price'),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_referral_links' => ReferralLink::count(),
            'total_referral_conversions' => ReferralLink::sum('conversions'),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentBookings = Booking::with(['user', 'service'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentBookings'));
    }
}
