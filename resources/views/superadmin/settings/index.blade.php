<x-superadmin-layout>
    <div class="max-w-3xl space-y-5">
        <x-page-header badge="Super admin" title="Site settings" subtitle="Shown in the site footer, the contact page and the legal pages." />

        <form method="POST" action="{{ route('superadmin.settings.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            @php
                $field = fn ($key) => old($key, $settings[$key] ?? '');
            @endphp

            <section class="backend-card space-y-4">
                <h2 class="text-lg font-extrabold text-stone-950">Brand</h2>
                <div>
                    <label for="site_title" class="mb-1.5 block text-sm font-bold text-stone-800">Site name <span class="text-brand">*</span></label>
                    <input id="site_title" name="site_title" value="{{ $field('site_title') }}" required class="backend-field">
                    @error('site_title')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="brand_blurb" class="mb-1.5 block text-sm font-bold text-stone-800">Footer blurb</label>
                    <textarea id="brand_blurb" name="brand_blurb" rows="2" class="backend-field h-auto py-3">{{ $field('brand_blurb') }}</textarea>
                    @error('brand_blurb')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
            </section>

            <section class="backend-card space-y-4">
                <h2 class="text-lg font-extrabold text-stone-950">Contact details</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_email" class="mb-1.5 block text-sm font-bold text-stone-800">Support email <span class="text-brand">*</span></label>
                        <input id="contact_email" type="email" name="contact_email" value="{{ $field('contact_email') }}" required class="backend-field">
                        @error('contact_email')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="mb-1.5 block text-sm font-bold text-stone-800">Phone / WhatsApp</label>
                        <input id="contact_phone" name="contact_phone" value="{{ $field('contact_phone') }}" class="backend-field">
                        @error('contact_phone')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="contact_address" class="mb-1.5 block text-sm font-bold text-stone-800">Address</label>
                    <input id="contact_address" name="contact_address" value="{{ $field('contact_address') }}" class="backend-field">
                    @error('contact_address')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="business_hours" class="mb-1.5 block text-sm font-bold text-stone-800">Business hours</label>
                    <textarea id="business_hours" name="business_hours" rows="3" class="backend-field h-auto py-3">{{ $field('business_hours') }}</textarea>
                    @error('business_hours')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
            </section>

            <div class="flex items-center gap-3">
                <button class="btn-primary">Save settings</button>
                <a href="{{ route('contact') }}" target="_blank" class="btn-link">Preview contact page →</a>
            </div>
        </form>
    </div>
</x-superadmin-layout>
