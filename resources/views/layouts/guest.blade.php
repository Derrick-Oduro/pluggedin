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
    <body class="font-sans antialiased bg-[#f5f7fb] text-slate-900">
        <div class="relative min-h-screen overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
            <div class="pointer-events-none absolute -top-24 -left-20 h-80 w-80 rounded-full bg-orange/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-20 h-96 w-96 rounded-full bg-orange/15 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(249,115,22,.12),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(15,23,42,.08),transparent_40%)]"></div>

            <div class="relative flex min-h-[calc(100vh-5rem)] items-center justify-center">
                <div class="w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-2 text-2xl font-bold">
                        <span class="text-orange">Plugged</span><span>In</span>
                    </a>

                    <div class="rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-[0_20px_70px_rgba(15,23,42,0.12)] backdrop-blur-xl sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
