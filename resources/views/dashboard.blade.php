<x-app-layout>
    <section class="page-shell">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold">My Dashboard</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-1">Track uploads, purchases, points, and referrals.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('products.upload.create') }}" class="bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                    Upload Product
                </a>
                <a href="{{ route('products.upload.index') }}" class="btn-secondary px-6 py-3">
                    Manage Uploads
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="glass-panel rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Upload Limit</p>
                <p class="text-2xl font-bold">{{ $uploadsUsed ?? 0 }}/{{ $uploadLimit ?? 0 }}</p>
            </div>
            <div class="glass-panel rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Uploads Remaining</p>
                <p class="text-2xl font-bold text-orange">{{ $uploadsRemaining ?? 0 }}</p>
            </div>
            <div class="glass-panel rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Points Balance</p>
                <p class="text-2xl font-bold text-orange">{{ number_format($pointsBalance ?? 0) }}</p>
            </div>
            <div class="glass-panel rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Total Reviews</p>
                <p class="text-2xl font-bold">{{ ($reviews ?? collect())->count() }}</p>
            </div>
        </div>

        <div class="glass-panel rounded-xl p-5 mb-10">
            <h2 class="text-xl font-bold mb-3">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('products.upload.index', ['status' => 'pending']) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                    <p class="text-xs text-gray-600 dark:text-text-secondary">Uploads</p>
                    <p class="font-semibold mt-1">{{ $pendingUploadsCount ?? 0 }} pending uploads</p>
                </a>
                <a href="{{ route('orders.index') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                    <p class="text-xs text-gray-600 dark:text-text-secondary">Orders</p>
                    <p class="font-semibold mt-1">Track Purchases</p>
                </a>
                <a href="{{ route('bookings.index') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                    <p class="text-xs text-gray-600 dark:text-text-secondary">Bookings</p>
                    <p class="font-semibold mt-1">View Service Bookings</p>
                </a>
                <a href="{{ route('notifications.index', ['scope' => 'unread']) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3 hover:border-orange transition">
                    <p class="text-xs text-gray-600 dark:text-text-secondary">Notifications</p>
                    <p class="font-semibold mt-1">{{ auth()->user()->unreadNotifications()->count() }} unread</p>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="glass-panel rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Order Tracking Updates</h2>
                <div class="space-y-3">
                    @forelse(($recentOrderStatusUpdates ?? collect()) as $update)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold">Order #{{ $update->order_id }}: {{ ucfirst($update->to_status) }}</p>
                                <span class="text-xs text-gray-500 dark:text-text-secondary">{{ $update->created_at->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Updated by {{ $update->actor?->name ?? 'System' }}</p>
                            <a href="{{ route('orders.show', $update->order_id) }}" class="inline-flex mt-2 text-sm font-semibold text-orange hover:text-orange-light">View Order</a>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No order tracking updates yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-panel rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Booking Tracking Updates</h2>
                <div class="space-y-3">
                    @forelse(($recentBookingStatusUpdates ?? collect()) as $update)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold">Booking #{{ $update->booking_id }}: {{ ucfirst($update->to_status) }}</p>
                                <span class="text-xs text-gray-500 dark:text-text-secondary">{{ $update->created_at->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Updated by {{ $update->actor?->name ?? 'System' }}</p>
                            <a href="{{ route('bookings.show', $update->booking_id) }}" class="inline-flex mt-2 text-sm font-semibold text-orange hover:text-orange-light">View Booking</a>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No booking tracking updates yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Reviews To Write</h2>
                <a href="{{ route('reviews.index') }}" class="text-orange hover:text-orange-light font-semibold">Open Review Center</a>
            </div>
            <div class="space-y-3">
                @forelse(($pendingReviewItems ?? collect()) as $item)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                        <div>
                            <p class="font-semibold">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Order #{{ $item->order->id }} completed</p>
                        </div>
                        <a href="{{ route('orders.show', $item->order) }}#review-product-{{ $item->product->id }}" class="inline-flex items-center justify-center bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg font-semibold transition">Write Review</a>
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-text-secondary">No pending reviews. Thanks for sharing feedback.</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="glass-panel rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">My Uploaded Products</h2>
                <div class="space-y-3">
                    @forelse(($uploadedProducts ?? collect()) as $product)
                        <div class="flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <div>
                                <p class="font-semibold">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-text-secondary">{{ ucfirst($product->status) }}</p>
                                @if($product->admin_review_comment)
                                    <p class="text-xs text-gray-500 dark:text-text-secondary mt-1">Admin note: {{ $product->admin_review_comment }}</p>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product) }}" class="text-orange hover:text-orange-light text-sm">View</a>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No uploaded products yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-panel rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Recent Purchases</h2>
                <div class="space-y-3">
                    @forelse(($orders ?? collect()) as $order)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <p class="font-semibold">Order #{{ $order->id }} - ${{ number_format($order->total_price, 2) }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">{{ ucfirst($order->status) }} | {{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No purchases yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="glass-panel rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Referral Performance</h2>
                <div class="space-y-3">
                    @forelse(($referralLinks ?? collect()) as $link)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <p class="font-semibold">{{ $link->product->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Clicks: {{ $link->clicks }} | Conversions: {{ $link->conversions }}</p>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No referral links generated yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-panel rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Recent Points Activity</h2>
                <div class="space-y-3">
                    @forelse(($pointsTransactions ?? collect()) as $txn)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $txn->description ?: ucfirst(str_replace('_', ' ', $txn->type)) }}</p>
                                <p class="text-sm text-gray-600 dark:text-text-secondary">{{ $txn->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="font-bold text-orange">+{{ $txn->points }}</span>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No points activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </section>
</x-app-layout>
