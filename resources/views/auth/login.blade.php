<x-guest-layout>
    <div class="mb-6">
        <p class="text-[0.65rem] uppercase tracking-[0.26em] text-slate-500">Member access</p>
        <h1 class="mt-3 text-3xl font-semibold sm:text-4xl font-display">Sign in to PluggedIn</h1>
        <p class="mt-2 text-sm text-slate-600">Welcome back. Jump into your dashboard and keep momentum going.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 bg-white/80 focus:bg-white" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full rounded-xl border-slate-200 bg-white/80 focus:bg-white"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex flex-col gap-3 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-orange focus:ring-orange" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="font-medium text-slate-600 hover:text-orange focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center rounded-xl py-3 text-base">
            {{ __('Log in') }}
        </x-primary-button>

        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 text-sm text-slate-600">
            <span class="text-slate-500">New here?</span>
            <a class="font-semibold text-slate-700 hover:text-orange" href="{{ route('register') }}">{{ __('Create an account') }}</a>
        </div>
    </form>
</x-guest-layout>
