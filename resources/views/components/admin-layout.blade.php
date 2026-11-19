{{-- Shell for every /admin page: sidebar on large screens, scrollable tab bar on small ones. --}}
@php
    $tabs = [
        ['admin.dashboard', 'Dashboard', 'admin.dashboard'],
        ['admin.products.pending', 'Moderation queue', 'admin.products.pending'],
        ['admin.products.index', 'Products', 'admin.products.index|admin.products.create|admin.products.edit|admin.products.show'],
        ['admin.reviews.index', 'Reviews', 'admin.reviews.*'],
        ['admin.orders.index', 'Orders', 'admin.orders.*'],
        ['admin.bookings.index', 'Bookings', 'admin.bookings.*'],
        ['admin.referrals.index', 'Referrals', 'admin.referrals.*'],
    ];
    $isActive = fn ($patterns) => request()->routeIs(...explode('|', $patterns));
@endphp

<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <div class="grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
                <aside>
                    <nav class="backend-sidebar flex gap-1 overflow-x-auto lg:sticky lg:top-24 lg:flex-col lg:overflow-visible" aria-label="Admin">
                        <p class="hidden px-3 pb-2 pt-1 text-[11px] font-extrabold uppercase tracking-[.14em] text-stone-400 lg:block">Admin</p>
                        @foreach ($tabs as [$route, $label, $patterns])
                            <a href="{{ route($route) }}" @class(['backend-tab shrink-0 whitespace-nowrap', 'backend-tab-active' => $isActive($patterns)])>{{ $label }}</a>
                        @endforeach
                        @if (auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('superadmin.dashboard') }}" class="backend-tab shrink-0 whitespace-nowrap lg:mt-2 lg:border-t lg:border-stone-100 lg:pt-3">Super admin panel →</a>
                        @endif
                    </nav>
                </aside>

                <div class="min-w-0 space-y-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
