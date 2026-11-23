@props(['title' => null])

@php
    $q = \App\Support\SuperAdminQueues::counts();

    // Sidebar: [label, route, active pattern, badge count, icon path]
    $groups = [
        'Overview' => [
            ['Dashboard', route('superadmin.dashboard'), 'superadmin.dashboard', null, 'M3.75 12 12 3.75 20.25 12M5.25 10.5V20.25h4.5v-6h4.5v6h4.5V10.5'],
        ],
        'To do' => [
            ['Listing approvals', route('admin.products.pending'), 'admin.products.pending', $q['listing_approvals'], 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ['Listing reports', route('superadmin.product-reports.index'), 'superadmin.product-reports.*', $q['reports'], 'M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5'],
            ['Trade-ins', route('superadmin.trade-ins.index'), 'superadmin.trade-ins.*', $q['trade_ins_to_quote'] + $q['trade_ins_to_complete'], 'M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5'],
            ['Shop applications', route('superadmin.shops.index'), 'superadmin.shops.*', $q['shops'], 'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z'],
            ['Messages', route('superadmin.contact-messages.index'), 'superadmin.contact-messages.*', $q['messages'], 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75'],
        ],
        'People' => [
            ['Users & roles', route('superadmin.users.index'), 'superadmin.users.*', null, 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
            ['Seller verification', route('superadmin.sellers.index'), 'superadmin.sellers.*', null, 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
        ],
        'Store setup' => [
            ['Repairs & services', route('superadmin.services.index'), 'superadmin.services.*', null, 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437'],
            ['Categories', route('superadmin.categories.index'), 'superadmin.categories.*', null, 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z M6 6h.008v.008H6V6Z'],
            ['Homepage & discounts', route('superadmin.marketing.index'), 'superadmin.marketing.*', null, 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46'],
            ['Site settings', route('superadmin.settings.index'), 'superadmin.settings.*', null, 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
            ['Audit log', route('superadmin.audit-logs.index'), 'superadmin.audit-logs.*', null, 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
        ],
    ];
    $todoTotal = collect($groups['To do'])->sum(fn ($item) => $item[3] ?? 0);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' · ' : '' }}Super admin · {{ config('app.name', 'PluggedIn') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|lilita-one:400&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 font-sans text-stone-950 antialiased" x-data="{ menu: false }" x-on:keydown.escape.window="menu = false">
        {{-- Phone/tablet backdrop --}}
        <div x-cloak x-show="menu" x-on:click="menu = false" class="fixed inset-0 z-30 bg-stone-950/40 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-stone-950 text-stone-300 transition-transform lg:translate-x-0"
            x-bind:class="menu && 'translate-x-0'" aria-label="Super admin menu">
            <div class="flex items-center justify-between gap-2 px-5 pb-4 pt-5">
                <a href="{{ route('superadmin.dashboard') }}" class="block">
                    <span class="display-heading text-2xl text-white">PluggedIn</span>
                    <span class="mt-0.5 block text-[10px] font-bold uppercase tracking-[0.18em] text-brand">Super admin</span>
                </a>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-lg text-stone-400 hover:bg-white/10 hover:text-white lg:hidden" x-on:click="menu = false" aria-label="Close menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" /></svg>
                </button>
            </div>
            <div class="flyer-stripes h-1.5 shrink-0"></div>

            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-5">
                @foreach($groups as $group => $items)
                    <div>
                        <p class="px-3 pb-1.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-stone-500">{{ $group }}</p>
                        @foreach($items as [$label, $href, $pattern, $count, $icon])
                            @php($active = request()->routeIs($pattern))
                            <a href="{{ $href }}" class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold transition {{ $active ? 'bg-white text-stone-950' : 'hover:bg-white/10 hover:text-white' }}" @if($active) aria-current="page" @endif>
                                <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-brand-deep' : 'text-stone-500 group-hover:text-brand' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                                <span class="flex-1 truncate">{{ $label }}</span>
                                @if($count)
                                    <span class="rounded-full bg-brand px-2 py-0.5 text-[11px] font-extrabold text-stone-950">{{ $count > 99 ? '99+' : $count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-3">
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-white/5 px-3 py-2 text-center text-xs font-bold text-stone-300 hover:bg-white/10 hover:text-white">Orders admin</a>
                    <a href="{{ route('home') }}" class="rounded-lg bg-white/5 px-3 py-2 text-center text-xs font-bold text-stone-300 hover:bg-white/10 hover:text-white">View site</a>
                </div>
                <div class="mt-3 flex items-center gap-3 px-1">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand text-sm font-extrabold text-stone-950">{{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-bold text-white">{{ Auth::user()->name }}</span>
                        <span class="block truncate text-xs text-stone-500">{{ Auth::user()->email }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg p-2 text-stone-500 hover:bg-white/10 hover:text-red-400" aria-label="Log out" title="Log out">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-64">
            {{-- Top bar --}}
            <header class="sticky top-0 z-20 flex h-14 items-center justify-between gap-3 border-b border-stone-200 bg-white/95 px-4 backdrop-blur sm:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" class="relative flex h-9 w-9 items-center justify-center rounded-lg bg-stone-950 text-white lg:hidden" x-on:click="menu = true" aria-label="Open menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                        @if($todoTotal)<span class="absolute -right-1.5 -top-1.5 h-3 w-3 rounded-full border-2 border-white bg-brand"></span>@endif
                    </button>
                    <p class="text-sm font-bold text-stone-500">{{ $title ?? 'Super admin' }}</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-stone-500">
                    @if($todoTotal)
                        <a href="{{ route('superadmin.dashboard') }}#queue" class="rounded-full bg-brand-tint px-3 py-1 font-bold text-brand-deep hover:bg-brand-soft">{{ $todoTotal }} waiting</a>
                    @endif
                    <span class="hidden sm:inline">{{ now()->format('D, M j') }}</span>
                </div>
            </header>

            {{-- Flash messages --}}
            @foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-700'] as $key => $classes)
                @if(session($key))
                    <div class="mx-4 mt-4 flex items-start justify-between gap-3 rounded-xl border px-4 py-3 text-sm font-semibold sm:mx-6 {{ $classes }}" x-data="{ show: true }" x-show="show" role="status">
                        <span>{{ session($key) }}</span>
                        <button type="button" class="opacity-60 hover:opacity-100" x-on:click="show = false" aria-label="Dismiss">&times;</button>
                    </div>
                @endif
            @endforeach
            @if($errors->any() && ! old('form_context'))
                <div class="mx-4 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 sm:mx-6" role="alert">{{ $errors->first() }}</div>
            @endif

            <main class="mx-auto max-w-7xl p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
