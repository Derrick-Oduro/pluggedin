<x-app-layout>
    @php
        $categoryPills = collect($categories)->map(fn ($category) => [
            'label' => $category->name,
            'value' => $category->slug,
        ]);
        $activeCategory = request('category');
        $baseQuery = request()->except(['category', 'page']);
    @endphp

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

            <div class="bg-white/95 dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-4 sm:p-5 mb-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-text-secondary">Global Search</h2>
                    <p class="text-xs text-gray-500 dark:text-text-secondary">Products, services, orders, users</p>
                </div>
                <form method="GET" action="{{ route('search.global') }}" class="flex flex-col sm:flex-row gap-2">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Try: gpu, cleaning service, pending, john@example.com"
                        class="backend-field flex-1"
                    >
                    <button class="bg-orange hover:bg-orange-light text-white px-5 py-2 rounded-lg font-semibold transition">Search</button>
                </form>
            </div>

            <x-filter-bar :action="route('products.index')" title="Filter Products" :count="$products->total()" formClass="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products" class="backend-field h-10 text-sm">
                <input type="number" step="0.01" min="0" name="min_price" value="{{ request('min_price') }}" placeholder="Min price" class="backend-field h-10 text-sm">
                <input type="number" step="0.01" min="0" name="max_price" value="{{ request('max_price') }}" placeholder="Max price" class="backend-field h-10 text-sm">
                <select name="availability" class="backend-field h-10 text-sm">
                    <option value="all" {{ request('availability', 'all') === 'all' ? 'selected' : '' }}>All availability</option>
                    <option value="in_stock" {{ request('availability') === 'in_stock' ? 'selected' : '' }}>In stock only</option>
                    <option value="out_of_stock" {{ request('availability') === 'out_of_stock' ? 'selected' : '' }}>Out of stock only</option>
                </select>
                <select name="sort" class="backend-field h-10 text-sm">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price low to high</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price high to low</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                </select>
                <div class="flex gap-2">
                    <button class="h-10 bg-orange hover:bg-orange-light text-white px-5 rounded-lg text-sm font-semibold transition">Apply</button>
                    <a href="{{ route('products.index') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Reset</a>
                </div>
            </x-filter-bar>

            <div class="mb-8 flex flex-wrap items-center gap-2">
                <a href="{{ route('products.index', $baseQuery) }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ $activeCategory === null || $activeCategory === '' ? 'border-brand bg-brand-tint text-brand-deep shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:border-brand hover:text-brand-deep dark:border-gray-800 dark:bg-dark-secondary dark:text-text-secondary' }}">
                    All
                </a>

                @foreach($categoryPills as $pill)
                    <a href="{{ route('products.index', array_merge($baseQuery, ['category' => $pill['value']])) }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ $activeCategory === $pill['value'] ? 'border-brand bg-brand-tint text-brand-deep shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:border-brand hover:text-brand-deep dark:border-gray-800 dark:bg-dark-secondary dark:text-text-secondary' }}">
                        {{ $pill['label'] }}
                    </a>
                @endforeach
            </div>

            @if($products->count())
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:hidden">
                    @foreach($products as $product)
                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200/70 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-orange/10 dark:border-gray-800 dark:bg-dark-secondary">
                            <a href="{{ route('products.show', $product) }}" class="relative block overflow-hidden">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-56 w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-56 w-full items-center justify-center bg-gray-100 dark:bg-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="absolute left-3 top-3 rounded-full bg-black/65 px-3 py-1 text-xs text-white backdrop-blur">
                                    {{ $product->category->name }}
                                </div>
                            </a>

                            <div class="flex flex-1 flex-col p-5">
                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-lg font-semibold leading-snug transition group-hover:text-orange">{{ $product->name }}</h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-text-secondary line-clamp-2">{{ Str::limit($product->description, 90) }}</p>
                                    </div>
                                </div>

                                <div class="mt-auto space-y-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-text-secondary">Price</p>
                                            <p class="text-2xl font-bold text-orange">GH₵{{ number_format($product->price, 2) }}</p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $product->stock_quantity > 0 ? 'In stock: ' . $product->stock_quantity : 'Out of stock' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('products.show', $product) }}" class="inline-flex flex-1 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-orange hover:text-orange dark:border-gray-700 dark:text-text-secondary dark:hover:border-orange-light dark:hover:text-orange-light">
                                            View details
                                        </a>

                                        <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-orange px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-light disabled:cursor-not-allowed disabled:opacity-60" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                Add to Cart
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="hidden lg:grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-7">
                    @foreach($products as $product)
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
                                    <p class="text-2xl font-bold text-orange">GH₵{{ number_format($product->price, 2) }}</p>
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
                    @endforeach
                </div>
            @else
                <div class="col-span-full bg-white dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-14 text-center">
                    <div class="mx-auto mb-5 h-14 w-14 rounded-2xl bg-orange/15 text-orange flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3.75h1.386a1.5 1.5 0 0 1 1.464 1.175L5.7 7.5m0 0h12.114a1.5 1.5 0 0 1 1.464 1.825l-1.2 6A1.5 1.5 0 0 1 16.61 16.5H8.025a1.5 1.5 0 0 1-1.464-1.175L5.7 7.5Zm0 0L4.5 3.75M9 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm8.25 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">No Products Found</h3>
                    <p class="text-gray-600 dark:text-text-secondary mb-6">Try adjusting your filters or check back soon for new inventory.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                        Reset Filters
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.183" />
                        </svg>
                    </a>
                </div>
            @endif

            <div class="mt-10 bg-white/95 dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-4 sm:p-6">
                {{ $products->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
