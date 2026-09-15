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
                        <div class="glass-panel border-green-300/70 dark:border-green-500/30 bg-green-50/90 dark:bg-green-900/20 px-4 py-2.5 text-sm text-green-700 dark:text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="glass-panel border-red-300/70 dark:border-red-500/30 bg-red-50/90 dark:bg-red-900/20 px-4 py-2.5 text-sm text-red-700 dark:text-red-300 {{ session('success') ? 'mt-2' : '' }}">
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
            <footer class="mt-20 border-t border-stone-200 bg-white">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <p class="text-base font-extrabold tracking-[-0.04em]">Plugged<span class="text-brand">In</span></p>
                            <p class="mt-3 text-sm leading-6 text-stone-600">Practical upgrades and thoughtful repairs that extend device life and save you money over time.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-3">Quick Links</h4>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-text-secondary">
                                <li><a href="{{ url('/') }}" class="hover:underline">Home</a></li>
                                <li><a href="{{ route('products.index') }}" class="hover:underline">Products</a></li>
                                <li><a href="{{ route('services.index') }}" class="hover:underline">Services</a></li>
                                <li><a href="{{ route('contact') }}" class="hover:underline">Contact</a></li>
                                <li><a href="{{ url('/about') }}" class="hover:underline">About</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-3">Services</h4>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-text-secondary">
                                <li><a href="{{ route('services.index') }}" class="hover:underline">Diagnostics & Repair</a></li>
                                <li><a href="{{ route('services.index') }}" class="hover:underline">Component Upgrades</a></li>
                                <li><a href="{{ route('services.index') }}" class="hover:underline">Data Migration</a></li>
                                <li><a href="{{ route('services.index') }}" class="hover:underline">Warranty Support</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-3">Contact</h4>
                            <p class="text-sm leading-6 text-stone-600">support@pluggedin.com<br>+233 20 000 0000</p>
                            <p class="mt-4 text-sm leading-6 text-stone-600">Accra, Ghana</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-stone-100">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between text-sm text-stone-500">
                        <p class="mb-2 md:mb-0">&copy; {{ date('Y') }} PluggedIn — All rights reserved.</p>
                        <p class="opacity-80">Built for longer-lasting devices</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
