<x-app-layout>
    <section class="page-shell">
    <div class="content-wrap relative z-10">
        <x-page-header badge="Account" title="My dashboard" subtitle="Track uploads, purchases, points and referrals.">
            <x-slot:actions>
                <a href="{{ route('products.upload.create') }}" class="btn-primary">Upload product</a>
                <a href="{{ route('products.upload.index') }}" class="btn-secondary">Manage uploads</a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Upload Limit</p>
                <p class="text-2xl font-bold">{{ $uploadsUsed ?? 0 }}/{{ $uploadLimit ?? 0 }}</p>
            </div>
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Uploads Remaining</p>
                <p class="text-2xl font-bold text-brand">{{ $uploadsRemaining ?? 0 }}</p>
            </div>
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Points Balance</p>
                <p class="text-2xl font-bold text-brand">{{ number_format($pointsBalance ?? 0) }}</p>
            </div>
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Total Reviews</p>
                <p class="text-2xl font-bold">{{ ($reviews ?? collect())->count() }}</p>
            </div>
        </div>

        <div class="glass-panel rounded-xl p-5 mb-10">
            <h2 class="text-lg font-extrabold text-stone-950 mb-3">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('products.upload.index', ['status' => 'pending']) }}" class="rounded-lg border border-stone-200 px-3 py-3 hover:border-brand transition">
                    <p class="text-xs text-stone-600">Uploads</p>
                    <p class="font-semibold mt-1">{{ $pendingUploadsCount ?? 0 }} pending uploads</p>
                </a>
                <a href="{{ route('orders.index') }}" class="rounded-lg border border-stone-200 px-3 py-3 hover:border-brand transition">
                    <p class="text-xs text-stone-600">Orders</p>
                    <p class="font-semibold mt-1">Track Purchases</p>
                </a>
                <a href="{{ route('bookings.index') }}" class="rounded-lg border border-stone-200 px-3 py-3 hover:border-brand transition">
                    <p class="text-xs text-stone-600">Bookings</p>
                    <p class="font-semibold mt-1">View Service Bookings</p>
                </a>
                <a href="{{ route('notifications.index', ['scope' => 'unread']) }}" class="rounded-lg border border-stone-200 px-3 py-3 hover:border-brand transition">
                    <p class="text-xs text-stone-600">Notifications</p>
                    <p class="font-semibold mt-1">{{ auth()->user()->unreadNotifications()->count() }} unread</p>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="backend-card">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Order Tracking Updates</h2>
                <div class="space-y-3">
                    @forelse(($recentOrderStatusUpdates ?? collect()) as $update)
                        <div class="border border-stone-200 rounded-lg p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold">Order #{{ $update->order_id }}: {{ ucfirst($update->to_status) }}</p>
                                <span class="text-xs text-stone-500">{{ $update->created_at->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-sm text-stone-600 mt-1">Updated by {{ $update->actor?->name ?? 'System' }}</p>
                            <a href="{{ route('orders.show', $update->order_id) }}" class="inline-flex mt-2 text-sm font-semibold text-brand hover:text-brand-deep">View Order</a>
                        </div>
                    @empty
                        <p class="text-stone-600">No order tracking updates yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="backend-card">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Booking Tracking Updates</h2>
                <div class="space-y-3">
                    @forelse(($recentBookingStatusUpdates ?? collect()) as $update)
                        <div class="border border-stone-200 rounded-lg p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold">Booking #{{ $update->booking_id }}: {{ ucfirst($update->to_status) }}</p>
                                <span class="text-xs text-stone-500">{{ $update->created_at->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-sm text-stone-600 mt-1">Updated by {{ $update->actor?->name ?? 'System' }}</p>
                            <a href="{{ route('bookings.show', $update->booking_id) }}" class="inline-flex mt-2 text-sm font-semibold text-brand hover:text-brand-deep">View Booking</a>
                        </div>
                    @empty
                        <p class="text-stone-600">No booking tracking updates yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-stone-950">Reviews To Write</h2>
                <a href="{{ route('reviews.index') }}" class="text-brand hover:text-brand-deep font-semibold">Open Review Center</a>
            </div>
            <div class="space-y-3">
                @forelse(($pendingReviewItems ?? collect()) as $item)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-stone-200 rounded-lg p-3">
                        <div>
                            <p class="font-semibold">{{ $item->product->name }}</p>
                            <p class="text-sm text-stone-600">Order #{{ $item->order->id }} completed</p>
                        </div>
                        <a href="{{ route('orders.show', $item->order) }}#review-product-{{ $item->product->id }}" class="btn-primary px-4">Write Review</a>
                    </div>
                @empty
                    <p class="text-stone-600">No pending reviews. Thanks for sharing feedback.</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="backend-card">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">My Uploaded Products</h2>
                <div class="space-y-3">
                    @forelse(($uploadedProducts ?? collect()) as $product)
                        <div class="flex items-center justify-between border border-stone-200 rounded-lg p-3">
                            <div>
                                <p class="font-semibold">{{ $product->name }}</p>
                                <p class="text-sm text-stone-600">{{ ucfirst($product->status) }}</p>
                                @if($product->admin_review_comment)
                                    <p class="text-xs text-stone-500 mt-1">Admin note: {{ $product->admin_review_comment }}</p>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product) }}" class="text-brand hover:text-brand-deep text-sm">View</a>
                        </div>
                    @empty
                        <p class="text-stone-600">No uploaded products yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="backend-card">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Recent Purchases</h2>
                <div class="space-y-3">
                    @forelse(($orders ?? collect()) as $order)
                        <div class="border border-stone-200 rounded-lg p-3">
                            <p class="font-semibold">Order #{{ $order->id }} - GH₵{{ number_format($order->total_price, 2) }}</p>
                            <p class="text-sm text-stone-600">{{ ucfirst($order->status) }} | {{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                    @empty
                        <p class="text-stone-600">No purchases yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="backend-card">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Referral Performance</h2>
                <div class="space-y-3">
                    @forelse(($referralLinks ?? collect()) as $link)
                        <div class="border border-stone-200 rounded-lg p-3">
                            <p class="font-semibold">{{ $link->product->name }}</p>
                            <p class="text-sm text-stone-600">Clicks: {{ $link->clicks }} | Conversions: {{ $link->conversions }}</p>
                        </div>
                    @empty
                        <p class="text-stone-600">No referral links generated yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="backend-card">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Recent Points Activity</h2>
                <div class="space-y-3">
                    @forelse(($pointsTransactions ?? collect()) as $txn)
                        <div class="border border-stone-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $txn->description ?: ucfirst(str_replace('_', ' ', $txn->type)) }}</p>
                                <p class="text-sm text-stone-600">{{ $txn->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="font-bold text-brand">+{{ $txn->points }}</span>
                        </div>
                    @empty
                        <p class="text-stone-600">No points activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </section>
</x-app-layout>
