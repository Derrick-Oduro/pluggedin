<x-guest-layout>
    <div class="mb-6">
        <span class="brand-badge">Security Check</span>
        <h1 class="mt-3 text-3xl font-extrabold tracking-[-.04em] text-stone-950">Confirm your password</h1>
    </div>

    <div class="mb-4 text-sm text-stone-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
