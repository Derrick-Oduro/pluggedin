<x-admin-layout>
                <x-page-header badge="Admin" title="Moderation queue" subtitle="Approve or reject listings submitted by sellers and shops.">
                    <x-slot:actions>
                        <a href="{{ route('admin.products.index') }}" class="btn-secondary">All products</a>
                    </x-slot:actions>
                </x-page-header>


        <form method="GET" class="backend-card grid gap-3 sm:grid-cols-2 xl:grid-cols-5 xl:items-end">
            <div>
                <label class="mb-1.5 block text-sm font-bold text-stone-800">Status</label>
                <select name="status" class="backend-field">
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-stone-800">Category</label>
                <select name="category_id" class="backend-field">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-stone-800">Uploader</label>
                <select name="uploader_id" class="backend-field">
                    <option value="">All uploaders</option>
                    @foreach($uploaders as $uploader)
                        <option value="{{ $uploader->id }}" {{ (string) request('uploader_id') === (string) $uploader->id ? 'selected' : '' }}>{{ $uploader->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-stone-800">Search</label>
                <input name="search" value="{{ request('search') }}" placeholder="Product name" class="backend-field">
            </div>
            <button class="btn-primary">Filter</button>
        </form>

        <form action="{{ route('admin.products.bulk-review') }}" method="POST" id="bulk-review-form" class="backend-card">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Bulk Status</label>
                    <select name="status" class="backend-field" required>
                        <option value="approved">Approve selected</option>
                        <option value="rejected">Reject selected</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Comment</label>
                    <input name="admin_review_comment" placeholder="Optional moderation comment" class="backend-field">
                </div>
                <button class="bg-stone-900 hover:bg-black text-white px-4 py-2 rounded-lg font-semibold transition">Apply Bulk Action</button>
            </div>
        </form>

        <div class="space-y-4">
            @forelse($products as $product)
                <div class="backend-card">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                        <div>
                            <label class="inline-flex items-center gap-2 mb-3 text-sm text-stone-600">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulk-review-form" class="rounded border-stone-300 text-brand focus:ring-brand">
                                Select for bulk moderation
                            </label>
                            <h2 class="text-lg font-extrabold text-stone-950 mb-2">{{ $product->name }}</h2>
                            <p class="text-sm text-stone-600 mb-2">Category: {{ $product->category->name }}</p>
                            <p class="text-sm text-stone-600 mb-2">Uploaded by: {{ $product->uploader?->name ?? 'N/A' }}</p>
                            <p class="text-sm text-stone-600">Price: GH₵{{ number_format($product->price, 2) }} | Stock: {{ $product->stock_quantity }}</p>
                            <p class="text-stone-600 mt-4">{{ $product->description }}</p>
                        </div>

                        <div class="w-full md:w-96">
                            <form action="{{ route('admin.products.review', $product) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Admin Comment</label>
                                    <textarea name="admin_review_comment" rows="3" class="backend-field">{{ old('admin_review_comment') }}</textarea>
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
                <div class="backend-card py-14 text-center text-stone-600">
                    No pending products at the moment.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
</x-admin-layout>
