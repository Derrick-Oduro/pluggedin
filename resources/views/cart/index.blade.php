<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="absolute inset-0 pointer-events-none opacity-70">
            <div class="absolute -top-20 left-1/4 h-64 w-64 rounded-full bg-orange/20 blur-3xl"></div>
            <div class="absolute -bottom-16 right-1/4 h-64 w-64 rounded-full bg-orange/15 blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Cart</p>
                <h1 class="text-4xl sm:text-5xl font-bold">Shopping Cart</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-2">Quickly adjust quantities, remove items, and checkout.</p>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg border border-green-500/50 bg-green-500/10 px-4 py-3 text-sm text-green-600 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-600 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            @if($cartItems->count() > 0)
                @php
                    $lineItems = $cartItems->sum('quantity');
                    $avgLine = $cartItems->count() ? $total / $cartItems->count() : 0;
                @endphp

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    <div class="xl:col-span-8 space-y-3">
                        @foreach($cartItems as $item)
                            <div class="bg-white dark:bg-dark-secondary rounded-xl p-4 border border-gray-200 dark:border-gray-800">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        @if($item->product->image_path)
                                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg shrink-0">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-800 rounded-lg shrink-0"></div>
                                        @endif

                                        <div class="min-w-0">
                                            <h3 class="text-base font-semibold truncate">{{ $item->product->name }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-text-secondary">${{ number_format($item->product->price, 2) }} each</p>
                                            <p class="text-xs text-gray-500 dark:text-text-secondary">Stock: {{ $item->product->stock_quantity }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 sm:justify-end">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="number"
                                                name="quantity"
                                                value="{{ $item->quantity }}"
                                                min="1"
                                                max="{{ $item->product->stock_quantity }}"
                                                class="w-16 h-9 bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-2 text-center text-sm"
                                            >
                                            <button type="submit" class="h-9 px-3 rounded-lg bg-orange hover:bg-orange-light text-white text-xs font-semibold transition">Update</button>
                                        </form>

                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-9 px-3 rounded-lg border border-red-500/40 text-red-500 hover:bg-red-500/10 text-xs font-semibold transition">Remove</button>
                                        </form>
                                    </div>

                                    <div class="sm:text-right">
                                        <p class="text-xs text-gray-500 dark:text-text-secondary">Line total</p>
                                        <p class="text-lg font-bold text-orange">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="xl:col-span-4">
                        <div class="bg-white dark:bg-dark-secondary rounded-xl border border-gray-200 dark:border-gray-800 p-5 xl:sticky xl:top-24">
                            <h2 class="text-lg font-semibold mb-4">Order Summary</h2>

                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-text-secondary">Unique items</span>
                                    <span class="font-semibold">{{ $cartItems->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-text-secondary">Total quantity</span>
                                    <span class="font-semibold">{{ $lineItems }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-text-secondary">Average line</span>
                                    <span class="font-semibold">${{ number_format($avgLine, 2) }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                                <span class="text-base font-semibold">Total</span>
                                <span class="text-2xl font-bold text-orange">${{ number_format($total, 2) }}</span>
                            </div>

                            <a href="{{ route('checkout') }}" class="h-11 w-full inline-flex items-center justify-center bg-orange hover:bg-orange-light text-white rounded-lg font-semibold transition">
                                Proceed to Checkout
                            </a>
                            <a href="{{ route('products.index') }}" class="h-10 mt-2 w-full inline-flex items-center justify-center backend-btn-muted text-sm">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
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
