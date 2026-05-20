<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="backend-sidebar sticky top-24">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.products.pending') }}" class="backend-tab">Moderation Queue</a>
                        <a href="{{ route('admin.reviews.index') }}" class="backend-tab">Review Moderation</a>
                        <a href="{{ route('admin.products.index') }}" class="backend-tab">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="backend-tab">Orders</a>
                        <a href="{{ route('admin.bookings.index') }}" class="backend-tab">Bookings</a>
                        <a href="{{ route('admin.referrals.index') }}" class="backend-tab">Referral Analytics</a>
                        @if(auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('superadmin.dashboard') }}" class="backend-tab text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">Super Admin Panel</a>
                        @endif
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-5">
                <x-page-header
                    title="Admin Dashboard"
                    subtitle="Compact operations panel for day-to-day management."
                >
                    <x-slot name="actions">
                        <a href="{{ route('admin.products.create') }}" class="backend-btn-primary">Add Product</a>
                        <a href="{{ route('admin.products.pending') }}" class="btn-secondary">Review Uploads</a>
                        <a href="{{ route('admin.reviews.index') }}" class="btn-secondary">Moderate Reviews</a>
                        <a href="{{ route('admin.referrals.index') }}" class="btn-secondary">Referrals</a>
                    </x-slot>
                </x-page-header>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Products</p><p class="text-xl font-semibold">{{ $stats['total_products'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Pending Products</p><p class="text-xl font-semibold">{{ $stats['pending_products'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Orders</p><p class="text-xl font-semibold">{{ $stats['total_orders'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Pending Orders</p><p class="text-xl font-semibold">{{ $stats['pending_orders'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Bookings</p><p class="text-xl font-semibold">{{ $stats['total_bookings'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Pending Bookings</p><p class="text-xl font-semibold">{{ $stats['pending_bookings'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Revenue</p><p class="text-xl font-semibold">${{ number_format($stats['total_revenue'], 2) }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Referral Conversions</p><p class="text-xl font-semibold">{{ $stats['total_referral_conversions'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Pending Reviews</p><p class="text-xl font-semibold">{{ $stats['pending_reviews'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-gray-500 dark:text-text-secondary">Reported Reviews</p><p class="text-xl font-semibold">{{ $stats['reported_reviews'] }}</p></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="backend-card p-5">
                        <h2 class="text-lg font-semibold mb-3">Order Tracking Updates</h2>
                        <div class="space-y-2">
                            @forelse(($recentOrderStatusUpdates ?? collect()) as $update)
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold">Order #{{ $update->order_id }}: {{ ucfirst($update->to_status) }}</p>
                                        <span class="text-xs text-gray-500 dark:text-text-secondary">{{ $update->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-text-secondary">{{ $update->actor?->name ?? 'System' }} · {{ $update->order?->user?->name ?? 'Unknown customer' }}</p>
                                    <a href="{{ route('admin.orders.show', $update->order_id) }}" class="text-xs text-orange hover:text-orange-light font-semibold">View Order</a>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600 dark:text-text-secondary">No order status updates yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="backend-card p-5">
                        <h2 class="text-lg font-semibold mb-3">Booking Tracking Updates</h2>
                        <div class="space-y-2">
                            @forelse(($recentBookingStatusUpdates ?? collect()) as $update)
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold">Booking #{{ $update->booking_id }}: {{ ucfirst($update->to_status) }}</p>
                                        <span class="text-xs text-gray-500 dark:text-text-secondary">{{ $update->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-text-secondary">{{ $update->actor?->name ?? 'System' }} · {{ $update->booking?->user?->name ?? 'Unknown customer' }}</p>
                                    <a href="{{ route('admin.bookings.show', $update->booking_id) }}" class="text-xs text-orange hover:text-orange-light font-semibold">View Booking</a>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600 dark:text-text-secondary">No booking status updates yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Recent Orders</h2>
                <div class="space-y-2">
                    @forelse($recentOrders as $order)
                        <div class="border-b border-gray-300 dark:border-gray-700 pb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">Order #{{ $order->id }}</p>
                                    <p class="text-xs text-gray-600 dark:text-text-secondary">{{ $order->user->name }}</p>
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
                        <p class="text-gray-600 dark:text-text-secondary">No orders yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.orders.index') }}" class="block text-center mt-3 text-sm text-orange hover:text-orange-light">
                    View all orders
                </a>
            </div>

            <div class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Recent Bookings</h2>
                <div class="space-y-2">
                    @forelse($recentBookings as $booking)
                        <div class="border-b border-gray-300 dark:border-gray-700 pb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $booking->service->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-text-secondary">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-text-secondary">{{ $booking->preferred_date->format('M d, Y') }}</p>
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
                        <p class="text-gray-600 dark:text-text-secondary">No bookings yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="block text-center mt-3 text-sm text-orange hover:text-orange-light">
                    View all bookings
                </a>
            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
