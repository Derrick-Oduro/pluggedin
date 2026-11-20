{{-- Phone/tablet menu: everything the desktop header and account dropdown offer. --}}
@php
    $link = 'flex items-center justify-between rounded-xl px-3 py-3 text-[15px] font-bold text-stone-800 hover:bg-brand-tint hover:text-brand-deep';
    $heading = 'px-3 pb-1 pt-4 text-[10px] font-extrabold uppercase tracking-[.16em] text-stone-400';
@endphp
<div class="max-h-[calc(100vh-110px)] overflow-y-auto border-t border-stone-200 bg-white px-4 pb-5 pt-2 lg:hidden">
    @auth
        <div class="flex items-center gap-3 rounded-xl bg-stone-50 px-3 py-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand text-sm font-extrabold text-stone-950">{{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
            <span class="min-w-0"><span class="block truncate text-sm font-extrabold text-stone-950">{{ Auth::user()->name }}</span><span class="block truncate text-xs text-stone-500">{{ Auth::user()->email }}</span></span>
        </div>
    @else
        <div class="grid grid-cols-2 gap-2 pt-2">
            <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
            <a href="{{ route('register') }}" class="btn-primary">Join PluggedIn</a>
        </div>
    @endauth

    <p class="{{ $heading }}">Shop</p>
    <a href="{{ route('products.index') }}" class="{{ $link }}">Browse all tech <span class="text-brand">&rarr;</span></a>
    @if($navCategories->isNotEmpty())
        <div class="grid grid-cols-2 gap-x-1">
            @foreach($navCategories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-stone-700 hover:bg-brand-tint hover:text-brand-deep">{{ $category->name }}</a>
            @endforeach
        </div>
    @endif
    <a href="{{ route('services.index') }}" class="{{ $link }}">Repairs</a>
    <a href="{{ route('campuses') }}" class="{{ $link }}">Campus partners</a>

    <p class="{{ $heading }}">Sell</p>
    <a href="{{ auth()->check() ? route('products.upload.create') : route('register') }}" class="{{ $link }}">List an item</a>
    <a href="{{ auth()->check() ? route('trade-ins.create') : route('register') }}" class="{{ $link }}">Trade in a device</a>

    @auth
        <p class="{{ $heading }}">My account</p>
        <a href="{{ route('dashboard') }}" class="{{ $link }}">Dashboard</a>
        <a href="{{ route('orders.index') }}" class="{{ $link }}">My orders</a>
        <a href="{{ route('wishlist.index') }}" class="{{ $link }}">Saved items</a>
        <a href="{{ route('products.upload.index') }}" class="{{ $link }}">My listings</a>
        <a href="{{ route('trade-ins.index') }}" class="{{ $link }}">My trade-ins</a>
        <a href="{{ route('shops.dashboard') }}" class="{{ $link }}">My shop</a>
        <a href="{{ route('settings.index') }}" class="{{ $link }}">Settings</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ auth()->user()->hasRole('super-admin') ? route('superadmin.dashboard') : route('admin.dashboard') }}" class="{{ $link }}">Admin workspace</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-stone-100 pt-2">
            @csrf
            <button type="submit" class="{{ $link }} w-full text-red-600 hover:bg-red-50 hover:text-red-700">Log out</button>
        </form>
    @endauth

    <p class="{{ $heading }}">Help</p>
    <a href="{{ route('contact') }}" class="{{ $link }}">Contact us</a>
</div>
