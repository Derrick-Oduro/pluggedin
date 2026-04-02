<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="absolute inset-0 pointer-events-none opacity-70">
            <div class="absolute -top-20 left-1/4 h-64 w-64 rounded-full bg-orange/20 blur-3xl"></div>
            <div class="absolute -bottom-16 right-1/4 h-64 w-64 rounded-full bg-orange/15 blur-3xl"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Cart</p>
                <h1 class="text-5xl sm:text-6xl font-bold">Shopping Cart</h1>
            </div>

            @if($cartItems->count() > 0)
                <div class="space-y-4 mb-8">
                    @foreach($cartItems as $item)
                        <div class="bg-white dark:bg-dark-secondary rounded-2xl p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center gap-5 border border-gray-200 dark:border-gray-800">
                            <div class="flex items-center gap-5 flex-1">
                                @if($item->product->image_path)
                                    <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-200 dark:bg-gray-800 rounded-lg"></div>
                                @endif

                                <div>
                                    <h3 class="text-xl font-semibold mb-1">{{ $item->product->name }}</h3>
                                    <p class="text-orange font-bold">${{ number_format($item->product->price, 2) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}"
                                           class="w-20 bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-2 py-1.5 text-center">
                                    <button type="submit" class="bg-orange hover:bg-orange-light text-white px-4 py-1.5 rounded text-sm font-semibold transition">
                                        Update
                                    </button>
                                </form>

                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-semibold">
                                        Remove
                                    </button>
                                </form>
                            </div>

                            <p class="text-xl font-bold text-orange">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-2xl font-bold">Total:</span>
                        <span class="text-3xl font-bold text-orange">${{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout') }}" class="block text-center bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold transition">
                        Proceed to Checkout
                    </a>
                </div>
            @else
                <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 text-center py-14 px-6">
                    <p class="text-gray-600 dark:text-text-secondary text-xl mb-6">Your cart is empty</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Continue Shopping
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
