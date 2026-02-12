@section('page-title', 'Super Admin Dashboard')

<x-superadmin-layout>
    <div class="space-y-8">
        <!-- Header with Quick Actions -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-4xl font-bold mb-2">🔐 Super Admin Control Panel</h1>
                <p class="text-text-secondary">Complete system administration and management</p>
            </div>
        </div>

        <!-- Primary Quick Actions - Highlighted -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('superadmin.users.index') }}" class="bg-gradient-to-br from-red-500/20 to-red-600/10 border-2 border-red-500/30 hover:border-red-500 rounded-lg p-8 transition group">
                <div class="flex items-center gap-4 mb-3">
                    <div class="text-5xl">👥</div>
                    <h3 class="text-2xl font-bold group-hover:text-red-400">User Management</h3>
                </div>
                <p class="text-text-secondary">Create, edit, and manage all user accounts and permissions</p>
                <div class="mt-4 text-red-500 font-semibold">→ Manage Users</div>
            </a>

            <a href="{{ route('superadmin.categories.index') }}" class="bg-gradient-to-br from-orange/20 to-orange/10 border-2 border-orange/30 hover:border-orange rounded-lg p-8 transition group">
                <div class="flex items-center gap-4 mb-3">
                    <div class="text-5xl">🏷️</div>
                    <h3 class="text-2xl font-bold group-hover:text-orange-light">Category Management</h3>
                </div>
                <p class="text-text-secondary">Create and organize product categories across the platform</p>
                <div class="mt-4 text-orange font-semibold">→ Manage Categories</div>
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-secondary text-sm mb-1">Total Users</p>
                        <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="text-4xl">👥</div>
                </div>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-secondary text-sm mb-1">Total Admins</p>
                        <p class="text-3xl font-bold text-orange">{{ $stats['total_admins'] }}</p>
                    </div>
                    <div class="text-4xl">🛡️</div>
                </div>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-secondary text-sm mb-1">Total Products</p>
                        <p class="text-3xl font-bold text-blue-500">{{ $stats['total_products'] }}</p>
                    </div>
                    <div class="text-4xl">📦</div>
                </div>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-secondary text-sm mb-1">Total Orders</p>
                        <p class="text-3xl font-bold text-green-500">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="text-4xl">🛒</div>
                </div>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-secondary text-sm mb-1">Total Bookings</p>
                        <p class="text-3xl font-bold text-purple-500">{{ $stats['total_bookings'] }}</p>
                    </div>
                    <div class="text-4xl">📅</div>
                </div>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-secondary text-sm mb-1">Total Revenue</p>
                        <p class="text-3xl font-bold text-orange">${{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </div>
        </div>

        <!-- Secondary Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.dashboard') }}" class="bg-dark-secondary hover:bg-gray-800 border border-gray-700 rounded-lg p-6 transition flex items-center gap-4">
                <div class="text-3xl">📊</div>
                <div>
                    <h3 class="text-lg font-bold">Admin Dashboard</h3>
                    <p class="text-sm text-text-secondary">Product, order & booking management</p>
                </div>
            </a>

            <a href="{{ route('admin.products.index') }}" class="bg-dark-secondary hover:bg-gray-800 border border-gray-700 rounded-lg p-6 transition flex items-center gap-4">
                <div class="text-3xl">📦</div>
                <div>
                    <h3 class="text-lg font-bold">Product Management</h3>
                    <p class="text-sm text-text-secondary">View and manage inventory</p>
                </div>
            </a>
        </div>

        <!-- Recent Users -->
        <div class="bg-dark-secondary rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Recent Users</h2>
            <table class="w-full">
                <thead class="border-b border-gray-700">
                    <tr>
                        <th class="text-left p-2">Name</th>
                        <th class="text-left p-2">Email</th>
                        <th class="text-left p-2">Role</th>
                        <th class="text-left p-2">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_users as $user)
                        <tr class="border-b border-gray-700">
                            <td class="p-2">{{ $user->name }}</td>
                            <td class="p-2 text-text-secondary">{{ $user->email }}</td>
                            <td class="p-2">
                                @if($user->hasRole('super-admin'))
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-500/20 text-red-500">Super Admin</span>
                                @elseif($user->hasRole('admin'))
                                    <span class="text-xs px-2 py-1 rounded-full bg-orange/20 text-orange">Admin</span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-500">User</span>
                                @endif
                            </td>
                            <td class="p-2 text-text-secondary text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Recent Orders -->
        <div class="bg-dark-secondary rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Recent Orders</h2>
            <table class="w-full">
                <thead class="border-b border-gray-700">
                    <tr>
                        <th class="text-left p-2">Order #</th>
                        <th class="text-left p-2">Customer</th>
                        <th class="text-left p-2">Total</th>
                        <th class="text-left p-2">Status</th>
                        <th class="text-left p-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_orders as $order)
                        <tr class="border-b border-gray-700">
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
                            <td class="p-2 text-text-secondary text-sm">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-superadmin-layout>
