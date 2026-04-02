<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-4 sticky top-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Dashboard</a>
                        <a href="{{ route('admin.products.pending') }}" class="block px-3 py-2 rounded-lg text-sm bg-orange/15 text-orange">Moderation Queue</a>
                        <a href="{{ route('admin.reviews.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Review Moderation</a>
                        <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Orders</a>
                        <a href="{{ route('admin.bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Bookings</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Pending Product Reviews</h1>
                    <a href="{{ route('admin.products.index') }}" class="text-orange hover:text-orange-light font-semibold text-sm">All Products</a>
                </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="bg-white dark:bg-dark-secondary rounded-2xl p-4 border border-gray-200 dark:border-gray-800 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="text-sm block mb-2">Status</label>
                <select name="status" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div>
                <label class="text-sm block mb-2">Category</label>
                <select name="category_id" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm block mb-2">Uploader</label>
                <select name="uploader_id" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <option value="">All uploaders</option>
                    @foreach($uploaders as $uploader)
                        <option value="{{ $uploader->id }}" {{ (string) request('uploader_id') === (string) $uploader->id ? 'selected' : '' }}>{{ $uploader->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm block mb-2">Search</label>
                <input name="search" value="{{ request('search') }}" placeholder="Product name" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
            </div>
            <button class="bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg font-semibold transition">Filter</button>
        </form>

        <form action="{{ route('admin.products.bulk-review') }}" method="POST" id="bulk-review-form" class="mb-6 bg-white dark:bg-dark-secondary rounded-2xl p-4 border border-gray-200 dark:border-gray-800">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="text-sm block mb-2">Bulk Status</label>
                    <select name="status" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2" required>
                        <option value="approved">Approve selected</option>
                        <option value="rejected">Reject selected</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm block mb-2">Comment</label>
                    <input name="admin_review_comment" placeholder="Optional moderation comment" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                </div>
                <button class="bg-gray-900 hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition">Apply Bulk Action</button>
            </div>
        </form>

        <div class="space-y-4">
            @forelse($products as $product)
                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                        <div>
                            <label class="inline-flex items-center gap-2 mb-3 text-sm text-gray-600 dark:text-text-secondary">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulk-review-form" class="rounded border-gray-300 text-orange focus:ring-orange">
                                Select for bulk moderation
                            </label>
                            <h2 class="text-2xl font-bold mb-2">{{ $product->name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Category: {{ $product->category->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Uploaded by: {{ $product->uploader?->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Price: ${{ number_format($product->price, 2) }} | Stock: {{ $product->stock_quantity }}</p>
                            <p class="text-gray-600 dark:text-text-secondary mt-4">{{ $product->description }}</p>
                        </div>

                        <div class="w-full md:w-96">
                            <form action="{{ route('admin.products.review', $product) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="block text-sm mb-2">Admin Comment</label>
                                    <textarea name="admin_review_comment" rows="3" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-3 py-2">{{ old('admin_review_comment') }}</textarea>
                                </div>

                                <div class="flex gap-3">
                                    <button type="submit" name="status" value="approved" class="flex-1 bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-semibold transition">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="flex-1 bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg font-semibold transition">Reject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-10 text-center border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-text-secondary">
                    No pending products at the moment.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
            </div>
        </div>
    </div>
</x-app-layout>
