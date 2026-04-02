<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Feedback Center</p>
                    <h1 class="text-5xl sm:text-6xl font-bold">My Reviews</h1>
                    <p class="text-gray-600 dark:text-text-secondary mt-3">Write reviews for completed purchases and manage feedback you already submitted.</p>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center text-orange hover:text-orange-light font-semibold">View orders</a>
            </div>

            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                    <h2 class="text-2xl font-bold mb-5">Pending Reviews</h2>
                    <div class="space-y-4">
                        @forelse($pendingReviews as $item)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                                <p class="font-semibold text-lg">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Order #{{ $item->order->id }} • {{ ucfirst($item->order->status) }}</p>
                                <a href="{{ route('orders.show', $item->order) }}#review-product-{{ $item->product->id }}" class="inline-flex items-center mt-4 bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg font-semibold transition">
                                    Write Review
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-text-secondary">No pending reviews right now.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                    <h2 class="text-2xl font-bold mb-5">Submitted Reviews</h2>
                    <div class="space-y-4">
                        @forelse($submittedReviews as $review)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-lg">{{ $review->product?->name ?? 'Product removed' }}</p>
                                    <span class="text-sm px-3 py-1 rounded-full bg-orange/15 text-orange font-semibold">{{ $review->rating }}/5</span>
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-600 dark:text-text-secondary mt-3">{{ $review->comment }}</p>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-text-secondary mt-3">Updated {{ $review->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-text-secondary">You have not submitted any reviews yet.</p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $submittedReviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
