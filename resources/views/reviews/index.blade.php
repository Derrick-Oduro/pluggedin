<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Reviews" title="My reviews" subtitle="Review items you've bought and manage feedback you've already left.">
                <x-slot:actions>
                    <a href="{{ route('orders.index') }}" class="btn-secondary">View orders</a>
                </x-slot:actions>
            </x-page-header>


            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <div class="backend-card">
                    <h2 class="text-lg font-extrabold text-stone-950 mb-5">Pending Reviews</h2>
                    <div class="space-y-4">
                        @forelse($pendingReviews as $item)
                            <div class="border border-stone-200 rounded-xl p-4">
                                <p class="font-semibold text-lg">{{ $item->product->name }}</p>
                                <p class="text-sm text-stone-600 mt-1">Order #{{ $item->order->id }} • {{ ucfirst($item->order->status) }}</p>
                                <a href="{{ route('orders.show', $item->order) }}#review-product-{{ $item->product->id }}" class="btn-primary mt-4 px-4">
                                    Write Review
                                </a>
                            </div>
                        @empty
                            <p class="text-stone-600">No pending reviews right now.</p>
                        @endforelse
                    </div>
                </div>

                <div class="backend-card">
                    <h2 class="text-lg font-extrabold text-stone-950 mb-5">Submitted Reviews</h2>
                    <div class="space-y-4">
                        @forelse($submittedReviews as $review)
                            <div class="border border-stone-200 rounded-xl p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-lg">{{ $review->product?->name ?? 'Product removed' }}</p>
                                    <span class="text-sm px-3 py-1 rounded-full bg-brand/15 text-brand-deep font-semibold">{{ $review->rating }}/5</span>
                                </div>
                                @if($review->comment)
                                    <p class="text-stone-600 mt-3">{{ $review->comment }}</p>
                                @endif
                                <p class="text-xs text-stone-500 mt-3">Updated {{ $review->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-stone-600">You have not submitted any reviews yet.</p>
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
