<x-app-layout>
    <section class="page-shell">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Account</p>
                <h1 class="text-5xl sm:text-6xl font-bold">Profile Settings</h1>
            </div>

            <div class="space-y-6">
                <div class="glass-panel p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

                <div class="glass-panel p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

                <div class="glass-panel p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
        </div>
    </section>
</x-app-layout>
