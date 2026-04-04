<x-app-layout>
    <section class="page-shell">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-bold">Global Search</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-2">Search across products, services, orders, and users based on your role.</p>
            </div>

            <form method="GET" action="{{ route('search.global') }}" class="glass-panel rounded-xl p-4 mb-6">
                <label for="q" class="block text-sm font-medium mb-2">Search term</label>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-2">
                    <input id="q" name="q" value="{{ $term }}" placeholder="Try: gpu, cleaning service, pending, john@example.com" class="backend-field h-10 text-sm" />
                    <select name="type" class="backend-field h-10 text-sm">
                        <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>All types</option>
                        <option value="products" {{ ($type ?? 'all') === 'products' ? 'selected' : '' }}>Products</option>
                        <option value="services" {{ ($type ?? 'all') === 'services' ? 'selected' : '' }}>Services</option>
                        @if(($canSearchOrders ?? false))
                            <option value="orders" {{ ($type ?? 'all') === 'orders' ? 'selected' : '' }}>Orders</option>
                        @endif
                        @if(($canSearchUsers ?? false))
                            <option value="users" {{ ($type ?? 'all') === 'users' ? 'selected' : '' }}>Users</option>
                        @endif
                    </select>
                    <select name="product_category" class="backend-field h-10 text-sm">
                        <option value="">All product categories</option>
                        @foreach(($categories ?? collect()) as $category)
                            <option value="{{ $category->slug }}" {{ ($productCategory ?? '') === $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" min="0" name="min_price" value="{{ $minPrice }}" placeholder="Min price" class="backend-field h-10 text-sm">
                    <input type="number" step="0.01" min="0" name="max_price" value="{{ $maxPrice }}" placeholder="Max price" class="backend-field h-10 text-sm">
                    <select name="availability" class="backend-field h-10 text-sm">
                        <option value="all" {{ ($availability ?? 'all') === 'all' ? 'selected' : '' }}>All availability</option>
                        <option value="in_stock" {{ ($availability ?? 'all') === 'in_stock' ? 'selected' : '' }}>In stock only</option>
                        <option value="out_of_stock" {{ ($availability ?? 'all') === 'out_of_stock' ? 'selected' : '' }}>Out of stock only</option>
                    </select>
                    <select name="sort" class="backend-field h-10 text-sm">
                        <option value="latest" {{ ($sort ?? 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ ($sort ?? 'latest') === 'price_asc' ? 'selected' : '' }}>Price low to high</option>
                        <option value="price_desc" {{ ($sort ?? 'latest') === 'price_desc' ? 'selected' : '' }}>Price high to low</option>
                        <option value="name_asc" {{ ($sort ?? 'latest') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    </select>
                    @if(($canSearchOrders ?? false))
                        <select name="order_status" class="backend-field h-10 text-sm">
                            <option value="">All order statuses</option>
                            @foreach(($orderStatuses ?? collect()) as $status)
                                <option value="{{ $status }}" {{ ($orderStatus ?? '') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    @endif
                    <div class="flex gap-2">
                        <button class="h-10 bg-orange hover:bg-orange-light text-white px-5 rounded-lg text-sm font-semibold transition">Search</button>
                        <a href="{{ route('search.global') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Reset</a>
                    </div>
                </div>
            </form>

            @if($term === '')
                <x-empty-state
                    title="Start searching"
                    message="Use the search field above to find products, services, orders, and users."
                />
            @else
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    @if(in_array(($type ?? 'all'), ['all', 'products'], true))
                        <div class="backend-card p-5">
                            <h2 class="text-lg font-semibold mb-3">Products</h2>
                            <div class="space-y-2">
                                @forelse($products as $product)
                                    <a href="{{ route('products.show', $product) }}" class="block rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 hover:border-orange transition">
                                        <p class="font-semibold">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-text-secondary">{{ $product->category?->name ?? 'Uncategorized' }} · ${{ number_format($product->price, 2) }}</p>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-text-secondary">No products matched.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    @if(in_array(($type ?? 'all'), ['all', 'services'], true))
                        <div class="backend-card p-5">
                            <h2 class="text-lg font-semibold mb-3">Services</h2>
                            <div class="space-y-2">
                                @forelse($services as $service)
                                    <a href="{{ route('services.show', $service) }}" class="block rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 hover:border-orange transition">
                                        <p class="font-semibold">{{ $service->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-text-secondary">${{ number_format($service->price, 2) }}</p>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-text-secondary">No services matched.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    @auth
                        @if(in_array(($type ?? 'all'), ['all', 'orders'], true))
                        <div class="backend-card p-5">
                            <h2 class="text-lg font-semibold mb-3">Orders</h2>
                            <div class="space-y-2">
                                @forelse($orders as $order)
                                    @php($orderRoute = auth()->user()->isAdmin() ? route('admin.orders.show', $order) : route('orders.show', $order))
                                    <a href="{{ $orderRoute }}" class="block rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 hover:border-orange transition">
                                        <p class="font-semibold">Order #{{ $order->id }} · {{ ucfirst($order->status) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-text-secondary">${{ number_format($order->total_price, 2) }} · {{ $order->created_at->format('M d, Y') }}</p>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-text-secondary">No orders matched.</p>
                                @endforelse
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->isSuperAdmin() && in_array(($type ?? 'all'), ['all', 'users'], true))
                            <div class="backend-card p-5">
                                <h2 class="text-lg font-semibold mb-3">Users</h2>
                                <div class="space-y-2">
                                    @forelse($users as $matchedUser)
                                        <a href="{{ route('superadmin.users.edit', $matchedUser) }}" class="block rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 hover:border-orange transition">
                                            <p class="font-semibold">{{ $matchedUser->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-text-secondary">{{ $matchedUser->email }}</p>
                                        </a>
                                    @empty
                                        <p class="text-sm text-gray-600 dark:text-text-secondary">No users matched.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
