<x-app-layout>
    @php
        $tabs = ['all' => 'All', 'active' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
        $ranks = ['pending' => 1, 'confirmed' => 2, 'processing' => 3, 'ready_for_pickup' => 4, 'shipped' => 4, 'completed' => 5];
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            <x-page-header badge="Orders" title="My orders" subtitle="Track delivery or pickup, pay for unpaid orders and review what you bought.">
                <x-slot:actions>
                    <a href="{{ route('products.index') }}" class="btn-secondary">Keep shopping</a>
                </x-slot:actions>
            </x-page-header>

            @if($filterCounts['all'] > 0)
                {{-- Filter tabs --}}
                <nav class="-mx-4 mb-5 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0" aria-label="Filter orders">
                    @foreach($tabs as $key => $label)
                        <a href="{{ route('orders.index', $key === 'all' ? [] : ['filter' => $key]) }}"
                            class="inline-flex shrink-0 items-center gap-2 rounded-full border px-4 py-2 text-sm font-bold transition {{ $filter === $key ? 'border-stone-950 bg-stone-950 text-white' : 'border-stone-200 bg-white text-stone-700 hover:border-stone-400' }}"
                            @if($filter === $key) aria-current="page" @endif>
                            {{ $label }}
                            <span class="rounded-full px-1.5 text-xs {{ $filter === $key ? 'bg-brand text-stone-950' : 'bg-stone-100 text-stone-500' }}">{{ $filterCounts[$key] }}</span>
                        </a>
                    @endforeach
                </nav>
            @endif

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        @php
                            $isPickup = $order->fulfilment_method === 'campus_pickup';
                            $stopped = in_array($order->status, ['cancelled', 'refunded'], true);
                            $rank = $ranks[$order->status] ?? 0;
                            $stepLabels = ['Placed', 'Confirmed', 'Preparing', $isPickup ? 'Ready' : 'Shipped', $isPickup ? 'Collected' : 'Delivered'];
                            $nowText = match ($order->status) {
                                'pending' => $order->isCashOnDelivery() ? 'Order placed' : 'Waiting for payment',
                                'confirmed' => 'Confirmed. PluggedIn is preparing it',
                                'processing' => 'Being packed',
                                'ready_for_pickup' => 'Ready at the campus pickup point',
                                'shipped' => 'On the way to you',
                                'completed' => ($isPickup ? 'Collected' : 'Delivered').($order->completed_at ? ' '.$order->completed_at->format('M d') : ''),
                                'cancelled' => 'Cancelled'.($order->cancelled_at ? ' '.$order->cancelled_at->format('M d') : ''),
                                'refunded' => 'Refunded',
                                default => \Illuminate\Support\Str::headline($order->status),
                            };
                            $shown = $order->items->take(3);
                            $needsPayment = $order->status === 'pending' && ! $order->isCashOnDelivery();
                        @endphp
                        <article class="overflow-hidden rounded-2xl border bg-white shadow-[0_12px_35px_-26px_rgba(41,30,23,0.22)] {{ $needsPayment ? 'border-amber-300' : 'border-stone-200' }}">
                            {{-- Header --}}
                            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-stone-100 px-5 py-4">
                                <div>
                                    <a href="{{ route('orders.show', $order) }}" class="text-lg font-extrabold text-stone-950 hover:text-brand-deep">Order #{{ $order->id }}</a>
                                    <p class="mt-0.5 text-xs text-stone-500">
                                        {{ $order->created_at->format('M d, Y') }}
                                        · {{ $isPickup ? 'Campus pickup' : 'Delivery' }}
                                        · <span class="{{ $order->awaitingCash() ? 'font-bold text-brand-deep' : '' }}">{{ $order->paymentLabel() }}</span>
                                    </p>
                                </div>
                                <x-status-badge :status="$order->status" />
                            </div>

                            <div class="px-5 py-4">
                                {{-- Progress --}}
                                @if($stopped)
                                    <p class="rounded-xl bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">{{ $nowText }}.@if($order->cancellation_reason) <span class="font-normal">{{ $order->cancellation_reason }}</span>@endif</p>
                                @else
                                    <p class="text-sm font-bold text-stone-950">{{ $nowText }}</p>
                                    <div class="mt-2 grid grid-cols-5 gap-1.5" aria-hidden="true">
                                        @foreach($stepLabels as $i => $stepLabel)
                                            <div>
                                                <div class="h-1.5 rounded-full {{ $rank >= $i + 1 ? 'bg-brand' : 'bg-stone-200' }}"></div>
                                                <p class="mt-1 hidden text-[11px] font-semibold sm:block {{ $rank >= $i + 1 ? 'text-stone-800' : 'text-stone-400' }}">{{ $stepLabel }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="sr-only">Step {{ $rank }} of 5</p>
                                @endif

                                {{-- Items --}}
                                <ul class="mt-4 space-y-3">
                                    @foreach($shown as $item)
                                        <li class="flex items-center gap-3">
                                            <span class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-stone-100">
                                                @if($item->product?->image_path)
                                                    <img src="{{ asset('storage/'.$item->product->image_path) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                                @endif
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                @if($item->product)
                                                    <a href="{{ route('products.show', $item->product) }}" class="line-clamp-2 text-sm font-bold leading-snug text-stone-950 hover:text-brand-deep sm:line-clamp-1">{{ $item->product->name }}</a>
                                                @else
                                                    <span class="text-sm font-bold text-stone-400">Product no longer listed</span>
                                                @endif
                                                <span class="text-xs text-stone-500">Qty {{ $item->quantity }} · GH₵{{ number_format($item->price, 2) }} each</span>
                                            </span>
                                            <span class="shrink-0 text-sm font-extrabold text-stone-950">GH₵{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                @if($order->items->count() > $shown->count())
                                    <a href="{{ route('orders.show', $order) }}" class="mt-2 inline-block text-xs font-bold text-stone-500 hover:text-brand-deep">+ {{ $order->items->count() - $shown->count() }} more {{ \Illuminate\Support\Str::plural('item', $order->items->count() - $shown->count()) }}</a>
                                @endif
                            </div>

                            {{-- Footer --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 bg-stone-50/70 px-5 py-3.5">
                                <p class="text-sm text-stone-600">
                                    Total <span class="ml-1 text-xl font-extrabold tracking-tight text-stone-950">GH₵{{ number_format($order->total_price, 2) }}</span>
                                    @if($order->discount_amount > 0)<span class="ml-1 text-xs font-bold text-emerald-700">saved GH₵{{ number_format($order->discount_amount, 2) }}</span>@endif
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @if($needsPayment)
                                        <form action="{{ route('orders.pay', $order) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-primary h-10 px-4">Pay now</button>
                                        </form>
                                    @elseif(in_array($order->status, ['confirmed', 'completed'], true))
                                        <a href="{{ route('orders.show', $order) }}#review-product-{{ $order->items->first()?->product_id }}" class="btn-secondary h-10 px-4">&#9733; Review</a>
                                    @endif
                                    <a href="{{ route('orders.show', $order) }}" class="{{ $needsPayment ? 'btn-secondary' : 'btn-primary' }} h-10 px-4">{{ $stopped || $order->status === 'completed' ? 'View details' : 'Track order' }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">{{ $orders->links() }}</div>
            @elseif($filterCounts['all'] > 0)
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                    <p class="font-extrabold text-stone-950">No {{ strtolower($tabs[$filter]) }} orders</p>
                    <a href="{{ route('orders.index') }}" class="btn-link mt-2">See all orders</a>
                </div>
            @else
                <x-empty-state title="No orders yet" message="When you buy something, it will show up here with its tracking status." action-label="Start shopping" :action-href="route('products.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
