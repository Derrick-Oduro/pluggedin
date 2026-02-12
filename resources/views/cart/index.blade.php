<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">Shopping Cart</h1>

        @if($cartItems->count() > 0)
            <div class="space-y-4 mb-8">
                @foreach($cartItems as $item)
                    <div class="bg-dark-secondary rounded-lg p-6 flex items-center gap-6">
                        @if($item->product->image_path)
                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="w-24 h-24 object-cover rounded">
                        @else
                            <div class="w-24 h-24 bg-gray-800 rounded"></div>
                        @endif

                        <div class="flex-1">
                            <h3 class="text-xl font-semibold mb-2">{{ $item->product->name }}</h3>
                            <p class="text-orange font-bold">${{ number_format($item->product->price, 2) }}</p>
                        </div>

                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}"
                                   class="w-20 bg-dark border border-gray-700 rounded px-2 py-1 text-center">
                            <button type="submit" class="bg-orange hover:bg-orange-light text-white px-4 py-1 rounded text-sm">
                                Update
                            </button>
                        </form>

                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-400">
                                Remove
                            </button>
                        </form>

                        <p class="text-xl font-bold">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-dark-secondary rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-2xl font-bold">Total:</span>
                    <span class="text-3xl font-bold text-orange">${{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('checkout') }}" class="block text-center bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold transition">
                    Proceed to Checkout
                </a>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-text-secondary text-xl mb-6">Your cart is empty</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                    Continue Shopping
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
