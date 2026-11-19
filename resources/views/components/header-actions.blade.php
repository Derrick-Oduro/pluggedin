@auth
    @php
        $unreadCount = Auth::user()->unreadNotifications()->count();
        $cartCount = Auth::user()->cartItems()->count();
    @endphp
    <a href="{{ route('notifications.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-600 transition hover:border-brand-soft hover:bg-brand-tint hover:text-brand-deep" aria-label="Notifications">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" /></svg>
        @if($unreadCount > 0)<span class="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-brand px-1 text-[10px] font-extrabold text-stone-950">{{ min($unreadCount, 9) }}{{ $unreadCount > 9 ? '+' : '' }}</span>@endif
    </a>
    <a href="{{ route('cart.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-600 transition hover:border-brand-soft hover:bg-brand-tint hover:text-brand-deep" aria-label="Cart">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3.75h1.4a1.5 1.5 0 0 1 1.45 1.18L5.7 7.5m0 0h12.1a1.5 1.5 0 0 1 1.47 1.82l-1.2 6a1.5 1.5 0 0 1-1.47 1.18H8a1.5 1.5 0 0 1-1.45-1.18L5.7 7.5Z" /></svg>
        @if($cartCount > 0)<span class="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-stone-950 px-1 text-[10px] font-extrabold text-white">{{ min($cartCount, 9) }}{{ $cartCount > 9 ? '+' : '' }}</span>@endif
    </a>
    <x-account-menu />
@else
    <a href="{{ route('login') }}" class="px-3 text-sm font-bold text-stone-700">Log in</a><a href="{{ route('register') }}" class="backend-btn-primary h-10 px-4">Join PluggedIn</a>
@endauth
