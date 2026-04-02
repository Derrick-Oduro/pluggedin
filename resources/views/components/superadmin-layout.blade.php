<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Super Admin - {{ config('app.name', 'PluggedIn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Theme Script -->
        <script>
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
    <body class="font-sans antialiased bg-white dark:bg-dark text-gray-900 dark:text-text-primary">
        <div class="min-h-screen flex bg-gray-50 dark:bg-dark">
            <!-- Sidebar -->
            <aside class="w-64 bg-white dark:bg-dark-secondary border-r border-gray-200 dark:border-gray-800 fixed h-full">
                <div class="p-4 border-b border-gray-200 dark:border-gray-800">
                    <a href="{{ route('superadmin.dashboard') }}" class="text-xl font-bold">
                        <span class="text-red-500">Super</span><span class="text-orange">Admin</span>
                    </a>
                    <p class="text-xs uppercase tracking-[0.12em] text-text-secondary mt-1">PluggedIn Control Panel</p>
                </div>

                <nav class="px-2.5 py-3 space-y-1">
                    <!-- Dashboard -->
                          <a href="{{ route('superadmin.dashboard') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-red-500/20 text-red-500' : 'text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- User Management -->
                          <a href="{{ route('superadmin.users.index') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('superadmin.users.*') ? 'bg-red-500/20 text-red-500' : 'text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>User Management</span>
                    </a>

                    <!-- Category Management -->
                          <a href="{{ route('superadmin.categories.index') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('superadmin.categories.*') ? 'bg-red-500/20 text-red-500' : 'text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Categories</span>
                    </a>

                          <a href="{{ route('superadmin.marketing.index') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('superadmin.marketing.*') ? 'bg-red-500/20 text-red-500' : 'text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3.75h9m-9 3.75h5.25M4.5 19.5h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 003 6v12a1.5 1.5 0 001.5 1.5z" />
                        </svg>
                        <span>Marketing</span>
                    </a>

                          <a href="{{ route('superadmin.audit-logs.index') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('superadmin.audit-logs.*') ? 'bg-red-500/20 text-red-500' : 'text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 13.5V5.25A2.25 2.25 0 0017.25 3h-10.5A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21h10.5a2.25 2.25 0 002.25-2.25V16.5M8.25 7.5h7.5M8.25 11.25h7.5M8.25 15h4.5" />
                        </svg>
                        <span>Audit Logs</span>
                    </a>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

                    <!-- Regular Admin -->
                          <a href="{{ route('admin.dashboard') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-orange">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Admin Panel</span>
                    </a>

                    <!-- Back to Site -->
                          <a href="{{ route('home') }}"
                              class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>View Site</span>
                    </a>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

                    <!-- User Info -->
                    <div class="px-3 py-2.5">
                        <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-text-secondary">{{ Auth::user()->email }}</p>
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
            <div class="flex-1 ml-64">
                <!-- Top Bar -->
                <header class="bg-white dark:bg-dark-secondary border-b border-gray-200 dark:border-gray-800 px-5 py-2.5">
                    <div class="flex justify-between items-center">
                        <h1 class="text-base font-semibold">
                            @yield('page-title', 'Super Admin Dashboard')
                        </h1>
                        <div class="text-sm text-text-secondary">
                            {{ now()->format('l, F j, Y') }}
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-500/20 border border-green-500 text-green-500 mx-5 mt-3 px-3 py-2 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-500/20 border border-red-500 text-red-500 mx-5 mt-3 px-3 py-2 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Page Content -->
                <main class="p-5">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
