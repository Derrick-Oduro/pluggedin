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
use Carbon\Carbon;

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

        $months = collect(range(5, 0))->reverse()->map(function ($offset) {
            $date = now()->subMonths($offset);
            return [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
            ];
        })->values();

        $periodStart = Carbon::parse($months->first()['key'].'-01')->startOfMonth();

        $ordersInPeriod = Order::query()
            ->where('created_at', '>=', $periodStart)
            ->get(['id', 'status', 'total_price', 'created_at']);

        $ordersByMonth = $ordersInPeriod->groupBy(fn ($order) => $order->created_at->format('Y-m'));

        $monthlyOrderCounts = $months->map(fn ($month) => ($ordersByMonth->get($month['key']) ?? collect())->count())->values();
        $monthlyRevenue = $months->map(function ($month) use ($ordersByMonth) {
            return ($ordersByMonth->get($month['key']) ?? collect())
                ->where('status', 'completed')
                ->sum('total_price');
        })->values();

        $statusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        $userRoleCounts = [
            'users' => User::role('user')->count(),
            'admins' => User::role('admin')->count(),
            'super_admins' => User::role('super-admin')->count(),
        ];

        $chartData = [
            'labels' => $months->pluck('label')->values(),
            'monthly_orders' => $monthlyOrderCounts,
            'monthly_revenue' => $monthlyRevenue,
            'order_status' => $statusCounts,
            'user_roles' => $userRoleCounts,
        ];

        return view('superadmin.dashboard', compact('stats', 'recent_users', 'recent_orders', 'recent_pending_uploads', 'recent_audit_logs', 'chartData'));
    }

    public function auditLogs()
    {
        $logs = AuditLog::with('user')->latest()->paginate(25);

        return view('superadmin.audit-logs.index', compact('logs'));
    }
}
