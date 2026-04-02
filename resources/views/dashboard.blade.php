<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold">My Dashboard</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-1">Track uploads, purchases, points, and referrals.</p>
            </div>
            <a href="{{ route('products.upload.create') }}" class="bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                Upload Product
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white dark:bg-dark-secondary rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Upload Limit</p>
                <p class="text-2xl font-bold">{{ $uploadsUsed ?? 0 }}/{{ $uploadLimit ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Uploads Remaining</p>
                <p class="text-2xl font-bold text-orange">{{ $uploadsRemaining ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Points Balance</p>
                <p class="text-2xl font-bold text-orange">{{ number_format($pointsBalance ?? 0) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Total Reviews</p>
                <p class="text-2xl font-bold">{{ ($reviews ?? collect())->count() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white dark:bg-dark-secondary rounded-xl p-6 border border-gray-200 dark:border-gray-800">
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

            <div class="bg-white dark:bg-dark-secondary rounded-xl p-6 border border-gray-200 dark:border-gray-800">
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
            <div class="bg-white dark:bg-dark-secondary rounded-xl p-6 border border-gray-200 dark:border-gray-800">
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

            <div class="bg-white dark:bg-dark-secondary rounded-xl p-6 border border-gray-200 dark:border-gray-800">
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
</x-app-layout>
