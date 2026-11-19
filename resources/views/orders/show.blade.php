<x-app-layout>
    @php
        $isPickup = $order->fulfilment_method === 'campus_pickup';
        $statusRanks = ['pending' => 1, 'confirmed' => 2, 'processing' => 3, 'ready_for_pickup' => 4, 'shipped' => 4, 'completed' => 5];
        $currentRank = $statusRanks[$order->status] ?? 0;
        $steps = [
            'pending' => ['Order placed', 'We received your order'],
            'confirmed' => ['Confirmed', 'PluggedIn is preparing it'],
            'processing' => ['Preparing', 'Your items are being packed'],
            $isPickup ? 'ready_for_pickup' : 'shipped' => [$isPickup ? 'Ready for pickup' : 'Shipped', $isPickup ? 'Waiting at the campus pickup point' : 'On the way to you'],
            'completed' => [$isPickup ? 'Collected' : 'Delivered', $isPickup ? 'Pickup completed' : 'Delivery completed'],
        ];
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            <x-page-header
                badge="Order detail"
                :title="'Order #'.$order->id"
                :subtitle="'Placed '.$order->created_at->format('M d, Y · H:i')"
                :back-href="route('orders.index')"
                back-label="Back to my orders"
            >
                <x-slot:actions>
                    <x-status-badge :status="$order->status" />
                    @if($order->status === 'pending')
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel this order?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="reason" value="Cancelled by customer">
                            <button type="submit" class="inline-flex h-11 items-center rounded-xl border border-red-200 bg-white px-4 text-sm font-bold text-red-600 transition hover:bg-red-50">Cancel order</button>
                        </form>
                    @elseif($order->status === 'confirmed')
                        <a href="{{ route('contact') }}" class="btn-secondary">Need to cancel? Contact us</a>
                    @endif
                </x-slot:actions>
            </x-page-header>

            <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                <div class="space-y-6">
                    <section class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950">Items</h2>
                        <div class="mt-4 divide-y divide-stone-100">
                            @foreach($order->items as $item)
                                <div id="review-product-{{ $item->product->id }}" class="py-4 first:pt-0 last:pb-0">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            @if($item->product->image_path)
                                                <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-16 w-16 rounded-xl object-cover">
                                            @else
                                                <div class="h-16 w-16 rounded-xl bg-stone-100"></div>
                                            @endif
                                            <div>
                                                <h3 class="font-extrabold text-stone-950">{{ $item->product->name }}</h3>
                                                <p class="text-sm text-stone-500">{{ $item->quantity }} × GH₵{{ number_format($item->price, 2) }}</p>
                                            </div>
                                        </div>
                                        <p class="font-extrabold text-stone-950">GH₵{{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>

                                    @if($canReviewOrder)
                                        @php $itemReview = $userReviews->get($item->product_id); @endphp
                                        <div class="mt-4 rounded-xl bg-stone-50 p-4">
                                            @if($itemReview)
                                                <p class="text-sm font-bold text-stone-950">Your review: {{ $itemReview->rating }}/5</p>
                                                @if($itemReview->comment)<p class="mt-1 text-sm text-stone-600">{{ $itemReview->comment }}</p>@endif
                                            @else
                                                <form action="{{ route('products.reviews.store', $item->product) }}" method="POST" class="grid gap-3 sm:grid-cols-[150px_1fr_auto]">
                                                    @csrf
                                                    <select name="rating" class="backend-field" required aria-label="Rating">
                                                        <option value="">Your rating</option>
                                                        @foreach([5, 4, 3, 2, 1] as $stars)
                                                            <option value="{{ $stars }}">{{ $stars }} {{ \Illuminate\Support\Str::plural('star', $stars) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input name="comment" class="backend-field" placeholder="Share your experience (optional)" aria-label="Comment">
                                                    <button type="submit" class="btn-primary">Post review</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-stone-200 pt-4">
                            <span class="font-extrabold text-stone-950">Total</span>
                            <span class="text-2xl font-extrabold tracking-[-.04em] text-stone-950">GH₵{{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    <section class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950">Tracking</h2>
                        @if($order->status === 'cancelled')
                            <p class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">This order was cancelled before completion.</p>
                        @elseif($order->status === 'refunded')
                            <p class="mt-4 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700">This order was refunded.</p>
                        @endif
                        <ol class="mt-5 space-y-0">
                            @foreach($steps as $statusKey => [$label, $hint])
                                @php
                                    $reached = $order->status !== 'cancelled' && ($statusRanks[$statusKey] ?? 0) <= $currentRank;
                                    $timestamp = optional($order->statusHistories->firstWhere('to_status', $statusKey))->created_at;
                                @endphp
                                <li class="relative flex gap-3 pb-5 last:pb-0">
                                    @unless($loop->last)<span @class(['absolute left-[9px] top-6 h-full w-0.5', 'bg-brand' => $reached, 'bg-stone-200' => ! $reached])></span>@endunless
                                    <span @class(['relative z-10 mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2', 'border-brand bg-brand' => $reached, 'border-stone-300 bg-white' => ! $reached])>
                                        @if($reached)<svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>@endif
                                    </span>
                                    <div>
                                        <p @class(['text-sm font-extrabold', 'text-stone-950' => $reached, 'text-stone-400' => ! $reached])>{{ $label }}</p>
                                        <p class="text-xs text-stone-500">{{ $timestamp ? $timestamp->format('M d, Y · H:i') : $hint }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </section>

                    <section class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950">{{ $isPickup ? 'Campus pickup' : 'Delivery' }}</h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div>
                                <dt class="text-[11px] font-extrabold uppercase tracking-[.12em] text-stone-500">{{ $isPickup ? 'Pickup campus' : 'Address' }}</dt>
                                <dd class="mt-1 whitespace-pre-line font-semibold text-stone-950">{{ $isPickup ? $order->pickup_campus : $order->delivery_address }}</dd>
                            </div>
                            <div>
                                <dt class="text-[11px] font-extrabold uppercase tracking-[.12em] text-stone-500">Phone</dt>
                                <dd class="mt-1 font-semibold text-stone-950">{{ $order->delivery_phone }}</dd>
                            </div>
                        </dl>
                    </section>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
