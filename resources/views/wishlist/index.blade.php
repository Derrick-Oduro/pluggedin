<x-app-layout>
    <section class="page-shell">
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold">Saved Items</h1>
                    <p class="text-gray-600 dark:text-text-secondary mt-1">Compact shortlist for products you plan to buy later.</p>
                </div>
                <a href="{{ route('products.index') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Continue Shopping</a>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg border border-green-500/50 bg-green-500/10 px-3 py-2 text-sm text-green-600 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-500/50 bg-red-500/10 px-3 py-2 text-sm text-red-600 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <div class="backend-card p-3 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-text-secondary">Saved products</p>
                    <p class="text-lg font-semibold">{{ $items->total() }}</p>
                </div>
                <div class="backend-card p-3 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-text-secondary">Visible this page</p>
                    <p class="text-lg font-semibold">{{ $items->count() }}</p>
                </div>
                <div class="backend-card p-3 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-text-secondary">Page</p>
                    <p class="text-lg font-semibold">{{ $items->currentPage() }} / {{ max(1, $items->lastPage()) }}</p>
                </div>
            </div>

            <div class="space-y-2">
                @forelse($items as $item)
                    <div class="backend-card p-3 rounded-xl">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-gray-500 dark:text-text-secondary">{{ $item->product?->category?->name ?? 'Uncategorized' }}</p>
                                <h2 class="text-base font-semibold truncate">{{ $item->product?->name ?? 'Product unavailable' }}</h2>
                                @if($item->product)
                                    <p class="text-sm text-orange font-semibold mt-0.5">${{ number_format($item->product->price, 2) }}</p>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 sm:justify-end">
                                @if($item->product)
                                    <a href="{{ route('products.show', $item->product) }}" class="h-9 inline-flex items-center backend-btn-muted text-xs">View</a>
                                    <form action="{{ route('wishlist.move-to-cart', $item) }}" method="POST">
                                        @csrf
                                        <button class="h-9 px-3 rounded-lg bg-orange hover:bg-orange-light text-white text-xs font-semibold transition">Move to Cart</button>
                                    </form>
                                @endif
                                <form action="{{ route('wishlist.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="h-9 px-3 rounded-lg border border-red-500/40 text-red-500 hover:bg-red-500/10 text-xs font-semibold transition">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <x-empty-state
                            title="No saved items yet"
                            message="When you save a product, it appears here so you can add it to cart later."
                            actionLabel="Browse Products"
                            :actionUrl="route('products.index')"
                        />
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
