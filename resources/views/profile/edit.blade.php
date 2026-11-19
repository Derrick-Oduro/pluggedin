<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-4xl">
            <x-page-header badge="Account" title="Profile settings" subtitle="Update your details, password and account." />

            <div class="space-y-6">
                <div class="backend-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

                <div class="backend-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

                <div class="backend-card">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
        </div>
    </section>
</x-app-layout>
