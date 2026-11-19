<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Saved" title="Saved items" :subtitle="$items->total().' '.\Illuminate\Support\Str::plural('product', $items->total()).' on your shortlist.'">
                <x-slot:actions>
                    <a href="{{ route('products.index') }}" class="btn-secondary">Continue shopping</a>
                </x-slot:actions>
            </x-page-header>

            @if($items->count())
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($items as $item)
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_12px_35px_-26px_rgba(41,30,23,0.22)]">
                            <a href="{{ $item->product ? route('products.show', $item->product) : '#' }}" class="block aspect-[4/3] bg-stone-100">
                                @if($item->product?->image_path)
                                    <img src="{{ asset('storage/'.$item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                @endif
                            </a>
                            <div class="flex flex-1 flex-col p-4">
                                <p class="text-xs font-bold text-brand">{{ $item->product?->category?->name ?? 'Uncategorized' }}</p>
                                <h2 class="mt-1 font-extrabold text-stone-950">{{ $item->product?->name ?? 'Product unavailable' }}</h2>
                                @if($item->product)
                                    <p class="mt-2 text-lg font-extrabold text-stone-950">GH₵{{ number_format($item->product->price, 2) }}</p>
                                @endif
                                <div class="mt-auto flex gap-2 pt-4">
                                    @if($item->product)
                                        <form action="{{ route('wishlist.move-to-cart', $item) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button class="btn-primary w-full">Move to cart</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('wishlist.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex h-11 items-center rounded-xl px-3 text-sm font-bold text-red-600 transition hover:bg-red-50">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">{{ $items->links() }}</div>
            @else
                <x-empty-state title="No saved items yet" message="Save products you like and they'll wait here until you're ready to buy." action-label="Browse products" :action-href="route('products.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
