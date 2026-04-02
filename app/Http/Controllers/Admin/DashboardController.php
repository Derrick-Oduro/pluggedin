<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointsTransaction;
use App\Models\Product;
use App\Models\ProductReview;
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
            'pending_reviews' => ProductReview::where('moderation_status', 'pending')->count(),
            'reported_reviews' => ProductReview::where('is_reported', true)->count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentBookings = Booking::with(['user', 'service'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentBookings'));
    }

    public function referrals()
    {
        $summary = [
            'links' => ReferralLink::count(),
            'clicks' => (int) ReferralLink::sum('clicks'),
            'conversions' => (int) ReferralLink::sum('conversions'),
            'points_awarded' => (int) PointsTransaction::where('type', 'referral_purchase')->sum('points'),
        ];

        $topLinks = ReferralLink::with(['user', 'product'])
            ->orderByDesc('conversions')
            ->orderByDesc('clicks')
            ->take(10)
            ->get();

        $topReferrers = User::role('user')
            ->withSum('referralLinks', 'conversions')
            ->orderByDesc('referral_links_sum_conversions')
            ->take(10)
            ->get();

        return view('admin.referrals.index', compact('summary', 'topLinks', 'topReferrers'));
    }
}
