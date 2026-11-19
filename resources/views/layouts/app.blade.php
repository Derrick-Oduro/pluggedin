<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PluggedIn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Flash Messages -->
            @if (session('success') || session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    @if (session('success'))
                        <div class="glass-panel border-green-300/70 bg-green-50/90 px-4 py-2.5 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="glass-panel border-red-300/70 bg-red-50/90 px-4 py-2.5 text-sm text-red-700 {{ session('success') ? 'mt-2' : '' }}">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-stone-200 bg-white/90 backdrop-blur">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="mt-20 bg-stone-950 text-stone-300">
                <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                    <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-5">
                        <div class="lg:col-span-2">
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xl font-extrabold tracking-[-0.06em] text-white">Plugged<span class="text-brand-soft">In</span></a>
                            <p class="mt-4 max-w-sm text-sm leading-6 text-stone-400">A safer, smarter place for campus communities to buy, sell, repair, and get more from their tech.</p>
                            <a href="{{ route('contact') }}" class="mt-6 inline-flex text-sm font-extrabold text-brand-soft transition hover:text-white">Bring PluggedIn to your campus →</a>
                        </div>
                        <div><h4 class="text-xs font-extrabold uppercase tracking-[0.15em] text-stone-500">Marketplace</h4><ul class="mt-4 space-y-3 text-sm"><li><a href="{{ route('products.index') }}" class="hover:text-white">Browse all tech</a></li><li><a href="{{ route('products.index', ['q' => 'laptop']) }}" class="hover:text-white">Laptops</a></li><li><a href="{{ route('products.index', ['q' => 'SSD']) }}" class="hover:text-white">Parts & upgrades</a></li><li><a href="{{ auth()->check() ? route('products.upload.create') : route('register') }}" class="hover:text-white">Sell or swap</a></li></ul></div>
                        <div><h4 class="text-xs font-extrabold uppercase tracking-[0.15em] text-stone-500">Support</h4><ul class="mt-4 space-y-3 text-sm"><li><a href="{{ route('services.index') }}" class="hover:text-white">Repairs & upgrades</a></li><li><a href="{{ route('contact') }}" class="hover:text-white">Contact us</a></li><li><a href="{{ route('about') }}" class="hover:text-white">Our approach</a></li></ul></div>
                        <div><h4 class="text-xs font-extrabold uppercase tracking-[0.15em] text-stone-500">Get in touch</h4><p class="mt-4 text-sm leading-7 text-stone-400">support@pluggedin.com<br>+233 20 000 0000<br>Accra, Ghana</p></div>
                    </div>
                </div>
                <div class="border-t border-white/10"><div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"><p>© {{ date('Y') }} PluggedIn. Built for better campus tech.</p><p>Buy better. Keep devices useful longer.</p></div></div>
            </footer>
        </div>
    </body>
</html>
