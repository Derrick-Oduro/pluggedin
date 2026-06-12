<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold">Site Settings</h1>
                <p class="text-sm text-gray-600 dark:text-text-secondary">Manage site-wide values like title, contact info, and brand text.</p>
            </div>
            <div>
                <a href="{{ route('superadmin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to dashboard</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <aside class="lg:col-span-1">
                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-4 border border-gray-200/70 dark:border-gray-800 space-y-2">
                    <a href="#general" class="block px-3 py-2 rounded-lg text-sm bg-orange/10 text-orange font-semibold">General</a>
                    <a href="#branding" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Branding</a>
                    <a href="#contact" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Contact</a>
                    <a href="#integrations" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Integrations</a>
                </div>
            </aside>

            <div class="lg:col-span-3 space-y-6">
                <form method="POST" action="{{ route('superadmin.settings.update') }}">
                    @csrf
                    @method('PATCH')

                    <div id="general" class="bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                        <h2 class="text-2xl font-bold mb-4">General</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">Site Title</label>
                                <input name="site_title" value="{{ old('site_title', $site_title) }}" class="w-full mt-2 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark" />
                                @error('site_title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium">Contact Email</label>
                                <input name="contact_email" value="{{ old('contact_email', $contact_email) }}" class="w-full mt-2 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark" />
                                @error('contact_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-sm font-medium">Brand Blurb</label>
                            <textarea name="brand_blurb" rows="3" class="w-full mt-2 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark">{{ old('brand_blurb', $brand_blurb) }}</textarea>
                            @error('brand_blurb')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <button class="bg-orange hover:bg-orange-light text-white px-6 py-2 rounded-lg font-semibold">Save</button>
                            <a href="{{ url('/') }}" target="_blank" class="text-sm text-gray-600 hover:text-gray-900">Preview on site</a>
                        </div>
                    </div>
                </form>

                <div id="contact" class="bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Contact</h2>
                    <p class="text-sm text-gray-600 dark:text-text-secondary">Address and other contact details.</p>
                    <form method="POST" action="{{ route('superadmin.settings.update') }}">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-sm font-medium">Contact Address</label>
                                <input name="contact_address" value="{{ old('contact_address', $contact_address) }}" class="w-full mt-2 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark" />
                            </div>
                            <div></div>
                        </div>
                        <div class="mt-4">
                            <button class="bg-orange hover:bg-orange-light text-white px-6 py-2 rounded-lg font-semibold">Save Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
