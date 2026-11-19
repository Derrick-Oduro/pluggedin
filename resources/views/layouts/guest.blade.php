<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $site['site_title'] }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|lilita-one:400|caveat:600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="page-shell min-h-screen py-0">
            <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-10 lg:grid-cols-[1.05fr_.95fr] lg:items-center">
                    <div>
                        <x-brand-logo size="lg" />
                        <p class="tagline mt-2 text-3xl">Your One-Stop Student Hub</p>

                        <h1 class="display-heading mt-8 text-4xl leading-[1.05] text-stone-950 sm:text-5xl">Buy, swap and upgrade campus tech, safely.</h1>
                        <p class="mt-4 max-w-md text-base leading-7 text-stone-600">One account for the marketplace, repairs, trade-ins and your orders.</p>

                        <ul class="mt-6 hidden space-y-2 text-sm font-semibold text-stone-700 sm:block">
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Every listing is reviewed before it goes live</li>
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Free campus delivery, or collect in person</li>
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Pay securely with Paystack</li>
                            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-brand"></span>Repairs and upgrades booked in minutes</li>
                        </ul>
                    </div>

                    <div>
                        <div class="rounded-2xl border border-stone-200 border-t-4 border-t-brand bg-white p-6 shadow-[0_24px_60px_-34px_rgba(41,30,23,.45)] sm:p-8">
                            {{ $slot }}
                        </div>
                        <p class="mt-4 text-center text-xs text-stone-500">Need help? Email <a href="mailto:{{ $site['contact_email'] }}" class="font-bold text-stone-700 hover:text-brand-deep">{{ $site['contact_email'] }}</a> · <a href="{{ route('legal.terms') }}" class="hover:text-brand-deep">Terms</a> · <a href="{{ route('legal.privacy') }}" class="hover:text-brand-deep">Privacy</a></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
