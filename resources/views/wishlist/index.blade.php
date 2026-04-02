<x-app-layout>
    <section class="page-shell">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
                <div>
                    <h1 class="text-4xl font-bold">Saved Items</h1>
                    <p class="text-gray-600 dark:text-text-secondary mt-2">Bookmark products now and move them to cart later.</p>
                </div>
                <a href="{{ route('products.index') }}" class="backend-btn-muted">Continue Shopping</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($items as $item)
                    <div class="backend-card p-4 rounded-xl">
                        <p class="text-xs text-gray-500 dark:text-text-secondary mb-1">{{ $item->product?->category?->name ?? 'Uncategorized' }}</p>
                        <h2 class="text-lg font-semibold">{{ $item->product?->name ?? 'Product unavailable' }}</h2>
                        @if($item->product)
                            <p class="text-orange font-semibold mt-2">${{ number_format($item->product->price, 2) }}</p>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($item->product)
                                <a href="{{ route('products.show', $item->product) }}" class="backend-btn-muted">View</a>
                                <form action="{{ route('wishlist.move-to-cart', $item) }}" method="POST">
                                    @csrf
                                    <button class="backend-btn-primary">Move to Cart</button>
                                </form>
                            @endif
                            <form action="{{ route('wishlist.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-500 text-white px-3 py-2 rounded-lg text-sm font-semibold transition">Remove</button>
                            </form>
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
