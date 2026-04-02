<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Checkout</p>
                <h1 class="text-5xl sm:text-6xl font-bold">Place Your Order</h1>
            </div>

            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                    <h2 class="text-2xl font-bold mb-4">Order Summary</h2>
                    @foreach($cartItems as $item)
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700 text-sm">
                            <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                            <span class="text-orange font-medium">${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach

                    <div class="flex justify-between py-2 text-sm border-t border-gray-200 dark:border-gray-700 mt-3 pt-3">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal ?? $total, 2) }}</span>
                    </div>

                    @if(($discountAmount ?? 0) > 0)
                        <div class="flex justify-between py-1 text-sm text-green-600 dark:text-green-400">
                            <span>
                                Discount
                                @if($autoCampaign)
                                    ({{ $autoCampaign->name }})
                                @endif
                            </span>
                            <span>- ${{ number_format($discountAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between py-3 text-xl font-bold border-t border-gray-200 dark:border-gray-700 mt-2">
                        <span>Total:</span>
                        <span class="text-orange">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                    <h2 class="text-2xl font-bold mb-4">Promotion</h2>
                    <div>
                        <label class="block text-sm mb-2">Promo Code (optional)</label>
                        <input type="text" name="promo_code" value="{{ old('promo_code') }}" placeholder="Enter promo code"
                               class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange uppercase">
                        @error('promo_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @if($autoCampaign)
                            <p class="text-xs text-gray-600 dark:text-text-secondary mt-2">Active automatic discount: {{ $autoCampaign->name }} ({{ $autoCampaign->discount_percent }}% off)</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                    <h2 class="text-2xl font-bold mb-4">Delivery Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm mb-2">Delivery Address *</label>
                            <textarea name="delivery_address" rows="3" required
                                      class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-2">Phone Number *</label>
                            <input type="text" name="delivery_phone" value="{{ old('delivery_phone') }}" required
                                   class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
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
    </section>
</x-app-layout>
