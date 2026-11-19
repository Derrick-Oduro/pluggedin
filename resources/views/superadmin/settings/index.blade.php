<x-superadmin-layout>
    <div class="space-y-5">
        <x-page-header badge="Super admin" title="Site settings" subtitle="Site-wide values like title, contact details and brand text." />


        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <aside class="lg:col-span-1">
                <div class="backend-card space-y-2">
                    <a href="#general" class="block px-3 py-2 rounded-lg text-sm bg-brand/10 text-brand font-semibold">General</a>
                    <a href="#branding" class="block px-3 py-2 rounded-lg text-sm hover:bg-stone-100">Branding</a>
                    <a href="#contact" class="block px-3 py-2 rounded-lg text-sm hover:bg-stone-100">Contact</a>
                    <a href="#integrations" class="block px-3 py-2 rounded-lg text-sm hover:bg-stone-100">Integrations</a>
                </div>
            </aside>

            <div class="lg:col-span-3 space-y-6">
                <form method="POST" action="{{ route('superadmin.settings.update') }}">
                    @csrf
                    @method('PATCH')

                    <div id="general" class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950 mb-4">General</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">Site Title</label>
                                <input name="site_title" value="{{ old('site_title', $site_title) }}" class="backend-field" />
                                @error('site_title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium">Contact Email</label>
                                <input name="contact_email" value="{{ old('contact_email', $contact_email) }}" class="backend-field" />
                                @error('contact_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-sm font-medium">Brand Blurb</label>
                            <textarea name="brand_blurb" rows="3" class="backend-field">{{ old('brand_blurb', $brand_blurb) }}</textarea>
                            @error('brand_blurb')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <button class="btn-primary">Save</button>
                            <a href="{{ url('/') }}" target="_blank" class="text-sm text-stone-600 hover:text-stone-900">Preview on site</a>
                        </div>
                    </div>
                </form>

                <div id="contact" class="backend-card">
                    <h2 class="text-lg font-extrabold text-stone-950 mb-4">Contact</h2>
                    <p class="text-sm text-stone-600">Address and other contact details.</p>
                    <form method="POST" action="{{ route('superadmin.settings.update') }}">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-sm font-medium">Contact Address</label>
                                <input name="contact_address" value="{{ old('contact_address', $contact_address) }}" class="backend-field" />
                            </div>
                            <div></div>
                        </div>
                        <div class="mt-4">
                            <button class="btn-primary">Save Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-superadmin-layout>
