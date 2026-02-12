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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-dark text-text-primary">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-500 text-white px-6 py-4 text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-500 text-white px-6 py-4 text-center">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-dark-secondary shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-dark-secondary border-t border-gray-800 mt-20">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-text-secondary">
                        <p>&copy; {{ date('Y') }} PluggedIn. Upgrade. Don't Replace.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
