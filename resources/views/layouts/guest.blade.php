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
    <body class="font-sans antialiased">
        <div class="relative min-h-screen flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8 overflow-hidden">
            <div class="pointer-events-none absolute -top-24 -left-20 h-80 w-80 rounded-full bg-orange/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-20 h-96 w-96 rounded-full bg-orange/15 blur-3xl"></div>

            <div class="relative w-full max-w-md">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 text-3xl font-bold mb-6">
                    <span class="text-orange">Plugged</span><span>In</span>
                    <img src="{{ asset('images/icons8-plug-64.png') }}" alt="PluggedIn Logo" class="h-8 w-8 object-contain">
                </a>

                <div class="glass-panel px-6 py-6 sm:px-8 sm:py-8 shadow-xl shadow-black/5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
