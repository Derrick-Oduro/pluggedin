<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Orders" title="My orders" subtitle="Track delivery or pickup progress and review what you bought." />

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <article class="backend-card">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-extrabold text-stone-950">Order #{{ $order->id }}</h3>
                                    <p class="text-sm text-stone-500">{{ $order->created_at->format('M d, Y · H:i') }}</p>
                                </div>
                                <x-status-badge :status="$order->status" />
                            </div>

                            <ul class="mt-4 divide-y divide-stone-100 border-y border-stone-100">
                                @foreach($order->items as $item)
                                    <li class="flex justify-between gap-4 py-2.5 text-sm">
                                        <span class="text-stone-700">{{ $item->product->name }} <span class="text-stone-400">× {{ $item->quantity }}</span></span>
                                        <span class="font-bold text-stone-950">GH₵{{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-4 flex items-center justify-between">
                                <p class="text-sm text-stone-600">Total <span class="ml-1 text-lg font-extrabold text-stone-950">GH₵{{ number_format($order->total_price, 2) }}</span></p>
                                <a href="{{ route('orders.show', $order) }}" class="btn-link">View details →</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">{{ $orders->links() }}</div>
            @else
                <x-empty-state title="No orders yet" message="When you buy something, it will show up here with its tracking status." action-label="Start shopping" :action-href="route('products.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
