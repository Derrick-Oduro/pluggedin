<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Pending Product Reviews</h1>
            <a href="{{ route('admin.products.index') }}" class="text-orange hover:text-orange-light font-semibold">All Products</a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6">
            @forelse($products as $product)
                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                        <div>
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
</x-app-layout>
