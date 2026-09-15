<x-dropdown align="right" width="64">
    <x-slot name="trigger">
        <button class="flex h-10 items-center gap-2 rounded-xl border border-stone-200 px-3 text-sm font-bold text-stone-800 transition hover:bg-stone-50">
            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-tint text-[11px] font-extrabold text-brand-deep">{{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
            <span class="max-w-24 truncate">{{ Auth::user()->name }}</span>
            <svg class="h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
        </button>
    </x-slot>
    <x-slot name="content">
        <div class="px-4 py-3"><p class="text-sm font-extrabold text-stone-950">{{ Auth::user()->name }}</p><p class="mt-0.5 text-xs text-stone-500">{{ Auth::user()->email }}</p></div>
        <div class="border-t border-stone-100 py-2"><p class="px-4 pb-1 text-[10px] font-extrabold uppercase tracking-[.14em] text-stone-400">Account</p><x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link><x-dropdown-link :href="route('orders.index')">My orders</x-dropdown-link><x-dropdown-link :href="route('wishlist.index')">Saved items</x-dropdown-link><x-dropdown-link :href="route('settings.index')">Settings</x-dropdown-link></div>
        <div class="border-t border-stone-100 py-2"><p class="px-4 pb-1 text-[10px] font-extrabold uppercase tracking-[.14em] text-stone-400">Selling & Shop</p><x-dropdown-link :href="route('products.upload.index')">My listings</x-dropdown-link><x-dropdown-link :href="route('trade-ins.index')">My trade-ins</x-dropdown-link><x-dropdown-link :href="route('shops.dashboard')">My Shop</x-dropdown-link></div>
        @if(auth()->user()->isAdmin())
            <div class="border-t border-stone-100 py-2"><p class="px-4 pb-1 text-[10px] font-extrabold uppercase tracking-[.14em] text-stone-400">Administration</p><x-dropdown-link :href="auth()->user()->hasRole('super-admin') ? route('superadmin.dashboard') : route('admin.dashboard')">Admin workspace</x-dropdown-link></div>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 py-2">@csrf<x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log out</x-dropdown-link></form>
    </x-slot>
</x-dropdown>
