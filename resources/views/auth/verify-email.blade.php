<x-guest-layout>
    <div class="mb-6">
        <span class="brand-badge">Email Verification</span>
        <h1 class="mt-3 text-3xl font-extrabold tracking-[-.04em] text-stone-950">Confirm your email</h1>
    </div>

    <div class="mb-4 text-sm text-stone-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-medium text-stone-600 hover:text-brand rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
