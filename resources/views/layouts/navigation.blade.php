<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-gray-200/70 bg-white/85 backdrop-blur-xl dark:border-gray-800 dark:bg-dark-secondary/85">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-bold tracking-tight">
                        <span class="text-orange">Plugged<span class="text-gray-900 dark:text-white">In</span></span>
                        <img src="{{ asset('images/icons8-plug-64.png') }}" alt="PluggedIn Logo" class="h-8 w-8 object-contain">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-5 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Products') }}
                    </x-nav-link>
                    <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                        {{ __('Services') }}
                    </x-nav-link>
                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                        {{ __('About') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        {{ __('Contact') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                @auth
                    @php($unreadNotificationsCount = auth()->user()->unreadNotifications()->count())
                    @php($cartItemsCount = auth()->user()->cartItems()->count())
                    @php($wishlistItemsCount = auth()->user()->wishlistItems()->count())

                    @if(auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('superadmin.dashboard') }}" class="px-3 py-1.5 rounded-full bg-red-500/10 text-red-500 hover:bg-red-500/20 text-sm font-semibold transition">
                            Super Admin
                        </a>
                    @elseif(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-full bg-orange/15 text-orange hover:bg-orange/25 text-sm font-semibold transition">
                            Admin
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('admin.referrals.index') }}" class="text-gray-900 dark:text-text-primary hover:text-orange font-medium transition">
                            Referrals
                        </a>
                    @endif

                    <a href="{{ route('notifications.index') }}" class="relative text-gray-900 dark:text-text-primary hover:text-orange" title="Notifications" aria-label="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118.6 14.4V11a6 6 0 10-12 0v3.4c0 .538-.214 1.055-.595 1.435L4 17h11z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 01-3.46 0" />
                        </svg>
                        @if($unreadNotificationsCount > 0)
                            <span class="absolute -top-2 -right-4 min-w-[18px] h-[18px] px-1 rounded-full bg-orange text-white text-[10px] leading-[18px] text-center font-bold">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('cart.index') }}" class="relative text-gray-900 dark:text-text-primary hover:text-orange" title="Cart" aria-label="Cart">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3.75h1.386a1.5 1.5 0 0 1 1.464 1.175L5.7 7.5m0 0h12.114a1.5 1.5 0 0 1 1.464 1.825l-1.2 6A1.5 1.5 0 0 1 16.61 16.5H8.025a1.5 1.5 0 0 1-1.464-1.175L5.7 7.5Zm0 0L4.5 3.75M9 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm8.25 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        @if($cartItemsCount > 0)
                            <span class="absolute -top-1 -right-1 h-2.5 w-2.5 rounded-full bg-orange"></span>
                        @endif
                    </a>

                    <a href="{{ route('wishlist.index') }}" class="relative text-gray-900 dark:text-text-primary hover:text-orange" title="Saved Items" aria-label="Saved Items">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 8.25c0-2.485-2.239-4.5-5-4.5-1.885 0-3.526.94-4.375 2.33a5.007 5.007 0 0 0-4.375-2.33c-2.761 0-5 2.015-5 4.5 0 7.22 9.375 12 9.375 12S21 15.47 21 8.25Z" />
                        </svg>
                        @if($wishlistItemsCount > 0)
                            <span class="absolute -top-1 -right-1 h-2.5 w-2.5 rounded-full bg-orange"></span>
                        @endif
                    </a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-700 text-sm leading-4 font-medium rounded-lg text-gray-900 dark:text-text-primary hover:border-orange/40 hover:text-orange transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(auth()->user()->hasRole('super-admin'))
                                <x-dropdown-link :href="route('superadmin.dashboard')">
                                    {{ __('Super Admin Dashboard') }}
                                </x-dropdown-link>
                            @elseif(auth()->user()->hasRole('admin'))
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('Admin Dashboard') }}
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="route('dashboard')">
                                    {{ __('Dashboard') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('orders.index')">
                                {{ __('My Orders') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('wishlist.index')">
                                {{ __('Saved Items') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('reviews.index')">
                                {{ __('My Reviews') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('products.upload.index')">
                                {{ __('My Uploads') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('bookings.index')">
                                {{ __('My Bookings') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('settings.index')">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-gray-900 dark:text-text-primary hover:text-orange font-medium transition">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary px-4 py-2 rounded-md text-sm">
                        Sign Up
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-900 dark:text-text-primary hover:text-orange transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 dark:bg-dark-secondary/95 border-t border-gray-200/70 dark:border-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                {{ __('Services') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                {{ __('About') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                {{ __('Contact') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-800">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-900 dark:text-text-primary">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-600 dark:text-text-secondary">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(auth()->user()->hasRole('super-admin'))
                        <x-responsive-nav-link :href="route('superadmin.dashboard')">
                            {{ __('Super Admin Dashboard') }}
                        </x-responsive-nav-link>
                    @elseif(auth()->user()->hasRole('admin'))
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('cart.index')">
                        {{ __('Cart') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('wishlist.index')">
                        {{ __('Saved Items') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('products.upload.index')">
                        {{ __('My Uploads') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notifications.index')">
                        {{ __('Notifications') }}
                    </x-responsive-nav-link>
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                        <x-responsive-nav-link :href="route('admin.referrals.index')">
                            {{ __('Referral Analytics') }}
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('orders.index')">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reviews.index')">
                        {{ __('My Reviews') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('bookings.index')">
                        {{ __('My Bookings') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.index')">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-800">
                <div class="px-4 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Sign Up') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>
