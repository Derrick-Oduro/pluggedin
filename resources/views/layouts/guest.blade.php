<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PluggedIn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <style>
            :root {
                --page-bg: #f6f1ea;
                --ink: #0f172a;
                --muted: #5b6472;
                --accent: #b02f00;
                --accent-2: #ff5722;
                --card: rgba(255, 255, 255, 0.9);
                --card-border: rgba(255, 255, 255, 0.65);
            }

            body {
                background: var(--page-bg);
                color: var(--ink);
            }

            .brand-accent {
                color: var(--accent);
            }

            .brand-accent-2 {
                color: var(--accent-2);
            }

            .fade-up {
                animation: fadeUp 0.8s ease-out both;
            }

            .fade-up.delay-1 {
                animation-delay: 0.12s;
            }

            .fade-up.delay-2 {
                animation-delay: 0.2s;
            }

            .float-slow {
                animation: float 14s ease-in-out infinite;
            }

            .float-fast {
                animation: float 9s ease-in-out infinite;
            }

            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translateY(16px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes float {
                0%,
                100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-18px);
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .fade-up,
                .float-slow,
                .float-fast {
                    animation: none !important;
                }
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute -top-24 -left-16 h-80 w-80 rounded-full bg-brand/25 blur-3xl float-slow"></div>
            <div class="pointer-events-none absolute top-1/4 -right-24 h-72 w-72 rounded-full bg-brand-light/20 blur-[110px] float-fast"></div>
            <div class="pointer-events-none absolute -bottom-32 left-1/3 h-96 w-96 rounded-full bg-slate-900/10 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(176,47,0,.18),transparent_38%),radial-gradient(circle_at_bottom_right,rgba(255,87,34,.16),transparent_40%),radial-gradient(circle_at_center,rgba(15,23,42,.04),transparent_55%)]"></div>

            <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12 lg:px-10">
                <div class="grid w-full gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                    <div class="fade-up">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-white/70 bg-white/70 px-4 py-1 text-sm font-semibold shadow-sm backdrop-blur">
                            <span class="brand-accent">Plugged</span><span>In</span>
                            <span class="text-xs text-slate-500">Marketplace Suite</span>
                        </a>

                        <h1 class="mt-6 font-sans text-4xl font-semibold leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl">
                            Power up your storefront, bookings, and rewards.
                        </h1>
                        <p class="mt-4 text-base text-slate-600 sm:text-lg">
                            Everything you need to launch services, track orders, and keep loyal customers coming back.
                        </p>

                        <div class="mt-6 flex items-center gap-3 text-sm text-slate-600">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <span>Secure sessions with device-level checks.</span>
                        </div>
                    </div>

                    <div class="fade-up delay-1">
                        <div class="rounded-[32px] bg-[linear-gradient(135deg,rgba(176,47,0,0.42),rgba(255,87,34,0.34),rgba(15,23,42,0.18))] p-[1px] shadow-[0_30px_80px_rgba(15,23,42,0.18)]">
                            <div class="rounded-[31px] border border-[var(--card-border)] bg-[var(--card)] p-6 backdrop-blur-xl sm:p-8">
                                {{ $slot }}
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500">
                            Need help? Reach us at <span class="font-semibold text-slate-700">support@pluggedin.test</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
