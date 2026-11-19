<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            <div class="mb-10 max-w-2xl">
                <span class="brand-badge">Account</span>
                <h1 class="section-title mt-4">Settings that stay out of your way.</h1>
                <p class="section-subtitle mt-3">Manage your account and choose the updates that are useful to you. PluggedIn uses one considered light appearance across the entire experience.</p>
            </div>


            <div class="grid gap-6 md:grid-cols-5">
                <article class="glass-panel p-6 md:col-span-2">
                    <p class="text-sm font-bold text-stone-950">Your account</p>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Update your details, password, or security preferences from one place.</p>
                    <a href="{{ route('profile.edit') }}" class="btn-secondary mt-6 w-full">Manage account</a>
                </article>

                <form action="{{ route('settings.notifications.update') }}" method="POST" class="glass-panel p-6 md:col-span-3">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-extrabold tracking-[-0.02em]">Notification preferences</h2>
                            <p class="mt-1 text-sm leading-6 text-stone-600">Control the operational updates you receive.</p>
                        </div>
                        <span class="brand-badge">Email</span>
                    </div>

                    <div class="mt-6 divide-y divide-stone-100 rounded-xl border border-stone-200">
                        @foreach (['notify_order_updates' => ['Order updates', 'Confirmation, payment, and delivery progress.'], 'notify_booking_updates' => ['Service bookings', 'Booking confirmations and appointment updates.'], 'notify_product_updates' => ['Product updates', 'Updates on your submitted marketplace listings.']] as $field => [$label, $description])
                            <label class="flex cursor-pointer items-center justify-between gap-4 p-4 hover:bg-stone-50">
                                <span><span class="block text-sm font-bold text-stone-900">{{ $label }}</span><span class="mt-0.5 block text-xs leading-5 text-stone-500">{{ $description }}</span></span>
                                <input type="hidden" name="{{ $field }}" value="0">
                                <input type="checkbox" name="{{ $field }}" value="1" @checked(auth()->user()->{$field}) class="h-5 w-5 rounded border-stone-300 text-brand-deep focus:ring-brand">
                            </label>
                        @endforeach
                    </div>

                    <button class="btn-primary mt-6">Save preferences</button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
