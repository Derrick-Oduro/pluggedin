<x-guest-layout>
    <div class="mb-6">
        <span class="brand-badge">New account</span>
        <h1 class="display-heading mt-3 text-3xl text-stone-950">Create your PluggedIn profile</h1>
        <p class="mt-2 text-sm text-stone-600">List services, track orders, and launch your first offer in minutes.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="mt-1"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-xs text-stone-500">
            Use 8+ characters with a mix of letters and numbers to keep your account secure.
        </div>

        <x-primary-button class="w-full">
            {{ __('Create account') }}
        </x-primary-button>

        <p class="text-center text-xs text-stone-500">By creating an account you agree to our <a href="{{ route('legal.terms') }}" class="font-bold text-stone-700 hover:text-brand-deep">Terms</a> and <a href="{{ route('legal.privacy') }}" class="font-bold text-stone-700 hover:text-brand-deep">Privacy policy</a>.</p>

        <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
            <span class="text-stone-500">Already have an account?</span>
            <a class="font-semibold text-stone-700 hover:text-brand-deep" href="{{ route('login') }}">{{ __('Log in') }}</a>
        </div>
    </form>
</x-guest-layout>
