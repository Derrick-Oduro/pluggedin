<x-app-layout>
    @php
        $statusLabel = fn ($status) => \Illuminate\Support\Str::headline((string) $status);
        $cardTitle = 'text-lg font-extrabold text-stone-950';
        $cardLink = 'text-sm font-extrabold text-brand-deep hover:text-stone-950';

        $attention = collect();
        foreach ($unpaidOrders as $order) {
            $attention->push(['tone' => 'amber', 'title' => "Order #{$order->id} is waiting for payment", 'text' => 'GH₵'.number_format($order->total_price, 2).'. Unpaid orders are cancelled after an hour.', 'url' => route('orders.show', $order), 'action' => 'Pay now']);
        }
        foreach ($cashDueOrders as $order) {
            $when = $order->fulfilment_method === 'campus_pickup' ? 'at pickup' : 'on delivery';
            $attention->push(['tone' => 'brand', 'title' => 'Have GH₵'.number_format($order->total_price, 2)." ready {$when}", 'text' => "Order #{$order->id} · cash or Mobile Money.", 'url' => route('orders.show', $order), 'action' => 'View order']);
        }
        foreach ($rejectedUploads as $product) {
            $attention->push(['tone' => 'red', 'title' => "“{$product->name}” wasn't approved", 'text' => $product->admin_review_comment ?: 'Edit the listing and send it again.', 'url' => route('products.upload.edit', $product), 'action' => 'Fix listing']);
        }
        if ($pendingReviewItems->isNotEmpty()) {
            $attention->push(['tone' => 'stone', 'title' => $pendingReviewItems->count().' '.\Illuminate\Support\Str::plural('purchase', $pendingReviewItems->count()).' to review', 'text' => 'Help other students choose. It takes a minute.', 'url' => '#reviews', 'action' => 'Write reviews']);
        }
        $toneClasses = [
            'amber' => 'border-l-amber-400 bg-amber-50/70',
            'brand' => 'border-l-brand bg-brand-tint',
            'red' => 'border-l-red-500 bg-red-50/70',
            'stone' => 'border-l-stone-400 bg-white',
        ];
        $pointsProgress = $pointsThreshold > 0 ? min(100, round($pointsBalance / $pointsThreshold * 100)) : 100;
        $uploadProgress = $uploadLimit > 0 ? min(100, round($uploadsUsed / $uploadLimit * 100)) : 0;
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10 space-y-6">

            {{-- Welcome --}}
            <div class="flyer-dark relative overflow-hidden rounded-3xl px-6 py-7 shadow-[0_24px_50px_-30px_rgba(12,10,9,0.9)] sm:px-8 sm:py-9">
                {{-- Striped ring like the flyer; the two circles share a centre. --}}
                <div class="flyer-stripes pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full opacity-90 sm:-right-10 sm:-top-20 sm:h-72 sm:w-72"></div>
                <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-stone-950 sm:right-0 sm:-top-10 sm:h-52 sm:w-52"></div>

                <div class="relative">
                    <div class="pr-20 sm:pr-48">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-brand">My dashboard</p>
                        <h1 class="display-heading mt-2 text-4xl text-white sm:text-5xl">Hey, {{ $firstName }}.</h1>
                        <p class="mt-2 max-w-md text-sm text-stone-300">
                            @if($openOrdersCount > 0)
                                You have {{ $openOrdersCount }} {{ \Illuminate\Support\Str::plural('order', $openOrdersCount) }} in progress.
                            @else
                                Nothing on the way right now. Find your next upgrade.
                            @endif
                        </p>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('products.index') }}" class="btn-primary hover:bg-white hover:text-stone-950">Browse marketplace</a>
                        <a href="{{ route('products.upload.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/25 px-5 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:border-brand hover:text-brand">Sell an item</a>
                    </div>
                </div>

                <div class="relative mt-7 grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
                    @foreach([
                        ['Orders in progress', $openOrdersCount, route('orders.index')],
                        ['In your cart', $cartCount, route('cart.index')],
                        ['Saved items', $savedCount, route('wishlist.index')],
                        ['Points', number_format($pointsBalance), '#points'],
                    ] as [$label, $value, $href])
                        <a href="{{ $href }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 transition hover:border-brand/60 hover:bg-white/10">
                            <span class="block text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $value }}</span>
                            <span class="mt-0.5 block text-xs font-semibold text-stone-400">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Needs your attention --}}
            @if($attention->isNotEmpty())
                <section aria-labelledby="attention-title">
                    <h2 id="attention-title" class="mb-3 flex items-center gap-2 text-sm font-extrabold uppercase tracking-[0.14em] text-stone-500">
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1.5 text-[11px] text-stone-950">{{ $attention->count() }}</span>
                        Needs your attention
                    </h2>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach($attention as $item)
                            <a href="{{ $item['url'] }}" class="group flex min-w-0 items-center justify-between gap-4 rounded-2xl border border-l-4 border-stone-200 px-4 py-3.5 transition hover:-translate-y-0.5 hover:shadow-[0_12px_30px_-20px_rgba(41,30,23,0.5)] {{ $toneClasses[$item['tone']] }}">
                                <span class="min-w-0">
                                    <span class="block font-extrabold text-stone-950">{{ $item['title'] }}</span>
                                    <span class="mt-0.5 block truncate text-sm text-stone-600">{{ $item['text'] }}</span>
                                </span>
                                <span class="shrink-0 text-sm font-extrabold text-brand-deep group-hover:text-stone-950">{{ $item['action'] }} &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_22rem] lg:items-start">
                {{-- Main column --}}
                <div class="min-w-0 space-y-6">
                    {{-- Recent orders --}}
                    <section class="backend-card p-0">
                        <div class="flex items-center justify-between px-5 pb-3 pt-5">
                            <h2 class="{{ $cardTitle }}">Recent orders</h2>
                            @if($orders->isNotEmpty())<a href="{{ route('orders.index') }}" class="{{ $cardLink }}">All orders &rarr;</a>@endif
                        </div>
                        @forelse($orders as $order)
                            @php
                                $first = $order->items->first()?->product;
                                $names = $order->items->map(fn ($i) => $i->product?->name)->filter();
                            @endphp
                            <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-4 border-t border-stone-100 px-5 py-3.5 transition hover:bg-stone-50">
                                <span class="relative h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-stone-100">
                                    @if($first?->image_path)
                                        <img src="{{ asset('storage/'.$first->image_path) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                    @endif
                                    @if($order->items->count() > 1)
                                        <span class="absolute bottom-0.5 right-0.5 rounded-md bg-stone-950/80 px-1 text-[10px] font-bold text-white">+{{ $order->items->count() - 1 }}</span>
                                    @endif
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="line-clamp-2 font-bold leading-snug text-stone-950 sm:line-clamp-1">{{ $names->first() ?? 'Order #'.$order->id }}{{ $names->count() > 1 ? ' and '.($names->count() - 1).' more' : '' }}</span>
                                    <span class="mt-0.5 block text-xs text-stone-500">
                                        #{{ $order->id }} · {{ $order->created_at->format('M d, Y') }}
                                        @if($order->awaitingCash())<span class="font-bold text-brand-deep"> · {{ $order->paymentLabel() }}</span>@endif
                                    </span>
                                </span>
                                <span class="flex shrink-0 flex-col items-end gap-1">
                                    <span class="font-extrabold text-stone-950">GH₵{{ number_format($order->total_price, 2) }}</span>
                                    <x-status-badge :status="$order->status" />
                                </span>
                            </a>
                        @empty
                            <div class="border-t border-stone-100 px-5 py-10 text-center">
                                <p class="font-bold text-stone-950">No orders yet</p>
                                <p class="mt-1 text-sm text-stone-500">Laptops, phones, RAM and more, checked before they go live.</p>
                                <a href="{{ route('products.index') }}" class="btn-primary mt-4">Start shopping</a>
                            </div>
                        @endforelse
                    </section>

                    {{-- Reviews to write --}}
                    @if($pendingReviewItems->isNotEmpty())
                        <section id="reviews" class="backend-card scroll-mt-28">
                            <div class="flex items-center justify-between">
                                <h2 class="{{ $cardTitle }}">Rate what you bought</h2>
                                <a href="{{ route('reviews.index') }}" class="{{ $cardLink }}">My reviews &rarr;</a>
                            </div>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                @foreach($pendingReviewItems as $item)
                                    <a href="{{ route('orders.show', $item->order) }}#review-product-{{ $item->product->id }}" class="group flex min-w-0 items-center gap-3 rounded-xl border border-stone-200 p-3 transition hover:border-brand">
                                        <span class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-stone-100">
                                            @if($item->product->image_path)<img src="{{ asset('storage/'.$item->product->image_path) }}" alt="" class="h-full w-full object-cover" loading="lazy">@endif
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-stone-950">{{ $item->product->name }}</span>
                                            <span class="text-xs font-extrabold text-brand-deep group-hover:text-stone-950">&#9733; Write a review</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Activity --}}
                    <section class="backend-card">
                        <h2 class="{{ $cardTitle }}">Latest updates</h2>
                        @if($activity->isNotEmpty())
                            <ol class="relative mt-4 space-y-4 border-l-2 border-stone-100 pl-5">
                                @foreach($activity as $event)
                                    <li class="relative">
                                        <span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full border-2 border-white {{ $loop->first ? 'bg-brand' : 'bg-stone-300' }}"></span>
                                        <a href="{{ $event['url'] }}" class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-0.5 hover:text-brand-deep">
                                            <span class="text-sm text-stone-700"><b class="text-stone-950">{{ $event['label'] }}</b> is now <b class="text-stone-950">{{ strtolower($statusLabel($event['status'])) }}</b></span>
                                            <span class="text-xs text-stone-400">{{ $event['at']->diffForHumans() }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p class="mt-3 text-sm text-stone-500">Order and repair updates will show up here.</p>
                        @endif
                    </section>
                </div>

                {{-- Side column --}}
                <aside class="min-w-0 space-y-6">
                    {{-- Points --}}
                    <section id="points" class="backend-card scroll-mt-28">
                        <div class="flex items-start justify-between">
                            <h2 class="{{ $cardTitle }}">Points</h2>
                            <span class="brand-badge">Referrals</span>
                        </div>
                        <p class="mt-3 text-4xl font-extrabold tracking-tight text-stone-950">{{ number_format($pointsBalance) }}</p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full bg-brand" style="width: {{ $pointsProgress }}%"></div></div>
                        <p class="mt-2 text-xs text-stone-500">
                            @if($pointsBalance >= $pointsThreshold)
                                You can redeem your points. <a href="{{ route('contact') }}" class="font-bold text-brand-deep">Ask us how</a>.
                            @else
                                {{ number_format($pointsThreshold - $pointsBalance) }} more to reach {{ number_format($pointsThreshold) }} and redeem. Share product links to earn.
                            @endif
                        </p>
                        @if($pointsTransactions->isNotEmpty())
                            <ul class="mt-4 space-y-2 border-t border-stone-100 pt-3 text-sm">
                                @foreach($pointsTransactions as $txn)
                                    <li class="flex justify-between gap-3">
                                        <span class="truncate text-stone-600">{{ $txn->description ?: $statusLabel($txn->type) }}</span>
                                        <span class="shrink-0 font-extrabold {{ $txn->points >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $txn->points >= 0 ? '+' : '' }}{{ $txn->points }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($referralLinks->isNotEmpty())
                            <div class="mt-4 border-t border-stone-100 pt-3">
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-stone-400">Your top shared links</p>
                                <ul class="mt-2 space-y-1.5 text-sm">
                                    @foreach($referralLinks as $link)
                                        <li class="flex justify-between gap-3">
                                            <span class="truncate text-stone-700">{{ $link->product?->name ?? 'Removed product' }}</span>
                                            <span class="shrink-0 text-xs text-stone-500">{{ $link->clicks }} clicks · <b class="text-stone-800">{{ $link->conversions }} sold</b></span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </section>

                    {{-- Selling --}}
                    <section class="backend-card">
                        <div class="flex items-center justify-between">
                            <h2 class="{{ $cardTitle }}">Selling</h2>
                            <a href="{{ route('products.upload.index') }}" class="{{ $cardLink }}">My listings &rarr;</a>
                        </div>
                        <div class="mt-3 flex items-baseline justify-between text-sm">
                            <span class="text-stone-600">Listings this month</span>
                            <span class="font-extrabold text-stone-950">{{ $uploadsUsed }} / {{ $uploadLimit }}</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full {{ $uploadsRemaining > 0 ? 'bg-stone-950' : 'bg-red-500' }}" style="width: {{ $uploadProgress }}%"></div></div>
                        <div class="mt-3 flex gap-2 text-xs font-bold">
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-800">{{ $liveUploadsCount }} live</span>
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-amber-800">{{ $pendingUploadsCount }} in review</span>
                        </div>
                        @if($uploadedProducts->isNotEmpty())
                            <ul class="mt-4 divide-y divide-stone-100 border-t border-stone-100">
                                @foreach($uploadedProducts as $product)
                                    <li>
                                        <a href="{{ route('products.show', $product) }}" class="flex items-center justify-between gap-3 py-2.5 hover:text-brand-deep">
                                            <span class="truncate text-sm font-semibold text-stone-800">{{ $product->name }}</span>
                                            <x-status-badge :status="$product->status">{{ ['approved' => 'Live', 'pending' => 'In review', 'rejected' => 'Not approved'][$product->status] ?? $statusLabel($product->status) }}</x-status-badge>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($uploadsRemaining > 0)
                            <a href="{{ route('products.upload.create') }}" class="btn-secondary mt-4 w-full">List an item</a>
                        @else
                            <p class="mt-4 rounded-xl bg-stone-100 px-3 py-2 text-xs text-stone-600">You've used this month's listings. More open on the 1st.</p>
                        @endif
                    </section>

                    {{-- Shortcuts --}}
                    <nav class="backend-card p-2" aria-label="Account shortcuts">
                        @foreach([
                            ['Repairs & bookings', route('bookings.index'), $activeBookingsCount ? $activeBookingsCount.' active' : null],
                            ['My trade-ins', route('trade-ins.index'), null],
                            ['Notifications', route('notifications.index'), $unreadCount ? $unreadCount.' new' : null],
                            ['Saved items', route('wishlist.index'), $savedCount ?: null],
                            ['Settings', route('settings.index'), null],
                        ] as [$label, $href, $meta])
                            <a href="{{ $href }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-bold text-stone-800 transition hover:bg-brand-tint hover:text-brand-deep">
                                {{ $label }}
                                <span class="flex items-center gap-2">
                                    @if($meta)<span class="rounded-full bg-brand-tint px-2 py-0.5 text-[11px] font-extrabold text-brand-deep">{{ $meta }}</span>@endif
                                    <span class="text-stone-300">&rsaquo;</span>
                                </span>
                            </a>
                        @endforeach
                    </nav>
                </aside>
            </div>
        </div>
    </section>
</x-app-layout>
