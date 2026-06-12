<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PluggedIn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Theme Script (must load before body) -->
        <script>
            // Apply theme immediately to prevent flash
            (function() {
                const theme = localStorage.getItem('theme') || 'dark';
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (theme === 'system') {
                    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (systemPrefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

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
                <header class="border-b border-gray-200/70 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-dark-secondary/80">
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
            <footer class="border-t border-gray-200/70 bg-white/80 backdrop-blur dark:border-gray-800 dark:bg-dark-secondary/80 mt-20">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <p class="text-base font-semibold">Plugged<span class="text-brand">In</span></p>
                            <p class="text-gray-600 dark:text-text-secondary mt-3">Practical upgrades and thoughtful repairs that extend device life and save you money over time.</p>

                            <div class="flex items-center gap-3 mt-4">
                                <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-white" aria-label="Twitter">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.92c-.63.28-1.3.47-2 .55.72-.43 1.28-1.1 1.54-1.9-.67.4-1.41.7-2.2.86C18.4 4.6 17.54 4 16.56 4c-1.48 0-2.68 1.2-2.68 2.68 0 .21.02.42.06.62C10.7 7.2 8 5.6 6.2 3.3c-.23.4-.36.86-.36 1.36 0 .94.48 1.77 1.22 2.25-.56 0-1.08-.17-1.53-.43v.04c0 1.3.93 2.38 2.17 2.63-.23.06-.48.1-.73.1-.18 0-.36-.02-.53-.05.36 1.12 1.4 1.93 2.64 1.95C7.6 15.7 6.4 16.2 5 16.2c-.23 0-.46-.01-.68-.04 1.28.82 2.8 1.3 4.44 1.3 5.32 0 8.24-4.41 8.24-8.24v-.38c.56-.4 1.06-.9 1.46-1.48-.51.22-1.06.37-1.63.44z"/></svg>
                                </a>
                                <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-white" aria-label="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 6.4A4.6 4.6 0 1 0 16.6 13 4.6 4.6 0 0 0 12 8.4zm6.4-3.6a1 1 0 1 1-1 1 1 1 0 0 1 1-1z"/></svg>
                                </a>
                                <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-white" aria-label="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.5 9.9v-7H8.5v-2.9h2V9.4c0-2 1.2-3.1 3-3.1.86 0 1.8.15 1.8.15v2h-1c-1 0-1.3.62-1.3 1.3v1.6h2.2l-.35 2.9H14.5v7A10 10 0 0 0 22 12z"/></svg>
                                </a>
                            </div>
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
                            <p class="text-sm text-gray-600 dark:text-text-secondary">info@pluggedin.example<br>+233 20 000 0000</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mt-4">123 Upgrade Lane<br>Accra, GH</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200/70 dark:border-gray-800">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
                        <p class="mb-2 md:mb-0">&copy; {{ date('Y') }} PluggedIn — All rights reserved.</p>
                        <p class="opacity-80">Built for longer-lasting devices</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
