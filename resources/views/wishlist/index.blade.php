<x-app-layout>
    @php
        $availability = function ($product) {
            if (! $product || $product->status !== 'approved' || $product->listing_state !== 'active') {
                return ['buyable' => false, 'label' => 'No longer available', 'class' => 'bg-stone-200 text-stone-600'];
            }
            if ($product->stock_quantity < 1) {
                return ['buyable' => false, 'label' => 'Sold out', 'class' => 'bg-red-100 text-red-700'];
            }
            if ($product->stock_quantity <= 3) {
                return ['buyable' => true, 'label' => "Only {$product->stock_quantity} left", 'class' => 'bg-amber-100 text-amber-800'];
            }

            return ['buyable' => true, 'label' => 'In stock', 'class' => 'bg-emerald-100 text-emerald-800'];
        };
        $buyableCount = $items->getCollection()->filter(fn ($item) => $availability($item->product)['buyable'])->count();
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Saved" title="Saved items" :subtitle="$items->total() ? $items->total().' '.\Illuminate\Support\Str::plural('product', $items->total()).' on your shortlist.' : 'Your shortlist is empty.'">
                <x-slot:actions>
                    @if($buyableCount > 1)
                        <form action="{{ route('wishlist.move-all-to-cart') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary">Add all to cart ({{ $buyableCount }})</button>
                        </form>
                    @endif
                    <a href="{{ route('products.index') }}" class="btn-secondary">Keep shopping</a>
                </x-slot:actions>
            </x-page-header>

            @if($items->count())
                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($items as $item)
                        @php
                            $product = $item->product;
                            $state = $availability($product);
                        @endphp
                        <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_12px_35px_-26px_rgba(41,30,23,0.22)] transition hover:-translate-y-0.5 hover:border-stone-300">
                            {{-- Photo --}}
                            <a href="{{ $product ? route('products.show', $product) : '#' }}" class="relative block aspect-square overflow-hidden bg-stone-100">
                                @if($product?->image_path)
                                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03] {{ $state['buyable'] ? '' : 'opacity-60 grayscale' }}" loading="lazy">
                                @else
                                    <span class="flex h-full items-center justify-center text-stone-300">
                                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z" /></svg>
                                    </span>
                                @endif
                                <span class="absolute bottom-2 left-2 rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide {{ $state['class'] }}">{{ $state['label'] }}</span>
                            </a>

                            {{-- Unsave (filled heart) --}}
                            <form action="{{ route('wishlist.destroy', $item) }}" method="POST" class="absolute right-2 top-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-brand shadow-md transition hover:scale-105 hover:text-red-600" aria-label="Remove {{ $product?->name ?? 'item' }} from saved items" title="Remove from saved">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" /></svg>
                                </button>
                            </form>

                            <div class="flex flex-1 flex-col p-3 sm:p-4">
                                <p class="truncate text-[10px] font-bold uppercase tracking-[0.12em] text-brand-deep">
                                    {{ $product?->category?->name ?? 'Unavailable' }}@if($product?->conditionLabel()) · {{ $product->conditionLabel() }}@endif
                                </p>
                                @if($product)
                                    <a href="{{ route('products.show', $product) }}" class="mt-1 line-clamp-2 text-sm font-extrabold leading-snug text-stone-950 hover:text-brand-deep sm:text-base">{{ $product->name }}</a>
                                    <p class="mt-2 text-lg font-extrabold tracking-tight text-stone-950">GH₵{{ number_format($product->price, 2) }}</p>
                                @else
                                    <p class="mt-1 text-sm font-extrabold text-stone-400">This listing was removed</p>
                                @endif
                                <p class="mt-0.5 text-[11px] text-stone-400">Saved {{ $item->created_at->gt(now()->subMinute()) ? 'just now' : $item->created_at->diffForHumans() }}</p>

                                <div class="mt-auto pt-3">
                                    @if($state['buyable'])
                                        <form action="{{ route('wishlist.move-to-cart', $item) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-primary h-10 w-full px-3 text-xs sm:text-sm">Move to cart</button>
                                        </form>
                                    @elseif($product && $product->category)
                                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="btn-secondary h-10 w-full px-3 text-xs sm:text-sm">See similar</a>
                                    @else
                                        <a href="{{ route('products.index') }}" class="btn-secondary h-10 w-full px-3 text-xs sm:text-sm">Browse instead</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">{{ $items->links() }}</div>
            @else
                <x-empty-state title="No saved items yet" message="Tap ♡ Save on any product and it'll wait here until you're ready to buy." action-label="Browse products" :action-href="route('products.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
