<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">Checkout</h1>

        <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Order Summary -->
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-6 border border-gray-200 dark:border-gray-800">
                <h2 class="text-2xl font-bold mb-4">Order Summary</h2>
                @foreach($cartItems as $item)
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                        <span class="text-orange">${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach

                <div class="flex justify-between py-4 text-xl font-bold">
                    <span>Total:</span>
                    <span class="text-orange">${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-6 border border-gray-200 dark:border-gray-800">
                <h2 class="text-2xl font-bold mb-4">Delivery Information</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-2">Delivery Address *</label>
                        <textarea name="delivery_address" rows="3" required
                                  class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">{{ old('delivery_address') }}</textarea>
                        @error('delivery_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-2">Phone Number *</label>
                        <input type="text" name="delivery_phone" value="{{ old('delivery_phone') }}" required
                               class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        @error('delivery_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold transition">
                Place Order
            </button>
        </form>
    </div>
</x-app-layout>
