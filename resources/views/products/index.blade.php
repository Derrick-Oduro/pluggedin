<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/45 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="absolute inset-0 pointer-events-none opacity-70">
            <div class="absolute -top-20 left-1/4 h-72 w-72 rounded-full bg-orange/20 blur-3xl"></div>
            <div class="absolute -bottom-20 right-1/4 h-72 w-72 rounded-full bg-orange/15 blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-4">Product Catalog</p>
                <h1 class="text-5xl sm:text-6xl font-bold leading-tight mb-5">Upgrade Components and Essentials</h1>
                <p class="text-lg text-gray-600 dark:text-text-secondary">Browse quality parts selected for compatibility, performance, and long-term reliability.</p>
            </div>

            <div class="bg-white/95 dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-4 sm:p-5 mb-10 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-text-secondary">Filter by Category</h2>
                    <p class="text-sm text-gray-500 dark:text-text-secondary">{{ $products->total() }} items</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ !request('category') ? 'bg-orange border-orange text-white shadow-lg shadow-orange/20' : 'bg-gray-100 border-gray-200 dark:bg-dark dark:border-gray-700 text-gray-700 dark:text-text-secondary hover:border-orange/40' }}">
                        All
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ request('category') == $category->slug ? 'bg-orange border-orange text-white shadow-lg shadow-orange/20' : 'bg-gray-100 border-gray-200 dark:bg-dark dark:border-gray-700 text-gray-700 dark:text-text-secondary hover:border-orange/40' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7">
                @forelse($products as $product)
                    <a href="{{ route('products.show', $product) }}" class="group bg-white dark:bg-dark-secondary rounded-2xl overflow-hidden border border-gray-200/70 dark:border-gray-800 hover:-translate-y-1 hover:shadow-2xl hover:shadow-orange/10 transition duration-300">
                        <div class="relative overflow-hidden">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-52 object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-52 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute top-3 left-3 bg-black/65 text-white text-xs px-3 py-1 rounded-full backdrop-blur">
                                {{ $product->category->name }}
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="text-lg font-semibold mb-2 group-hover:text-orange transition">{{ $product->name }}</h3>
                            <p class="text-gray-600 dark:text-text-secondary text-sm mb-4 line-clamp-2">{{ Str::limit($product->description, 70) }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <p class="text-2xl font-bold text-orange">${{ number_format($product->price, 2) }}</p>
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $product->stock_quantity > 0 ? 'In stock: ' . $product->stock_quantity : 'Out of stock' }}
                                </span>
                            </div>

                            <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-orange transition">
                                View product
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                                </svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-14 text-center">
                        <div class="mx-auto mb-5 h-14 w-14 rounded-2xl bg-orange/15 text-orange flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3.75h1.386a1.5 1.5 0 0 1 1.464 1.175L5.7 7.5m0 0h12.114a1.5 1.5 0 0 1 1.464 1.825l-1.2 6A1.5 1.5 0 0 1 16.61 16.5H8.025a1.5 1.5 0 0 1-1.464-1.175L5.7 7.5Zm0 0L4.5 3.75M9 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm8.25 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">No Products Found</h3>
                        <p class="text-gray-600 dark:text-text-secondary mb-6">Try a different category filter or check back soon for new inventory.</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                            Reset Filters
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.183" />
                            </svg>
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-10 bg-white/95 dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-4 sm:p-6">
                {{ $products->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
