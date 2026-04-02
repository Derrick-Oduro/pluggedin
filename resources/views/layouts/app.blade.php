<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PluggedIn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
                        <div class="glass-panel border-green-300/70 dark:border-green-500/30 bg-green-50/90 dark:bg-green-900/20 px-5 py-3 text-green-700 dark:text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="glass-panel border-red-300/70 dark:border-red-500/30 bg-red-50/90 dark:bg-red-900/20 px-5 py-3 text-red-700 dark:text-red-300 {{ session('success') ? 'mt-3' : '' }}">
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
                    <div class="text-center">
                        <p class="text-lg font-semibold">Plugged<span class="text-orange">In</span></p>
                        <p class="text-gray-600 dark:text-text-secondary mt-1">Upgrade. Don't Replace.</p>
                        <p class="text-xs uppercase tracking-[0.16em] text-gray-500 dark:text-text-secondary mt-3">&copy; {{ date('Y') }} Built for longer-lasting devices</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
