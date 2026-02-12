<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Admin Dashboard</h1>
            @if(auth()->user()->hasRole('super-admin'))
                <a href="{{ route('superadmin.dashboard') }}" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                    🔐 Super Admin Panel
                </a>
            @endif
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-dark-secondary rounded-lg p-6">
                <h3 class="text-text-secondary text-sm mb-2">Total Products</h3>
                <p class="text-3xl font-bold text-orange">{{ $stats['total_products'] }}</p>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <h3 class="text-text-secondary text-sm mb-2">Total Orders</h3>
                <p class="text-3xl font-bold text-orange">{{ $stats['total_orders'] }}</p>
                <p class="text-sm text-text-secondary mt-1">{{ $stats['pending_orders'] }} pending</p>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <h3 class="text-text-secondary text-sm mb-2">Total Bookings</h3>
                <p class="text-3xl font-bold text-orange">{{ $stats['total_bookings'] }}</p>
                <p class="text-sm text-text-secondary mt-1">{{ $stats['pending_bookings'] }} pending</p>
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <h3 class="text-text-secondary text-sm mb-2">Total Revenue</h3>
                <p class="text-3xl font-bold text-orange">${{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <a href="{{ route('admin.products.create') }}" class="bg-orange hover:bg-orange-light text-white p-6 rounded-lg text-center font-semibold transition">
                + Add New Product
            </a>

            <a href="{{ route('admin.products.index') }}" class="bg-dark-secondary hover:bg-gray-800 p-6 rounded-lg text-center font-semibold transition">
                Manage Products
            </a>

            <a href="{{ route('admin.orders.index') }}" class="bg-dark-secondary hover:bg-gray-800 p-6 rounded-lg text-center font-semibold transition">
                View All Orders
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-dark-secondary rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Recent Orders</h2>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <div class="border-b border-gray-700 pb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">Order #{{ $order->id }}</p>
                                    <p class="text-sm text-text-secondary">{{ $order->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-orange">${{ number_format($order->total_price, 2) }}</p>
                                    <span class="text-xs px-2 py-1 rounded
                                        @if($order->status === 'pending') bg-yellow-500/20 text-yellow-500
                                        @elseif($order->status === 'confirmed') bg-blue-500/20 text-blue-500
                                        @elseif($order->status === 'completed') bg-green-500/20 text-green-500
                                        @else bg-red-500/20 text-red-500
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-text-secondary">No orders yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.orders.index') }}" class="block text-center mt-4 text-orange hover:text-orange-light">
                    View All Orders →
                </a>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-dark-secondary rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Recent Bookings</h2>
                <div class="space-y-3">
                    @forelse($recentBookings as $booking)
                        <div class="border-b border-gray-700 pb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $booking->service->name }}</p>
                                    <p class="text-sm text-text-secondary">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-text-secondary">{{ $booking->preferred_date->format('M d, Y') }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded
                                    @if($booking->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($booking->status === 'approved') bg-blue-500/20 text-blue-500
                                    @elseif($booking->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-text-secondary">No bookings yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="block text-center mt-4 text-orange hover:text-orange-light">
                    View All Bookings →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
