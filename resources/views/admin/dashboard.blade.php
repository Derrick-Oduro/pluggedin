<x-admin-layout>
                <x-page-header
                    badge="Admin"
                    title="Dashboard"
                    subtitle="Day-to-day marketplace operations at a glance."
                >
                    <x-slot name="actions">
                        <a href="{{ route('admin.products.create') }}" class="btn-primary">Add product</a>
                        <a href="{{ route('admin.products.pending') }}" class="btn-secondary">Review uploads</a>
                        <a href="{{ route('admin.reviews.index') }}" class="btn-secondary">Moderate reviews</a>
                        <a href="{{ route('admin.referrals.index') }}" class="btn-secondary">Referrals</a>
                    </x-slot>
                </x-page-header>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="backend-card"><p class="text-xs text-stone-500">Products</p><p class="text-xl font-semibold">{{ $stats['total_products'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Pending Products</p><p class="text-xl font-semibold">{{ $stats['pending_products'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Orders</p><p class="text-xl font-semibold">{{ $stats['total_orders'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Pending Orders</p><p class="text-xl font-semibold">{{ $stats['pending_orders'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Bookings</p><p class="text-xl font-semibold">{{ $stats['total_bookings'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Pending Bookings</p><p class="text-xl font-semibold">{{ $stats['pending_bookings'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Revenue</p><p class="text-xl font-semibold">GH₵{{ number_format($stats['total_revenue'], 2) }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Referral Conversions</p><p class="text-xl font-semibold">{{ $stats['total_referral_conversions'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Pending Reviews</p><p class="text-xl font-semibold">{{ $stats['pending_reviews'] }}</p></div>
                    <div class="backend-card"><p class="text-xs text-stone-500">Reported Reviews</p><p class="text-xl font-semibold">{{ $stats['reported_reviews'] }}</p></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="backend-card p-5">
                        <h2 class="text-lg font-extrabold text-stone-950 mb-3">Order Tracking Updates</h2>
                        <div class="space-y-2">
                            @forelse(($recentOrderStatusUpdates ?? collect()) as $update)
                                <div class="rounded-lg border border-stone-200 px-3 py-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold">Order #{{ $update->order_id }}: {{ ucfirst($update->to_status) }}</p>
                                        <span class="text-xs text-stone-500">{{ $update->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-stone-600">{{ $update->actor?->name ?? 'System' }} · {{ $update->order?->user?->name ?? 'Unknown customer' }}</p>
                                    <a href="{{ route('admin.orders.show', $update->order_id) }}" class="text-xs text-brand hover:text-brand-deep font-semibold">View Order</a>
                                </div>
                            @empty
                                <p class="text-sm text-stone-600">No order status updates yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="backend-card p-5">
                        <h2 class="text-lg font-extrabold text-stone-950 mb-3">Booking Tracking Updates</h2>
                        <div class="space-y-2">
                            @forelse(($recentBookingStatusUpdates ?? collect()) as $update)
                                <div class="rounded-lg border border-stone-200 px-3 py-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold">Booking #{{ $update->booking_id }}: {{ ucfirst($update->to_status) }}</p>
                                        <span class="text-xs text-stone-500">{{ $update->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-stone-600">{{ $update->actor?->name ?? 'System' }} · {{ $update->booking?->user?->name ?? 'Unknown customer' }}</p>
                                    <a href="{{ route('admin.bookings.show', $update->booking_id) }}" class="text-xs text-brand hover:text-brand-deep font-semibold">View Booking</a>
                                </div>
                            @empty
                                <p class="text-sm text-stone-600">No booking status updates yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="backend-card p-5">
                <h2 class="text-lg font-extrabold text-stone-950 mb-3">Recent Orders</h2>
                <div class="space-y-2">
                    @forelse($recentOrders as $order)
                        <div class="border-b border-stone-300 pb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">Order #{{ $order->id }}</p>
                                    <p class="text-xs text-stone-600">{{ $order->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-brand">GH₵{{ number_format($order->total_price, 2) }}</p>
                                    <x-status-badge :status="$order->status" />
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-stone-600">No orders yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.orders.index') }}" class="block text-center mt-3 text-sm text-brand hover:text-brand-deep">
                    View all orders
                </a>
            </div>

            <div class="backend-card p-5">
                <h2 class="text-lg font-extrabold text-stone-950 mb-3">Recent Bookings</h2>
                <div class="space-y-2">
                    @forelse($recentBookings as $booking)
                        <div class="border-b border-stone-300 pb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $booking->service->name }}</p>
                                    <p class="text-xs text-stone-600">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-stone-600">{{ $booking->preferred_date->format('M d, Y') }}</p>
                                </div>
                                <x-status-badge :status="$booking->status" />
                            </div>
                        </div>
                    @empty
                        <p class="text-stone-600">No bookings yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="block text-center mt-3 text-sm text-brand hover:text-brand-deep">
                    View all bookings
                </a>
            </div>
                </div>
</x-admin-layout>
