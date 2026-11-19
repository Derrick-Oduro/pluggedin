<x-guest-layout>
    <div class="mb-6">
        <span class="brand-badge">Member access</span>
        <h1 class="mt-3 text-3xl font-extrabold tracking-[-.04em] text-stone-950">Sign in to PluggedIn</h1>
        <p class="mt-2 text-sm text-stone-600">Welcome back. Jump into your dashboard and keep momentum going.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex flex-col gap-3 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-stone-300 text-brand focus:ring-brand" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="font-medium text-stone-600 hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            {{ __('Log in') }}
        </x-primary-button>

        <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
            <span class="text-stone-500">New here?</span>
            <a class="font-semibold text-stone-700 hover:text-brand" href="{{ route('register') }}">{{ __('Create an account') }}</a>
        </div>
    </form>
</x-guest-layout>
