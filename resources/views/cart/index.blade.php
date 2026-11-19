<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Cart" title="Shopping cart" subtitle="Adjust quantities, remove items, then check out." />

            @if($cartItems->count() > 0)
                <div class="grid gap-6 lg:grid-cols-12">
                    <div class="space-y-3 lg:col-span-8">
                        @foreach($cartItems as $item)
                            <article class="backend-card">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <a href="{{ route('products.show', $item->product) }}" class="flex min-w-0 flex-1 items-center gap-4">
                                        @if($item->product->image_path)
                                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                                        @else
                                            <div class="h-20 w-20 shrink-0 rounded-xl bg-stone-100"></div>
                                        @endif
                                        <div class="min-w-0">
                                            <h3 class="truncate font-extrabold text-stone-950">{{ $item->product->name }}</h3>
                                            <p class="mt-1 text-sm text-stone-600">GH₵{{ number_format($item->product->price, 2) }} each</p>
                                            @php
                                                $p = $item->product;
                                                $problem = ($p->status !== 'approved' || $p->listing_state !== 'active') ? 'No longer available. Please remove it.'
                                                    : ($p->stock_quantity < 1 ? 'Sold out. Please remove it.'
                                                    : ($item->quantity > $p->stock_quantity ? "Only {$p->stock_quantity} left. Lower the quantity." : null));
                                            @endphp
                                            @if($problem)
                                                <p class="mt-1 text-xs font-bold text-red-600">{{ $problem }}</p>
                                            @else
                                                <p class="text-xs text-stone-500">{{ $p->stock_quantity }} in stock</p>
                                            @endif
                                        </div>
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="sr-only" for="qty-{{ $item->id }}">Quantity</label>
                                            <input id="qty-{{ $item->id }}" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" class="backend-field w-20 text-center">
                                            <button type="submit" class="btn-secondary px-4">Update</button>
                                        </form>
                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-11 items-center rounded-xl px-3 text-sm font-bold text-red-600 transition hover:bg-red-50">Remove</button>
                                        </form>
                                    </div>

                                    <div class="sm:w-32 sm:text-right">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[.12em] text-stone-500">Line total</p>
                                        <p class="text-lg font-extrabold text-stone-950">GH₵{{ number_format($item->product->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="lg:col-span-4">
                        <div class="backend-card lg:sticky lg:top-24">
                            <h2 class="text-lg font-extrabold text-stone-950">Order summary</h2>
                            <dl class="mt-4 space-y-2 text-sm">
                                <div class="flex justify-between"><dt class="text-stone-600">Items</dt><dd class="font-bold">{{ $cartItems->sum('quantity') }}</dd></div>
                            </dl>
                            <div class="mt-4 flex items-center justify-between border-t border-stone-200 pt-4">
                                <span class="font-extrabold text-stone-950">Total</span>
                                <span class="text-2xl font-extrabold tracking-[-.04em] text-stone-950">GH₵{{ number_format($total, 2) }}</span>
                            </div>
                            @php
                                $cartBlocked = $cartItems->contains(fn ($i) => $i->product->status !== 'approved' || $i->product->listing_state !== 'active' || $i->quantity > $i->product->stock_quantity);
                            @endphp
                            @if($cartBlocked)
                                <p class="mt-5 rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700">Fix the items marked in red to check out.</p>
                                <span class="btn-primary mt-3 w-full cursor-not-allowed opacity-50" aria-disabled="true">Proceed to checkout</span>
                            @else
                                <a href="{{ route('checkout') }}" class="btn-primary mt-5 w-full">Proceed to checkout</a>
                            @endif
                            <a href="{{ route('products.index') }}" class="btn-secondary mt-2 w-full">Continue shopping</a>
                        </div>
                    </aside>
                </div>
            @else
                <x-empty-state title="Your cart is empty" message="Browse the marketplace and add something you like." action-label="Continue shopping" :action-href="route('products.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
