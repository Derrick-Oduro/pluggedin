<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            @php
                $itemCount = $cartItems->sum('quantity');
                $problemFor = function ($item) {
                    $p = $item->product;
                    if ($p->status !== 'approved' || $p->listing_state !== 'active') {
                        return 'No longer available. Please remove it.';
                    }
                    if ($p->stock_quantity < 1) {
                        return 'Sold out. Please remove it.';
                    }

                    return $item->quantity > $p->stock_quantity ? "Only {$p->stock_quantity} left. Lower the quantity." : null;
                };
                $cartBlocked = $cartItems->contains(fn ($i) => $problemFor($i) !== null);
            @endphp

            <x-page-header badge="Cart" title="Shopping cart"
                :subtitle="$cartItems->isEmpty() ? 'Nothing here yet.' : $itemCount.' '.Str::plural('item', $itemCount).' ready for checkout.'" />

            @if($cartItems->count() > 0)
                <div class="grid gap-6 lg:grid-cols-[1fr_22rem] lg:items-start xl:gap-8">
                    <div>
                        <div class="mb-3 flex items-center justify-between text-sm">
                            <p class="font-bold text-stone-500">{{ $cartItems->count() }} {{ Str::plural('product', $cartItems->count()) }}</p>
                            <a href="{{ route('products.index') }}" class="btn-link">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                                Keep shopping
                            </a>
                        </div>

                        <div class="space-y-3">
                            @foreach($cartItems as $item)
                                @php
                                    $p = $item->product;
                                    $problem = $problemFor($item);
                                    $max = max(1, $p->stock_quantity);
                                @endphp
                                <article class="flex gap-4 rounded-2xl border bg-white p-3 shadow-[0_12px_35px_-26px_rgba(41,30,23,0.22)] transition sm:gap-5 sm:p-4 {{ $problem ? 'border-red-300 ring-1 ring-red-200' : 'border-stone-200 hover:border-stone-300' }}">
                                    <a href="{{ route('products.show', $p) }}" class="relative h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-stone-100 sm:h-32 sm:w-32">
                                        @if($p->image_path)
                                            <img src="{{ asset('storage/' . $p->image_path) }}" alt="{{ $p->name }}" class="h-full w-full object-cover {{ $problem ? 'opacity-60 grayscale' : '' }}" loading="lazy">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-stone-300">
                                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z" /></svg>
                                            </div>
                                        @endif
                                    </a>

                                    <div class="flex min-w-0 flex-1 flex-col">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-brand-deep">
                                                    {{ $p->category?->name }}@if($p->conditionLabel()) · {{ $p->conditionLabel() }}@endif
                                                </p>
                                                <a href="{{ route('products.show', $p) }}" class="mt-1 line-clamp-2 font-extrabold leading-snug text-stone-950 hover:text-brand-deep sm:text-lg">{{ $p->name }}</a>
                                                <p class="mt-0.5 text-sm text-stone-500">GH₵{{ number_format($p->price, 2) }} each</p>
                                            </div>

                                            <form action="{{ route('cart.remove', $item) }}" method="POST" class="shrink-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-stone-400 transition hover:bg-red-50 hover:text-red-600" aria-label="Remove {{ $p->name }}" title="Remove">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                </button>
                                            </form>
                                        </div>

                                        @if($problem)
                                            <p class="mt-2 inline-flex w-fit items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>{{ $problem }}
                                            </p>
                                        @elseif($p->stock_quantity <= 3)
                                            <p class="mt-2 inline-flex w-fit items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Only {{ $p->stock_quantity }} left
                                            </p>
                                        @else
                                            <p class="mt-2 inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>In stock
                                            </p>
                                        @endif

                                        <div class="mt-auto flex flex-wrap items-end justify-between gap-3 pt-3">
                                            {{-- Quantity saves as soon as it changes. --}}
                                            <form action="{{ route('cart.update', $item) }}" method="POST" x-data="{ q: {{ $item->quantity }}, max: {{ $max }} }" class="inline-flex items-center rounded-full border border-stone-300 bg-white p-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg font-bold text-stone-700 transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-30"
                                                    :disabled="q <= 1" @click="q--; $nextTick(() => $root.requestSubmit())" aria-label="Decrease quantity">−</button>
                                                <label class="sr-only" for="qty-{{ $item->id }}">Quantity</label>
                                                <input id="qty-{{ $item->id }}" type="number" name="quantity" x-model.number="q" value="{{ $item->quantity }}" min="1" max="{{ $max }}"
                                                    @change="$root.requestSubmit()"
                                                    class="w-10 border-0 bg-transparent p-0 text-center text-sm font-extrabold text-stone-950 [appearance:textfield] focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg font-bold text-stone-700 transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-30"
                                                    :disabled="q >= max" @click="q++; $nextTick(() => $root.requestSubmit())" aria-label="Increase quantity">+</button>
                                                <noscript><button type="submit" class="ml-1 px-2 text-xs font-bold text-brand-deep">Update</button></noscript>
                                            </form>

                                            <p class="text-right">
                                                <span class="block text-[10px] font-bold uppercase tracking-[0.14em] text-stone-400">Subtotal</span>
                                                <span class="text-lg font-extrabold tracking-tight text-stone-950 sm:text-xl">GH₵{{ number_format($p->price * $item->quantity, 2) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <aside class="lg:sticky lg:top-24">
                        <div class="flyer-dark overflow-hidden rounded-2xl shadow-[0_24px_50px_-28px_rgba(12,10,9,0.8)]">
                            <div class="flyer-stripes h-3"></div>
                            <div class="p-6">
                                <h2 class="display-heading text-3xl text-white">Order summary</h2>

                                <dl class="mt-5 space-y-3 text-sm">
                                    <div class="flex justify-between"><dt class="text-stone-400">Items ({{ $itemCount }})</dt><dd class="font-bold text-white">GH₵{{ number_format($total, 2) }}</dd></div>
                                    <div class="flex justify-between"><dt class="text-stone-400">Delivery or pickup</dt><dd class="font-bold text-stone-300">Chosen at checkout</dd></div>
                                </dl>

                                <div class="mt-5 flex items-end justify-between border-t border-white/15 pt-5">
                                    <span class="font-extrabold text-white">Total</span>
                                    <span class="text-3xl font-extrabold tracking-[-0.04em] text-brand">GH₵{{ number_format($total, 2) }}</span>
                                </div>

                                @if($cartBlocked)
                                    <p class="mt-5 rounded-xl bg-red-500/15 px-3 py-2 text-xs font-bold text-red-200">Fix the items marked in red to check out.</p>
                                    <span class="btn-primary mt-3 h-12 w-full cursor-not-allowed opacity-50" aria-disabled="true">Proceed to checkout</span>
                                @else
                                    <a href="{{ route('checkout') }}" class="btn-primary mt-6 h-12 w-full gap-2 text-base hover:bg-white hover:text-stone-950">
                                        Proceed to checkout
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg>
                                    </a>
                                @endif

                                <p class="mt-4 flex items-center justify-center gap-2 text-xs text-stone-400">
                                    <svg class="h-4 w-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                    Secure payment with Paystack
                                </p>
                            </div>
                        </div>

                        <ul class="mt-4 grid grid-cols-2 gap-3 text-xs font-bold text-stone-700">
                            <li class="flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-3 py-3">
                                <svg class="h-5 w-5 shrink-0 text-brand-deep" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                                Delivery to you
                            </li>
                            <li class="flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-3 py-3">
                                <svg class="h-5 w-5 shrink-0 text-brand-deep" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                Or campus pickup
                            </li>
                        </ul>
                    </aside>
                </div>
            @else
                <x-empty-state title="Your cart is empty" message="Browse the marketplace and add something you like." action-label="Continue shopping" :action-href="route('products.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
