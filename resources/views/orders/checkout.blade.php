<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            <x-page-header badge="Checkout" title="Place your order" subtitle="Choose delivery or campus pickup, then pay securely with Paystack." :back-href="route('cart.index')" back-label="Back to cart" />

            <form action="{{ route('orders.store') }}" method="POST" x-data="{ method: @js(old('fulfilment_method', 'delivery')) }" class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                @csrf

                <div class="space-y-6">
                    @foreach(['cart', 'payment'] as $problem)
                        @error($problem)
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                                {{ $message }}
                                @if($problem === 'cart')<a href="{{ route('cart.index') }}" class="ml-1 underline">Go to cart</a>@endif
                            </div>
                        @enderror
                    @endforeach

                    <section class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950">How would you like to receive it?</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach(['delivery' => ['Delivery', 'Send the order to your address.'], 'campus_pickup' => ['Campus pickup', 'Collect from the PluggedIn point on campus.']] as $value => [$label, $hint])
                                <label class="flex cursor-pointer gap-3 rounded-xl border p-4 transition" x-bind:class="method === @js($value) ? 'border-brand bg-brand-tint' : 'border-stone-200 hover:border-stone-300'">
                                    <input type="radio" name="fulfilment_method" value="{{ $value }}" x-model="method" class="mt-0.5 text-brand-deep focus:ring-brand">
                                    <span><span class="block font-extrabold text-stone-950">{{ $label }}</span><span class="mt-1 block text-sm text-stone-600">{{ $hint }}</span></span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-5 space-y-4">
                            <div x-show="method === 'campus_pickup'" x-cloak>
                                <label for="pickup_campus" class="mb-1.5 block text-sm font-bold text-stone-800">Campus for pickup</label>
                                <input id="pickup_campus" type="text" name="pickup_campus" value="{{ old('pickup_campus') }}" placeholder="e.g. University of Ghana" class="backend-field">
                                @error('pickup_campus')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div x-show="method === 'delivery'">
                                <label for="delivery_address" class="mb-1.5 block text-sm font-bold text-stone-800">Delivery address</label>
                                <textarea id="delivery_address" name="delivery_address" rows="3" class="backend-field h-auto py-3" placeholder="Hall, room, landmark or full address">{{ old('delivery_address') }}</textarea>
                                @error('delivery_address')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="delivery_phone" class="mb-1.5 block text-sm font-bold text-stone-800">Phone number <span class="text-brand-deep">*</span></label>
                                <input id="delivery_phone" type="tel" name="delivery_phone" value="{{ old('delivery_phone') }}" required class="backend-field">
                                @error('delivery_phone')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950">Promo code</h2>
                        <input type="text" name="promo_code" value="{{ old('promo_code') }}" placeholder="Enter promo code (optional)" aria-label="Promo code" class="backend-field mt-4 uppercase">
                        @error('promo_code')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        @if($autoCampaign)
                            <p class="mt-2 text-xs text-stone-600">Automatic discount applied: {{ $autoCampaign->name }} ({{ $autoCampaign->discount_percent }}% off)</p>
                        @endif
                    </section>
                </div>

                <aside>
                    <div class="backend-card lg:sticky lg:top-24">
                        <h2 class="text-lg font-extrabold text-stone-950">Order summary</h2>
                        <ul class="mt-4 divide-y divide-stone-100 text-sm">
                            @foreach($cartItems as $item)
                                <li class="flex justify-between gap-4 py-2.5">
                                    <span class="text-stone-700">{{ $item->product->name }} <span class="text-stone-400">× {{ $item->quantity }}</span></span>
                                    <span class="font-bold text-stone-950">GH₵{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <dl class="mt-3 space-y-2 border-t border-stone-200 pt-3 text-sm">
                            <div class="flex justify-between"><dt class="text-stone-600">Subtotal</dt><dd class="font-bold">GH₵{{ number_format($subtotal ?? $total, 2) }}</dd></div>
                            @if(($discountAmount ?? 0) > 0)
                                <div class="flex justify-between text-emerald-700"><dt>Discount @if($autoCampaign)({{ $autoCampaign->name }})@endif</dt><dd class="font-bold">− GH₵{{ number_format($discountAmount, 2) }}</dd></div>
                            @endif
                        </dl>
                        <div class="mt-3 flex items-center justify-between border-t border-stone-200 pt-3">
                            <span class="font-extrabold text-stone-950">Total</span>
                            <span class="text-2xl font-extrabold tracking-[-.04em] text-stone-950">GH₵{{ number_format($total, 2) }}</span>
                        </div>
                        <button type="submit" class="btn-primary mt-5 w-full">Continue to Paystack</button>
                        <p class="mt-3 text-center text-xs text-stone-500">You'll be redirected to Paystack to complete payment.</p>
                    </div>
                </aside>
            </form>
        </div>
    </section>
</x-app-layout>
