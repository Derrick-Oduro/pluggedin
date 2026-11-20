@php
    // Desktop link styles; the current section gets an orange underline.
    $navLink = 'flex h-full items-center gap-1 whitespace-nowrap px-2.5 text-sm font-bold transition xl:px-3';
    $idle = 'text-stone-700 hover:text-stone-950';
    $current = 'text-stone-950 underline decoration-brand decoration-[3px] underline-offset-[10px]';
    $is = fn (string ...$patterns) => request()->routeIs(...$patterns);

    $sellHref = auth()->check() ? route('products.upload.create') : route('register');
    $tradeHref = auth()->check() ? route('trade-ins.create') : route('register');
@endphp

{{-- Black promo strip --}}
<div class="bg-stone-950 text-[11px] font-bold uppercase tracking-[0.18em] text-white">
    <div class="mx-auto flex h-9 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <span class="flex items-center gap-2"><span class="text-brand">&raquo;&raquo;&raquo;</span>Free campus delivery</span>
        <span class="hidden sm:inline">Buy<span class="text-brand"> &middot; </span>Swap<span class="text-brand"> &middot; </span>Upgrade</span>
        @if($site['contact_phone'])
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $site['contact_phone']) }}" class="hidden normal-case tracking-normal hover:text-brand md:inline">{{ $site['contact_phone'] }}</a>
        @endif
    </div>
</div>

<nav x-data="{ open: false, search: false }" x-on:keydown.escape.window="open = false; search = false" class="sticky top-0 z-40 border-b-4 border-brand bg-white/95 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-[72px] items-center justify-between gap-3 xl:gap-5">
            <x-brand-logo />

            {{-- Desktop links --}}
            <div class="hidden h-full items-center lg:flex">
                {{-- Marketplace: categories --}}
                <div class="relative h-full" x-data="{ menu: false }" x-on:mouseenter="menu = true" x-on:mouseleave="menu = false" x-on:click.outside="menu = false">
                    <button type="button" class="{{ $navLink }} {{ $is('products.index', 'products.show') ? $current : $idle }}" x-on:click="menu = !menu" x-bind:aria-expanded="menu">
                        Marketplace
                        <svg class="h-4 w-4 transition" x-bind:class="menu && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="menu" x-transition.opacity.duration.150ms class="absolute left-0 top-[64px] w-[26rem] rounded-2xl border border-stone-200 bg-white p-2 shadow-xl">
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl bg-stone-950 px-4 py-3 text-white hover:bg-stone-800">
                            <span><b class="block text-sm">Browse all tech</b><span class="text-xs text-stone-400">Everything on sale right now</span></span>
                            <span class="text-brand">&rarr;</span>
                        </a>
                        <div class="mt-2 grid grid-cols-2 gap-0.5">
                            @foreach($navCategories as $category)
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="rounded-xl px-3 py-2.5 text-sm font-bold text-stone-800 hover:bg-brand-tint hover:text-brand-deep">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Sell: list or trade in --}}
                <div class="relative h-full" x-data="{ menu: false }" x-on:mouseenter="menu = true" x-on:mouseleave="menu = false" x-on:click.outside="menu = false">
                    <button type="button" class="{{ $navLink }} {{ $is('products.upload.*', 'trade-ins.*') ? $current : $idle }}" x-on:click="menu = !menu" x-bind:aria-expanded="menu">
                        Sell
                        <svg class="h-4 w-4 transition" x-bind:class="menu && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="menu" x-transition.opacity.duration.150ms class="absolute left-0 top-[64px] w-72 rounded-2xl border border-stone-200 bg-white p-2 shadow-xl">
                        <a href="{{ $sellHref }}" class="block rounded-xl px-3 py-3 hover:bg-brand-tint">
                            <b class="block text-sm text-stone-950">List an item</b>
                            <span class="text-xs text-stone-500">Sell to other students on the marketplace.</span>
                        </a>
                        <a href="{{ $tradeHref }}" class="block rounded-xl px-3 py-3 hover:bg-brand-tint">
                            <b class="block text-sm text-stone-950">Trade in a device</b>
                            <span class="text-xs text-stone-500">Get an offer from PluggedIn for your old tech.</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('services.index') }}" class="{{ $navLink }} {{ $is('services.*') ? $current : $idle }}">Repairs</a>
                <a href="{{ route('campuses') }}" class="{{ $navLink }} {{ $is('campuses') ? $current : $idle }}">Campus partners</a>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">
                {{-- Inline search on wide screens --}}
                <form action="{{ route('products.index') }}" method="GET" role="search" class="relative hidden xl:block">
                    <label for="nav-search" class="sr-only">Search the marketplace</label>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="m21 21-4.3-4.3M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" /></svg>
                    <input id="nav-search" type="search" name="q" value="{{ request()->routeIs('products.index') ? request('q') : '' }}" placeholder="Search laptops, RAM…"
                        class="h-10 w-56 rounded-xl border border-stone-200 bg-stone-50 pl-9 pr-3 text-sm text-stone-900 placeholder:text-stone-400 transition-all focus:w-64 focus:border-brand focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-soft/60 2xl:w-64 2xl:focus:w-72">
                </form>

                {{-- Search button below xl: opens the search row --}}
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-600 transition hover:border-brand-soft hover:bg-brand-tint hover:text-brand-deep xl:hidden"
                    x-on:click="search = !search; open = false; $nextTick(() => search && $refs.searchInput.focus())" x-bind:aria-expanded="search" aria-label="Search">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m21 21-4.3-4.3M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" /></svg>
                </button>

                <div class="hidden items-center gap-2 lg:flex">
                    <x-header-actions />
                </div>

                {{-- Phone and tablet --}}
                <div class="flex items-center gap-2 lg:hidden">
                    <x-header-actions compact />
                    <button type="button" class="flex h-10 w-10 items-center justify-center rounded-xl bg-stone-950 text-white" x-on:click="open = !open; search = false" x-bind:aria-expanded="open" aria-label="Menu">
                        <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                        <svg x-cloak x-show="open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Search row (phones, tablets and smaller laptops) --}}
    <div x-cloak x-show="search" x-transition.opacity.duration.150ms class="border-t border-stone-200 bg-white xl:hidden">
        <form action="{{ route('products.index') }}" method="GET" role="search" class="mx-auto flex max-w-7xl gap-2 px-4 py-3 sm:px-6 lg:px-8">
            <label for="nav-search-row" class="sr-only">Search the marketplace</label>
            <input id="nav-search-row" x-ref="searchInput" type="search" name="q" value="{{ request()->routeIs('products.index') ? request('q') : '' }}" placeholder="Search laptops, RAM, phones…" class="backend-field flex-1">
            <button type="submit" class="btn-primary px-4">Search</button>
        </form>
    </div>

    <div x-cloak x-show="open">
        <x-mobile-menu />
    </div>
</nav>
