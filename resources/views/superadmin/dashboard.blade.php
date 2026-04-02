@section('page-title', 'Super Admin Dashboard')

<x-superadmin-layout>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <aside class="xl:col-span-3">
            <div class="backend-sidebar sticky top-24">
                <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Control Tabs</p>
                <nav class="space-y-1">
                    <a href="{{ route('superadmin.users.index') }}" class="backend-tab">User Management</a>
                    <a href="{{ route('superadmin.categories.index') }}" class="backend-tab">Category Management</a>
                    <a href="{{ route('superadmin.marketing.index') }}" class="backend-tab">Homepage and Promotions</a>
                    <a href="{{ route('superadmin.audit-logs.index') }}" class="backend-tab">Audit Logs</a>
                    <a href="{{ route('admin.products.pending') }}" class="backend-tab">Moderation Queue</a>
                    <a href="{{ route('admin.dashboard') }}" class="backend-tab">Admin Dashboard</a>
                </nav>

                <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Key Metrics</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between"><span class="text-gray-600 dark:text-text-secondary">Pending uploads</span><span class="font-semibold">{{ $stats['pending_user_uploads'] }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-600 dark:text-text-secondary">Active discounts</span><span class="font-semibold">{{ $stats['active_discount_campaigns'] }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-600 dark:text-text-secondary">Carousel slides</span><span class="font-semibold">{{ $stats['carousel_slides'] }}</span></div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Recent Audits</p>
                    <div class="space-y-2">
                        @forelse(($recent_audit_logs ?? collect())->take(4) as $audit)
                            <div class="text-xs">
                                <p class="font-semibold">{{ str_replace('.', ' • ', $audit->action) }}</p>
                                <p class="text-gray-500 dark:text-text-secondary">{{ $audit->created_at->format('M d, H:i') }} · {{ $audit->user?->name ?? 'System' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 dark:text-text-secondary">No audit activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </aside>

        <div class="xl:col-span-9 space-y-5">
            <div>
                <h1 class="text-2xl font-bold">Super Admin Control Panel</h1>
                <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Compact overview of system health and latest operations.</p>
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
    </div>
</x-superadmin-layout>
