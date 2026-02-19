<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Products</h1>

            <!-- Category Filter -->
            <div class="flex gap-4">
                <a href="{{ route('products.index') }}" class="px-4 py-2 rounded {{ !request('category') ? 'bg-orange text-white' : 'bg-gray-100 dark:bg-dark-secondary text-gray-700 dark:text-text-secondary' }}">
                    All
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                       class="px-4 py-2 rounded {{ request('category') == $category->slug ? 'bg-orange text-white' : 'bg-gray-100 dark:bg-dark-secondary text-gray-700 dark:text-text-secondary' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <a href="{{ route('products.show', $product) }}" class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden hover:ring-2 hover:ring-orange transition border border-gray-200 dark:border-gray-800">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500 dark:text-text-secondary">No Image</span>
                        </div>
                    @endif

                    <div class="p-4">
                        <p class="text-orange text-sm mb-1">{{ $product->category->name }}</p>
                        <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 dark:text-text-secondary text-sm mb-3 line-clamp-2">{{ Str::limit($product->description, 60) }}</p>
                        <div class="flex justify-between items-center">
                            <p class="text-xl font-bold text-orange">${{ number_format($product->price, 2) }}</p>
                            <span class="text-sm text-gray-500 dark:text-text-secondary">Stock: {{ $product->stock_quantity }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500 dark:text-text-secondary">
                    No products found.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
