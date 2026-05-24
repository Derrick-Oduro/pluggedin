<x-guest-layout>
    <div class="mb-6">
        <p class="text-[0.65rem] uppercase tracking-[0.26em] text-slate-500">New account</p>
        <h1 class="mt-3 text-3xl font-semibold sm:text-4xl font-display">Create your PluggedIn profile</h1>
        <p class="mt-2 text-sm text-slate-600">List services, track orders, and launch your first offer in minutes.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full rounded-xl border-slate-200 bg-white/80 focus:bg-white" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 bg-white/80 focus:bg-white" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full rounded-xl border-slate-200 bg-white/80 focus:bg-white"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-xl border-slate-200 bg-white/80 focus:bg-white"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 text-xs text-slate-500">
            Use 8+ characters with a mix of letters and numbers to keep your account secure.
        </div>

        <x-primary-button class="w-full justify-center rounded-xl py-3 text-base">
            {{ __('Create account') }}
        </x-primary-button>

        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 text-sm text-slate-600">
            <span class="text-slate-500">Already have an account?</span>
            <a class="font-semibold text-slate-700 hover:text-orange" href="{{ route('login') }}">{{ __('Log in') }}</a>
        </div>
    </form>
</x-guest-layout>
