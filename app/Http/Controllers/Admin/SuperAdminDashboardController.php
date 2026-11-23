<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\SuperAdminQueues;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $queues = SuperAdminQueues::counts();

        // This month vs last month. "Money in" is what was actually paid (online or cash on delivery).
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonthNoOverflow()->startOfMonth();
        $sameDayLastMonth = now()->subMonthNoOverflow(); // compare like for like: 1st to today

        $kpis = [
            'money_in' => [
                'label' => 'Money in',
                'now' => (float) Order::whereBetween('paid_at', [$thisMonth, now()])->sum('total_price'),
                'before' => (float) Order::whereBetween('paid_at', [$lastMonth, $sameDayLastMonth])->sum('total_price'),
                'money' => true,
            ],
            'orders' => [
                'label' => 'Orders placed',
                'now' => Order::whereBetween('created_at', [$thisMonth, now()])->count(),
                'before' => Order::whereBetween('created_at', [$lastMonth, $sameDayLastMonth])->count(),
            ],
            'users' => [
                'label' => 'New members',
                'now' => User::whereBetween('created_at', [$thisMonth, now()])->count(),
                'before' => User::whereBetween('created_at', [$lastMonth, $sameDayLastMonth])->count(),
            ],
            'listings' => [
                'label' => 'New listings',
                'now' => Product::whereBetween('created_at', [$thisMonth, now()])->count(),
                'before' => Product::whereBetween('created_at', [$lastMonth, $sameDayLastMonth])->count(),
            ],
        ];

        $totals = [
            'members' => User::count(),
            'live_listings' => Product::where('status', 'approved')->where('listing_state', 'active')->count(),
            'open_orders' => Order::whereNotIn('status', ['completed', 'cancelled', 'refunded'])->count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $recentAudits = AuditLog::with('user')->latest()->take(6)->get();

        // Six-month trend for the chart.
        $months = collect(range(5, 0))->map(fn ($offset) => now()->startOfMonth()->subMonthsNoOverflow($offset));
        $periodStart = $months->first();
        $placed = Order::where('created_at', '>=', $periodStart)->get(['created_at'])
            ->countBy(fn ($order) => $order->created_at->format('Y-m'));
        $paid = Order::where('paid_at', '>=', $periodStart)->get(['paid_at', 'total_price'])
            ->groupBy(fn ($order) => $order->paid_at->format('Y-m'))
            ->map(fn ($group) => round((float) $group->sum('total_price'), 2));

        $chartData = [
            'labels' => $months->map(fn (Carbon $m) => $m->format('M'))->values(),
            'orders' => $months->map(fn (Carbon $m) => $placed->get($m->format('Y-m'), 0))->values(),
            'money' => $months->map(fn (Carbon $m) => $paid->get($m->format('Y-m'), 0))->values(),
            'status' => Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ];

        return view('superadmin.dashboard', compact('queues', 'kpis', 'totals', 'recentOrders', 'recentAudits', 'chartData'));
    }

    public function auditLogs(Request $request)
    {
        $area = (string) $request->query('area', '');
        $actorId = $request->integer('actor') ?: null;

        $logs = AuditLog::with('user')
            ->when($area !== '', fn ($query) => $query->where(fn ($q) => $q->where('action', 'like', $area.'.%')->orWhere('action', 'like', '%.'.$area.'.%')))
            ->when($actorId, fn ($query) => $query->where('user_id', $actorId))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        // Filter options built from what's actually in the log.
        $areas = AuditLog::query()->distinct()->pluck('action')
            ->map(fn ($action) => explode('.', $action)[1] ?? explode('.', $action)[0])
            ->unique()->sort()->values();
        $actors = User::whereIn('id', AuditLog::query()->distinct()->whereNotNull('user_id')->pluck('user_id'))->orderBy('name')->get(['id', 'name']);

        return view('superadmin.audit-logs.index', compact('logs', 'areas', 'actors', 'area', 'actorId'));
    }
}
