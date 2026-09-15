<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Super Admin - {{ config('app.name', 'PluggedIn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-50 font-sans text-stone-950 antialiased">
        <div class="min-h-screen flex bg-stone-50">
            <!-- Sidebar -->
            <aside class="fixed h-full w-56 border-r border-stone-200 bg-white">
                <div class="border-b border-stone-200 p-4">
                    <a href="{{ route('superadmin.dashboard') }}" class="text-lg font-bold tracking-tight">
                        <span class="text-brand-deep">Super</span><span class="text-brand">Admin</span>
                    </a>
                    <p class="mt-1 text-[10px] uppercase tracking-[0.14em] text-stone-500">PluggedIn Control Panel</p>
                </div>

                <nav class="px-2 py-2 space-y-1">
                    <!-- Dashboard -->
                          <a href="{{ route('superadmin.dashboard') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-brand-tint text-brand-deep' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- User Management -->
                          <a href="{{ route('superadmin.users.index') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('superadmin.users.*') ? 'bg-brand-tint text-brand-deep' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>User Management</span>
                    </a>

                    <!-- Category Management -->
                          <a href="{{ route('superadmin.categories.index') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('superadmin.categories.*') ? 'bg-brand-tint text-brand-deep' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Categories</span>
                    </a>

                          <a href="{{ route('superadmin.marketing.index') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('superadmin.marketing.*') ? 'bg-brand-tint text-brand-deep' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3.75h9m-9 3.75h5.25M4.5 19.5h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 003 6v12a1.5 1.5 0 001.5 1.5z" />
                        </svg>
                        <span>Marketing</span>
                    </a>

                    <a href="{{ route('superadmin.audit-logs.index') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('superadmin.audit-logs.*') ? 'bg-brand-tint text-brand-deep' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 13.5V5.25A2.25 2.25 0 0017.25 3h-10.5A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21h10.5a2.25 2.25 0 002.25-2.25V16.5M8.25 7.5h7.5M8.25 11.25h7.5M8.25 15h4.5" />
                        </svg>
                        <span>Audit Logs</span>
                    </a>

                    <a href="{{ route('superadmin.contact-messages.index') }}"
                        class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs('superadmin.contact-messages.*') ? 'bg-brand-tint text-brand-deep' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8.25 10.7 13a2.5 2.5 0 0 0 2.6 0L21 8.25M5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v9a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 16.5v-9a2.25 2.25 0 0 1 2.25-2.25Z" /></svg>
                        <span>Messages</span>
                    </a>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

                    <!-- Regular Admin -->
                          <a href="{{ route('admin.dashboard') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition text-stone-600 hover:bg-stone-100 hover:text-brand">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Admin Panel</span>
                    </a>

                    <!-- Back to Site -->
                          <a href="{{ route('home') }}"
                              class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition text-stone-600 hover:bg-stone-100 hover:text-stone-950">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>View Site</span>
                    </a>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

                    <!-- User Info -->
                    <div class="px-3 py-2">
                        <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-stone-500">{{ Auth::user()->email }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="text-sm text-red-500 hover:text-red-400">
                                Logout
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 ml-56">
                <!-- Top Bar -->
                <header class="border-b border-stone-200 bg-white px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[10px] uppercase tracking-[0.16em] text-stone-500">Super Admin Workspace</p>
                        <div class="text-xs text-stone-500">
                            {{ now()->format('l, F j, Y') }}
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-500/20 border border-green-500 text-green-500 mx-4 mt-2 px-3 py-2 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-500/20 border border-red-500 text-red-500 mx-4 mt-2 px-3 py-2 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Page Content -->
                <main class="p-4">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
