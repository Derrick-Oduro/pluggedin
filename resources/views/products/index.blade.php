<x-app-layout>
    @php
        $activeCategory = request('category');
        $baseQuery = request()->except(['category', 'page']);
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <header class="max-w-3xl">
                <span class="brand-badge">Marketplace</span>
                <h1 class="section-title mt-4 text-4xl sm:text-5xl">Laptops, phones, parts and more.</h1>
                <p class="section-subtitle mt-4 max-w-2xl text-base">Every listing is checked before it goes live. Pay securely with Paystack and get free delivery on campus.</p>
            </header>

            <div class="mt-10 flex flex-wrap gap-2 border-b border-stone-200 pb-5">
                <a href="{{ route('products.index', $baseQuery) }}" class="rounded-full px-4 py-2 text-sm font-bold transition {{ ! $activeCategory ? 'bg-stone-950 text-white' : 'bg-white text-stone-600 hover:bg-stone-100' }}">All products</a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', array_merge($baseQuery, ['category' => $category->slug])) }}" class="rounded-full px-4 py-2 text-sm font-bold transition {{ $activeCategory === $category->slug ? 'bg-stone-950 text-white' : 'bg-white text-stone-600 hover:bg-stone-100' }}">{{ $category->name }}</a>
                @endforeach
            </div>

            <details class="group mt-5 rounded-2xl border border-stone-200 bg-white p-4 shadow-[0_12px_30px_-26px_rgba(41,30,23,0.25)]" {{ request()->hasAny(['q', 'min_price', 'max_price', 'availability', 'sort']) ? 'open' : '' }}>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4"><span><span class="block text-sm font-extrabold text-stone-950">Find the right part</span><span class="mt-1 block text-xs text-stone-500">Search, price range, availability, and sorting.</span></span><span class="rounded-lg bg-stone-100 px-3 py-2 text-xs font-bold text-stone-600 group-open:hidden">Filter & sort</span><span class="hidden rounded-lg bg-stone-100 px-3 py-2 text-xs font-bold text-stone-600 group-open:inline">Close</span></summary>
                <form method="GET" action="{{ route('products.index') }}" class="mt-5 grid gap-3 border-t border-stone-100 pt-5 sm:grid-cols-2 lg:grid-cols-5">
                    @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products" class="backend-field lg:col-span-2">
                    <select name="availability" class="backend-field"><option value="all">All availability</option><option value="in_stock" @selected(request('availability') === 'in_stock')>In stock</option><option value="out_of_stock" @selected(request('availability') === 'out_of_stock')>Out of stock</option></select>
                    <select name="sort" class="backend-field"><option value="latest">Latest first</option><option value="price_asc" @selected(request('sort') === 'price_asc')>Price: low to high</option><option value="price_desc" @selected(request('sort') === 'price_desc')>Price: high to low</option><option value="name_asc" @selected(request('sort') === 'name_asc')>Name: A–Z</option></select>
                    <div class="flex gap-2"><button class="backend-btn-primary flex-1">Apply</button><a href="{{ route('products.index') }}" class="btn-secondary px-4">Reset</a></div>
                    <input type="number" step="0.01" min="0" name="min_price" value="{{ request('min_price') }}" placeholder="Minimum price" class="backend-field"><input type="number" step="0.01" min="0" name="max_price" value="{{ request('max_price') }}" placeholder="Maximum price" class="backend-field">
                </form>
            </details>

            <div class="mt-10 flex items-center justify-between"><p class="text-sm font-bold text-stone-600"><span class="text-stone-950">{{ $products->total() }}</span> product{{ $products->total() === 1 ? '' : 's' }} available</p><p class="hidden text-sm text-stone-500 sm:block">Compatibility questions? <a href="{{ route('contact') }}" class="font-bold text-brand-deep hover:text-brand-deep">Talk to us.</a></p></div>

            @if($products->count())
                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        <article class="group flex overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:-translate-y-1 hover:shadow-[0_22px_45px_-30px_rgba(41,30,23,0.45)] sm:block">
                            <a href="{{ route('products.show', $product) }}" class="relative block w-32 shrink-0 overflow-hidden bg-stone-100 sm:h-52 sm:w-full">
                                @if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@else<div class="flex h-full items-center justify-center p-3 text-center text-xs font-bold text-stone-400">{{ $product->category->name }}</div>@endif
                                <span class="absolute left-3 top-3 hidden rounded-full bg-stone-950 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-white sm:block">{{ $product->category->name }}</span>
                            </a>
                            <div class="flex min-w-0 flex-1 flex-col p-4 sm:p-5"><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-brand-deep sm:hidden">{{ $product->category->name }}</p><a href="{{ route('products.show', $product) }}" class="mt-1 text-base font-extrabold leading-snug text-stone-950 hover:text-brand-deep sm:text-lg">{{ $product->name }}</a><p class="mt-2 hidden min-h-10 text-sm leading-5 text-stone-600 sm:block">{{ Str::limit($product->description, 75) }}</p><div class="mt-auto pt-4"><div class="flex items-center justify-between gap-2"><span class="text-lg font-extrabold text-stone-950">GH₵{{ number_format($product->price, 2) }}</span><span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $product->stock_quantity > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">{{ $product->stock_quantity > 0 ? 'IN STOCK' : 'SOLD OUT' }}</span></div><div class="mt-4 flex gap-2"><a href="{{ route('products.show', $product) }}" class="btn-secondary h-10 flex-1 px-3 text-xs">Details</a><form method="POST" action="{{ route('cart.add', $product) }}" class="flex-1">@csrf<button type="submit" class="btn-primary h-10 w-full px-3 text-xs disabled:cursor-not-allowed disabled:opacity-50" @disabled($product->stock_quantity <= 0)>Add</button></form></div></div></div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-5 rounded-2xl border border-dashed border-stone-300 bg-white p-12 text-center"><p class="text-lg font-extrabold text-stone-950">No products match those filters.</p><p class="mt-2 text-sm text-stone-600">Try changing your search or viewing every product.</p><a href="{{ route('products.index') }}" class="btn-primary mt-5">View all products</a></div>
            @endif

            <div class="mt-10 rounded-2xl border border-stone-200 bg-white p-4">{{ $products->links() }}</div>
        </div>
    </section>
</x-app-layout>
