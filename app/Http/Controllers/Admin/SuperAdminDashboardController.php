<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\DiscountCampaign;
use App\Models\HeroSlide;
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
            'pending_user_uploads' => Product::where('is_user_uploaded', true)->where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_price'),
            'active_discount_campaigns' => DiscountCampaign::active()->count(),
            'carousel_slides' => HeroSlide::count(),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_orders = Order::with('user')->latest()->take(5)->get();
        $recent_pending_uploads = Product::with('uploader')
            ->where('is_user_uploaded', true)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        $recent_audit_logs = AuditLog::with('user')->latest()->take(8)->get();

        return view('superadmin.dashboard', compact('stats', 'recent_users', 'recent_orders', 'recent_pending_uploads', 'recent_audit_logs'));
    }

    public function auditLogs()
    {
        $logs = AuditLog::with('user')->latest()->paginate(25);

        return view('superadmin.audit-logs.index', compact('logs'));
    }
}
