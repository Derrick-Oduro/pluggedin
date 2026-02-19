<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Product Image -->
            <div>
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                @else
                    <div class="w-full h-96 bg-gray-100 dark:bg-dark-secondary rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-800">
                        <span class="text-gray-500 dark:text-text-secondary text-xl">No Image</span>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <p class="text-orange text-sm mb-2">{{ $product->category->name }}</p>
                <h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>
                <p class="text-3xl font-bold text-orange mb-6">${{ number_format($product->price, 2) }}</p>

                <div class="mb-6">
                    <p class="text-gray-600 dark:text-text-secondary mb-2">Stock: {{ $product->stock_quantity }} available</p>
                </div>

                <p class="text-gray-600 dark:text-text-secondary mb-8 leading-relaxed">{{ $product->description }}</p>

                @auth
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm mb-2">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                   class="w-full bg-white dark:bg-dark-secondary border border-gray-300 dark:border-gray-700 rounded px-4 py-2">
                        </div>

                        <button type="submit" class="w-full bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                            Add to Cart
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                        Login to Purchase
                    </a>
                @endauth
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold mb-6">Related Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('products.show', $related) }}" class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden hover:ring-2 hover:ring-orange transition border border-gray-200 dark:border-gray-800">
                            @if($related->image_path)
                                <img src="{{ asset('storage/' . $related->image_path) }}" alt="{{ $related->name }}" class="w-full h-32 object-cover">
                            @else
                                <div class="w-full h-32 bg-gray-200 dark:bg-gray-800"></div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold mb-2">{{ $related->name }}</h3>
                                <p class="text-orange font-bold">${{ number_format($related->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
