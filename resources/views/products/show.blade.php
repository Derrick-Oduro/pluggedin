<x-app-layout>
    @php
        $isCommunityListing = (bool) $product->is_user_uploaded;
        $seller = $product->uploader;
        $sellerName = $product->shop?->name ?? $seller?->name ?? 'PluggedIn Marketplace';
        $sellerVerified = ! $isCommunityListing || $seller?->seller_verified_at;
        $specs = $product->specs ?? [];
        $knownIssues = $specs['known_issues'] ?? null;
        $highlights = \App\Support\ProductSpecs::highlights($product->category, $specs);
        $specGroups = \App\Support\ProductSpecs::display($product->category, $specs, ['known_issues']);
        $gallery = $product->galleryImages();
        $conditionLabel = $product->conditionLabel() ?? ($isCommunityListing ? 'Pre-owned' : 'New');
        $inStock = $product->stock_quantity > 0 && $product->listing_state === 'active';
        $avgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : null;
        $isOwner = auth()->id() && auth()->id() === $product->uploaded_by;
        $shareLink = session('referral_link') ?? ($referralLink ? route('products.referrals.track', [$product, $referralLink->code]) : null);
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <nav class="flex flex-wrap items-center gap-1.5 text-xs font-bold text-stone-500" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="hover:text-brand-deep">Marketplace</a>
                <span class="text-stone-300">/</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-brand-deep">{{ $product->category->name }}</a>
                <span class="text-stone-300">/</span>
                <span class="truncate text-stone-800">{{ $product->name }}</span>
            </nav>

            @if($product->status !== 'approved')
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                    Preview: this listing is <strong>{{ $product->status }}</strong> and isn't visible to buyers yet.
                </div>
            @endif

            {{-- ===== Top: gallery + buy box ===== --}}
            <div class="mt-6 grid gap-8 lg:grid-cols-[1.1fr_.9fr] lg:gap-12">
                <div x-data="{ active: 0, images: @js(array_map(fn ($p) => asset('storage/'.$p), $gallery)) }">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-stone-200 bg-stone-100 shadow-[0_18px_45px_-32px_rgba(41,30,23,.35)]">
                        @if($gallery)
                            <img x-bind:src="images[active]" src="{{ asset('storage/'.$gallery[0]) }}" alt="{{ $product->name }}" class="h-full w-full object-contain bg-white">
                            @if(count($gallery) > 1)
                                <button type="button" x-on:click="active = (active - 1 + images.length) % images.length" class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-stone-800 shadow hover:bg-white" aria-label="Previous photo"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg></button>
                                <button type="button" x-on:click="active = (active + 1) % images.length" class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-stone-800 shadow hover:bg-white" aria-label="Next photo"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg></button>
                                <span class="absolute bottom-3 right-3 rounded-full bg-stone-950/75 px-2.5 py-1 text-xs font-bold text-white" x-text="(active + 1) + ' / ' + images.length">1 / {{ count($gallery) }}</span>
                            @endif
                        @else
                            <div class="flex h-full items-center justify-center p-10 text-center text-sm font-bold text-stone-400">No photos yet for this listing.</div>
                        @endif
                    </div>
                    @if(count($gallery) > 1)
                        <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                            @foreach($gallery as $i => $image)
                                <button type="button" x-on:click="active = {{ $i }}" x-bind:class="active === {{ $i }} ? 'ring-2 ring-brand' : 'opacity-70 hover:opacity-100'" class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-stone-200 bg-white" aria-label="Photo {{ $i + 1 }}">
                                    <img src="{{ asset('storage/'.$image) }}" alt="" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:py-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="brand-badge">{{ $product->category->name }}</span>
                        <x-status-badge :status="$product->condition === 'new' ? 'active' : 'confirmed'">{{ $conditionLabel }}</x-status-badge>
                    </div>

                    <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-[-.05em] text-stone-950 sm:text-4xl">{{ $product->name }}</h1>

                    @if($avgRating)
                        <a href="#reviews" class="mt-2 inline-flex items-center gap-1.5 text-sm font-bold text-stone-600 hover:text-brand-deep">
                            <span class="text-amber-500">@for($i = 1; $i <= 5; $i++){{ $i <= round($avgRating) ? '★' : '☆' }}@endfor</span>
                            {{ $avgRating }} · {{ $reviews->count() }} {{ \Illuminate\Support\Str::plural('review', $reviews->count()) }}
                        </a>
                    @endif

                    <p class="mt-4 text-3xl font-extrabold tracking-[-.05em] text-stone-950">GH₵{{ number_format($product->price, 2) }}</p>
                    <p @class(['mt-1 text-sm font-bold', 'text-emerald-700' => $inStock, 'text-red-700' => ! $inStock])>
                        @if($product->listing_state === 'sold') Sold
                        @elseif(! $inStock) Currently unavailable
                        @elseif($product->stock_quantity <= 3) Only {{ $product->stock_quantity }} left
                        @else In stock
                        @endif
                    </p>

                    @if($highlights)
                        <dl class="mt-6 grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-stone-200 bg-stone-200">
                            @foreach($highlights as $row)
                                <div class="bg-white px-4 py-3">
                                    <dt class="text-[10px] font-extrabold uppercase tracking-[.12em] text-stone-500">{{ $row['label'] }}</dt>
                                    <dd class="mt-0.5 text-sm font-extrabold text-stone-950">{{ $row['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        <a href="#specs" class="btn-link mt-2 text-xs">See all specifications ↓</a>
                    @endif

                    {{-- Buy actions --}}
                    @auth
                        @unless($isOwner)
                            <div class="mt-6 flex gap-3">
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-[1.5]">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn-primary h-12 w-full" @disabled(! $inStock)>{{ $inStock ? 'Add to cart' : 'Unavailable' }}</button>
                                </form>
                                @if($isWishlisted && $wishlistItemId)
                                    <form action="{{ route('wishlist.destroy', $wishlistItemId) }}" method="POST" class="flex-1">@csrf @method('DELETE')<button class="btn-secondary h-12 w-full">♥ Saved</button></form>
                                @else
                                    <form action="{{ route('wishlist.store', $product) }}" method="POST" class="flex-1">@csrf<button class="btn-secondary h-12 w-full">♡ Save</button></form>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('products.upload.edit', $product) }}" class="btn-secondary mt-6 h-12 w-full">This is your listing · Edit</a>
                        @endunless
                    @else
                        <a href="{{ route('login') }}" class="btn-primary mt-6 h-12 w-full">Log in to buy</a>
                    @endauth

                    {{-- Delivery & payment --}}
                    <ul class="mt-6 space-y-2.5 text-sm text-stone-700">
                        <li class="flex gap-3"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg><span><b class="text-stone-950">Delivery or campus pickup</b>, chosen at checkout.</span></li>
                        <li class="flex gap-3"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg><span><b class="text-stone-950">Secure payment</b> with Paystack (card or mobile money).</span></li>
                        @if(filled($specs['warranty'] ?? null))
                            <li class="flex gap-3"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg><span><b class="text-stone-950">Warranty:</b> {{ $specs['warranty'] }}</span></li>
                        @endif
                    </ul>

                    {{-- Seller --}}
                    <div class="mt-6 flex items-center gap-3 rounded-2xl border border-stone-200 bg-white p-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-tint text-base font-extrabold text-brand-deep">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($sellerName, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-extrabold uppercase tracking-[.14em] text-stone-500">Sold by</p>
                            <p class="flex items-center gap-1.5 truncate font-extrabold text-stone-950">
                                {{ $sellerName }}
                                @if($sellerVerified)<svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="currentColor" aria-label="Verified seller"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>@endif
                            </p>
                            <p class="text-xs text-stone-500">{{ $isCommunityListing ? ($sellerVerified ? 'Verified seller · listing reviewed by PluggedIn' : 'Listing reviewed by PluggedIn before going live') : 'Supplied directly by PluggedIn' }}</p>
                        </div>
                        @if($product->shop && $product->shop->status === 'approved')
                            <a href="{{ route('shops.show', $product->shop) }}" class="btn-link shrink-0 text-xs">Visit shop →</a>
                        @elseif($isCommunityListing && $seller)
                            <a href="{{ route('sellers.show', $seller) }}" class="btn-link shrink-0 text-xs">More from seller →</a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== Details ===== --}}
            <div class="mt-12 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                <div class="space-y-6">
                    <section class="backend-card sm:p-6">
                        <h2 class="text-lg font-extrabold text-stone-950">About this item</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-stone-700">{{ $product->description }}</p>

                        @if($knownIssues)
                            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-[.12em] text-amber-800">Seller's condition notes</p>
                                <p class="mt-1 whitespace-pre-line text-sm text-amber-900">{{ $knownIssues }}</p>
                            </div>
                        @endif
                    </section>

                    @if($specGroups)
                        <section id="specs" class="backend-card scroll-mt-24 sm:p-6">
                            <h2 class="text-lg font-extrabold text-stone-950">Specifications</h2>
                            <div class="mt-5 space-y-6">
                                @foreach($specGroups as $groupName => $rows)
                                    <div>
                                        <h3 class="text-[11px] font-extrabold uppercase tracking-[.14em] text-stone-500">{{ $groupName }}</h3>
                                        <dl class="mt-2 divide-y divide-stone-100 rounded-xl border border-stone-100">
                                            @foreach($rows as $row)
                                                <div @class(['px-4 py-2.5 text-sm', 'grid grid-cols-[minmax(0,2fr)_minmax(0,3fr)] gap-4' => ! $row['long']])>
                                                    <dt class="font-semibold text-stone-500">{{ $row['label'] }}</dt>
                                                    <dd @class(['font-bold text-stone-950', 'mt-1 whitespace-pre-line' => $row['long']])>{{ $row['value'] }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="space-y-6">
                    <section class="backend-card sm:p-6">
                        <h2 class="text-lg font-extrabold text-stone-950">At a glance</h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-stone-500">Condition</dt><dd class="text-right font-bold text-stone-950">{{ $conditionLabel }}</dd></div>
                            @if(filled($specs['brand'] ?? null))<div class="flex justify-between gap-4"><dt class="text-stone-500">Brand</dt><dd class="text-right font-bold text-stone-950">{{ $specs['brand'] }}</dd></div>@endif
                            @if(filled($specs['model'] ?? null))<div class="flex justify-between gap-4"><dt class="text-stone-500">Model</dt><dd class="text-right font-bold text-stone-950">{{ $specs['model'] }}</dd></div>@endif
                            <div class="flex justify-between gap-4"><dt class="text-stone-500">Category</dt><dd class="text-right font-bold text-stone-950">{{ $product->category->name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-stone-500">Listed</dt><dd class="text-right font-bold text-stone-950">{{ $product->created_at->format('M j, Y') }}</dd></div>
                        </dl>
                        @if($product->whats_included)
                            <div class="mt-5 border-t border-stone-100 pt-4">
                                <p class="text-[11px] font-extrabold uppercase tracking-[.14em] text-stone-500">What's in the box</p>
                                <p class="mt-1.5 whitespace-pre-line text-sm font-semibold text-stone-950">{{ $product->whats_included }}</p>
                            </div>
                        @endif
                    </section>

                    <section class="rounded-2xl bg-stone-950 p-6 text-white">
                        <p class="text-xs font-extrabold uppercase tracking-[.14em] text-brand-soft">Need advice?</p>
                        <h2 class="mt-2 text-lg font-extrabold">Not sure it's compatible?</h2>
                        <p class="mt-2 text-sm leading-6 text-white/70">Tell us what device you have and what you want to improve.</p>
                        <a href="{{ route('contact') }}" class="mt-4 inline-flex text-sm font-extrabold text-brand-soft hover:text-white">Ask the PluggedIn team →</a>
                    </section>

                    @auth
                        @unless($isOwner)
                            <section class="backend-card space-y-4 sm:p-6">
                                @if($product->status === 'approved')
                                    <div>
                                        <h2 class="text-sm font-extrabold text-stone-950">Share & earn points</h2>
                                        @if($shareLink)
                                            <div class="mt-2 flex gap-2" x-data="{ copied: false }">
                                                <input readonly value="{{ $shareLink }}" class="backend-field text-xs" aria-label="Your referral link" x-on:focus="$el.select()">
                                                <button type="button" class="btn-secondary shrink-0 px-3" x-on:click="navigator.clipboard.writeText(@js($shareLink)); copied = true; setTimeout(() => copied = false, 2000)" x-text="copied ? 'Copied' : 'Copy'">Copy</button>
                                            </div>
                                        @else
                                            <p class="mt-1 text-xs text-stone-500">Get a personal link. You earn points when someone buys through it.</p>
                                            <form action="{{ route('products.referrals.store', $product) }}" method="POST" class="mt-2">@csrf<button class="btn-secondary w-full">Get my share link</button></form>
                                        @endif
                                    </div>
                                @endif

                                <details class="group border-t border-stone-100 pt-4">
                                    <summary class="cursor-pointer list-none text-xs font-bold text-stone-500 hover:text-red-600">⚑ Report this listing</summary>
                                    <form action="{{ route('products.report.store', $product) }}" method="POST" class="mt-3 space-y-2">
                                        @csrf
                                        <select name="reason" required class="backend-field" aria-label="Reason">
                                            <option value="">Choose a reason</option>
                                            <option value="suspicious_listing">Looks like a scam</option>
                                            <option value="incorrect_details">Details are wrong</option>
                                            <option value="prohibited_item">Prohibited or stolen item</option>
                                            <option value="other">Something else</option>
                                        </select>
                                        <textarea name="details" rows="2" maxlength="1000" class="backend-field h-auto py-2" placeholder="Anything else we should know (optional)" aria-label="Details"></textarea>
                                        <button class="btn-danger w-full">Send report</button>
                                    </form>
                                </details>
                            </section>
                        @endunless
                    @endauth
                </aside>
            </div>

            {{-- ===== Reviews ===== --}}
            <section id="reviews" class="mt-12 backend-card scroll-mt-24 sm:p-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="text-lg font-extrabold text-stone-950">Reviews</h2>
                    @if($avgRating)<p class="text-sm font-bold text-stone-600"><span class="text-amber-500">★</span> {{ $avgRating }} out of 5 · {{ $reviews->count() }} {{ \Illuminate\Support\Str::plural('review', $reviews->count()) }}</p>@endif
                </div>
                @auth
                    @if($canReview)
                        <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="mt-5 grid gap-3 border-b border-stone-100 pb-5 sm:grid-cols-[160px_1fr_auto]">
                            @csrf
                            <select name="rating" class="backend-field" required aria-label="Rating"><option value="">Your rating</option>@foreach([5, 4, 3, 2, 1] as $stars)<option value="{{ $stars }}">{{ $stars }} {{ \Illuminate\Support\Str::plural('star', $stars) }}</option>@endforeach</select>
                            <input name="comment" class="backend-field" placeholder="Share your experience (optional)" aria-label="Comment">
                            <button class="btn-primary">Post review</button>
                        </form>
                    @endif
                @endauth
                <div class="mt-5 space-y-4">
                    @forelse($reviews as $review)
                        <article class="border-b border-stone-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-extrabold text-stone-950">{{ $review->user->name }} <span class="ml-1 text-xs font-semibold text-stone-400">{{ $review->created_at->format('M j, Y') }}</span></p>
                                <p class="text-sm text-amber-500" aria-label="{{ $review->rating }} out of 5">@for($i = 1; $i <= 5; $i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor</p>
                            </div>
                            @if($review->comment)<p class="mt-2 text-sm leading-6 text-stone-600">{{ $review->comment }}</p>@endif
                        </article>
                    @empty
                        <p class="text-sm text-stone-600">No reviews yet. Buyers can review after their order is confirmed.</p>
                    @endforelse
                </div>
            </section>

            @if($relatedProducts->count())
                <section class="mt-12">
                    <div class="flex items-end justify-between">
                        <div><span class="brand-badge">Keep exploring</span><h2 class="mt-3 text-2xl font-extrabold tracking-[-.04em] text-stone-950">Similar items</h2></div>
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="btn-link">More in {{ $product->category->name }} →</a>
                    </div>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($relatedProducts as $related)
                            <a href="{{ route('products.show', $related) }}" class="group overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-28px_rgba(41,30,23,.4)]">
                                <div class="h-36 bg-stone-100">@if($related->image_path)<img src="{{ asset('storage/'.$related->image_path) }}" alt="{{ $related->name }}" class="h-full w-full object-cover transition group-hover:scale-105">@endif</div>
                                <div class="p-4"><p class="text-xs font-bold text-brand">{{ $related->category->name }}</p><h3 class="mt-1 font-extrabold text-stone-950">{{ $related->name }}</h3><p class="mt-3 font-extrabold text-stone-950">GH₵{{ number_format($related->price, 2) }}</p></div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
</x-app-layout>
