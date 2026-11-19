<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $site['site_title'] }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="page-shell min-h-screen py-0">
            <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-10 lg:grid-cols-[1.05fr_.95fr] lg:items-center">
                    <div>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-stone-950"><img src="{{ asset('images/icons8-plug-64.png') }}" alt="" class="h-5 w-5"></span>
                            <span class="text-xl font-extrabold tracking-[-.06em] text-stone-950">Plugged<span class="text-brand">In</span></span>
                        </a>

                        <h1 class="mt-8 text-4xl font-extrabold leading-[1.02] tracking-[-.05em] text-stone-950 sm:text-5xl">Buy, sell and upgrade campus tech, safely.</h1>
                        <p class="mt-4 max-w-md text-base leading-7 text-stone-600">One account for the marketplace, repairs, trade-ins and your orders.</p>

                        <ul class="mt-6 hidden space-y-2 text-sm font-semibold text-stone-700 sm:block">
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Every listing is reviewed before it goes live</li>
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Pay with Paystack, collect on campus or get delivery</li>
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Repairs and upgrades booked in minutes</li>
                        </ul>
                    </div>

                    <div>
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-[0_24px_60px_-34px_rgba(41,30,23,.45)] sm:p-8">
                            {{ $slot }}
                        </div>
                        <p class="mt-4 text-center text-xs text-stone-500">Need help? Email <a href="mailto:{{ $site['contact_email'] }}" class="font-bold text-stone-700 hover:text-brand">{{ $site['contact_email'] }}</a> · <a href="{{ route('legal.terms') }}" class="hover:text-brand">Terms</a> · <a href="{{ route('legal.privacy') }}" class="hover:text-brand">Privacy</a></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
