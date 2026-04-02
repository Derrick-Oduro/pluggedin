@section('page-title', 'Super Admin Dashboard')

<x-superadmin-layout>
    <div class="space-y-5">
            <x-page-header
                title="Super Admin Control Panel"
                subtitle="Compact overview of system health and latest operations."
                :breadcrumbs="[
                    ['label' => 'Dashboard'],
                ]"
            />

            <div class="backend-card p-4">
                <h2 class="text-lg font-semibold mb-3">Quick Actions</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                    <a href="{{ route('superadmin.users.index') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">User Management</p>
                        <p class="text-base font-semibold mt-1">{{ $stats['total_users'] }} accounts</p>
                    </a>
                    <a href="{{ route('admin.products.pending') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Moderation Queue</p>
                        <p class="text-base font-semibold mt-1">{{ $stats['pending_user_uploads'] }} pending uploads</p>
                    </a>
                    <a href="{{ route('superadmin.marketing.index', ['scope' => 'live']) }}#campaigns-manage" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Marketing</p>
                        <p class="text-base font-semibold mt-1">{{ $stats['active_discount_campaigns'] }} live campaigns</p>
                    </a>
                    <a href="{{ route('notifications.index', ['scope' => 'unread']) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Notifications</p>
                        <p class="text-base font-semibold mt-1">{{ auth()->user()->unreadNotifications()->count() }} unread</p>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Users</p><p class="text-xl font-semibold">{{ $stats['total_users'] }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Admins</p><p class="text-xl font-semibold">{{ $stats['total_admins'] }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Products</p><p class="text-xl font-semibold">{{ $stats['total_products'] }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Orders</p><p class="text-xl font-semibold">{{ $stats['total_orders'] }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Bookings</p><p class="text-xl font-semibold">{{ $stats['total_bookings'] }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Revenue</p><p class="text-xl font-semibold">${{ number_format($stats['total_revenue'], 2) }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Pending uploads</p><p class="text-xl font-semibold">{{ $stats['pending_user_uploads'] }}</p></div>
                <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Active discounts</p><p class="text-xl font-semibold">{{ $stats['active_discount_campaigns'] }}</p></div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
                <div class="xl:col-span-8 backend-card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-semibold">Orders and Revenue Trend</h2>
                        <span class="text-xs text-gray-500 dark:text-text-secondary">Last 6 months</span>
                    </div>
                    <div class="h-72">
                        <canvas id="superadminTrendChart"></canvas>
                    </div>
                </div>

                <div class="xl:col-span-4 backend-card p-5">
                    <h2 class="text-lg font-semibold mb-3">Order Status Distribution</h2>
                    <div class="h-72">
                        <canvas id="superadminStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
                <div class="xl:col-span-5 backend-card p-5">
                    <h2 class="text-lg font-semibold mb-3">User Role Split</h2>
                    <div class="h-64">
                        <canvas id="superadminRoleChart"></canvas>
                    </div>
                </div>

                <div class="xl:col-span-7 backend-card p-5">
                    <h2 class="text-lg font-semibold mb-3">Recent Audits</h2>
                    <div class="space-y-2">
                        @forelse(($recent_audit_logs ?? collect())->take(6) as $audit)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs">
                                <p class="font-semibold">{{ str_replace('.', ' • ', $audit->action) }}</p>
                                <p class="text-gray-500 dark:text-text-secondary">{{ $audit->created_at->format('M d, H:i') }} · {{ $audit->user?->name ?? 'System' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-text-secondary">No audit activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Recent Pending Uploads</h2>
            <div class="space-y-3">
                @forelse($recent_pending_uploads as $upload)
                    <div class="flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2">
                        <div>
                            <p class="font-semibold">{{ $upload->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-text-secondary">By {{ $upload->uploader?->name ?? 'N/A' }}</p>
                        </div>
                        <a href="{{ route('admin.products.pending') }}" class="text-orange hover:text-orange-light text-xs font-semibold">Review</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-600 dark:text-text-secondary">No pending uploads right now.</p>
                @endforelse
            </div>
            </div>

            <div class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Recent Users</h2>
                <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="text-left p-2 font-semibold">Name</th>
                        <th class="text-left p-2 font-semibold">Email</th>
                        <th class="text-left p-2 font-semibold">Role</th>
                        <th class="text-left p-2 font-semibold">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_users as $user)
                        <tr class="border-b border-gray-300 dark:border-gray-700">
                            <td class="p-2">{{ $user->name }}</td>
                            <td class="p-2 text-gray-600 dark:text-text-secondary">{{ $user->email }}</td>
                            <td class="p-2">
                                @if($user->hasRole('super-admin'))
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-500/20 text-red-500">Super Admin</span>
                                @elseif($user->hasRole('admin'))
                                    <span class="text-xs px-2 py-1 rounded-full bg-orange/20 text-orange">Admin</span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-500">User</span>
                                @endif
                            </td>
                            <td class="p-2 text-gray-600 dark:text-text-secondary text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
                </div>
        </div>

            <div class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Recent Orders</h2>
                <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="text-left p-2 font-semibold">Order #</th>
                        <th class="text-left p-2 font-semibold">Customer</th>
                        <th class="text-left p-2 font-semibold">Total</th>
                        <th class="text-left p-2 font-semibold">Status</th>
                        <th class="text-left p-2 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_orders as $order)
                        <tr class="border-b border-gray-300 dark:border-gray-700">
                            <td class="p-2">#{{ $order->id }}</td>
                            <td class="p-2">{{ $order->user->name }}</td>
                            <td class="p-2 text-orange font-semibold">${{ number_format($order->total_price, 2) }}</td>
                            <td class="p-2">
                                <span class="text-xs px-2 py-1 rounded-full
                                    @if($order->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($order->status === 'confirmed') bg-blue-500/20 text-blue-500
                                    @elseif($order->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="p-2 text-gray-600 dark:text-text-secondary text-sm">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
                </div>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($chartData ?? []);

        const sharedGrid = {
            color: 'rgba(148, 163, 184, 0.25)',
            drawBorder: false,
        };

        const trendCtx = document.getElementById('superadminTrendChart');
        if (trendCtx && chartData.labels) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Orders',
                            data: chartData.monthly_orders,
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.16)',
                            tension: 0.35,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Revenue',
                            data: chartData.monthly_revenue,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.14)',
                            tension: 0.35,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, grid: sharedGrid },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
                        x: { grid: sharedGrid }
                    }
                }
            });
        }

        const statusCtx = document.getElementById('superadminStatusChart');
        if (statusCtx && chartData.order_status) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                    datasets: [{
                        data: [
                            chartData.order_status.pending || 0,
                            chartData.order_status.confirmed || 0,
                            chartData.order_status.completed || 0,
                            chartData.order_status.cancelled || 0,
                        ],
                        backgroundColor: ['#facc15', '#38bdf8', '#22c55e', '#ef4444'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        const roleCtx = document.getElementById('superadminRoleChart');
        if (roleCtx && chartData.user_roles) {
            new Chart(roleCtx, {
                type: 'bar',
                data: {
                    labels: ['Users', 'Admins', 'Super Admins'],
                    datasets: [{
                        label: 'Accounts',
                        data: [
                            chartData.user_roles.users || 0,
                            chartData.user_roles.admins || 0,
                            chartData.user_roles.super_admins || 0,
                        ],
                        backgroundColor: ['#fb923c', '#0ea5e9', '#ef4444'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: sharedGrid },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    </script>
</x-superadmin-layout>
