<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            @if (! $shop)
                <div class="backend-card py-14 text-center">
                    <span class="brand-badge">PluggedIn Shops</span>
                    <h1 class="mt-4 text-3xl font-extrabold tracking-[-.04em] text-stone-950">Ready to open a shop?</h1>
                    <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-stone-600">Create a verified campus-tech storefront, manage inventory, and grow beyond personal listing limits.</p>
                    <a href="{{ route('shops.create') }}" class="mt-6 backend-btn-primary">Open a Shop</a>
                </div>
            @else
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="brand-badge">My Shop</p>
                        <h1 class="mt-4 text-3xl font-extrabold tracking-[-.04em] text-stone-950">{{ $shop->name }}</h1>
                        <p class="mt-2 text-sm text-stone-600">{{ $shop->campus ?: 'PluggedIn Marketplace' }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1.5 text-sm font-extrabold {{ $shop->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($shop->status === 'suspended' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($shop->status) }}</span>
                </div>

                @if ($shop->status === 'pending')
                    <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-6">
                        <h2 class="font-extrabold text-stone-950">Your application is under review.</h2>
                        <p class="mt-2 text-sm leading-6 text-stone-600">PluggedIn is checking your shop details. Once approved, you can publish inventory and receive a public shop link.</p>
                        @if ($shop->admin_notes)
                            <p class="mt-4 rounded-xl bg-white p-3 text-sm text-stone-700"><b>Note from PluggedIn:</b> {{ $shop->admin_notes }}</p>
                        @endif
                    </div>
                @elseif ($shop->status === 'suspended')
                    <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-6">
                        <h2 class="font-extrabold text-stone-950">Your shop is currently suspended.</h2>
                        <p class="mt-2 text-sm text-stone-600">{{ $shop->admin_notes ?: 'Please contact PluggedIn support for next steps.' }}</p>
                    </div>
                @else
                    <div class="mt-8 grid gap-5 md:grid-cols-3">
                        <div class="backend-card"><p class="text-xs font-extrabold uppercase tracking-[.12em] text-stone-500">Shop plan</p><p class="mt-3 text-xl font-extrabold text-stone-950">{{ str_replace('_', ' ', ucfirst($shop->plan)) }}</p></div>
                        <div class="backend-card"><p class="text-xs font-extrabold uppercase tracking-[.12em] text-stone-500">Products</p><p class="mt-3 text-xl font-extrabold text-stone-950">{{ $shop->products_count }}</p></div>
                        <div class="backend-card"><p class="text-xs font-extrabold uppercase tracking-[.12em] text-stone-500">Public shop</p><a href="{{ route('shops.show', $shop) }}" class="mt-3 inline-flex text-sm font-extrabold text-brand hover:text-brand-deep">View storefront →</a></div>
                    </div>
                    <div class="mt-6 backend-card"><div class="flex flex-wrap items-center justify-between gap-4"><div><h2 class="text-xl font-extrabold text-stone-950">Shop inventory</h2><p class="mt-2 text-sm leading-6 text-stone-600">Add products for moderation, then they appear in your public storefront after approval.</p></div><a href="{{ route('shops.products.create') }}" class="backend-btn-primary">Add product</a></div><div class="mt-6 divide-y divide-stone-100">@forelse($products as $product)<div class="flex items-center justify-between py-3"><div><p class="font-bold text-stone-950">{{ $product->name }}</p><p class="text-xs text-stone-500">{{ $product->category->name }} · GH₵{{ number_format($product->price, 2) }}</p></div><span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-bold text-stone-600">{{ ucfirst($product->status) }}</span></div>@empty<p class="py-4 text-sm text-stone-600">No products yet. Add your first item to start building your storefront.</p>@endforelse</div></div>
                @endif
            @endif
        </div>
    </section>
</x-app-layout>
