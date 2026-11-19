<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-3xl">
            <x-page-header badge="Book a service" :title="'Book '.$service->name" :back-href="route('services.index')" back-label="Back to services" />

            <div class="backend-card mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-lg text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                <p class="shrink-0 text-2xl font-extrabold tracking-[-.04em] text-stone-950">GH₵{{ number_format($service->price, 2) }}</p>
            </div>

            <form action="{{ route('bookings.store') }}" method="POST" class="backend-card space-y-5">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <div>
                    <label for="device_model" class="mb-1.5 block text-sm font-bold text-stone-800">Device model <span class="text-brand">*</span></label>
                    <input id="device_model" type="text" name="device_model" value="{{ old('device_model') }}" required placeholder="e.g. Dell Inspiron 15, MacBook Pro 2020" class="backend-field">
                    @error('device_model')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="preferred_date" class="mb-1.5 block text-sm font-bold text-stone-800">Preferred date <span class="text-brand">*</span></label>
                    <input id="preferred_date" type="date" name="preferred_date" value="{{ old('preferred_date') }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="backend-field">
                    @error('preferred_date')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notes" class="mb-1.5 block text-sm font-bold text-stone-800">Additional notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Anything we should know about the device or the problem" class="backend-field h-auto py-3">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <p class="rounded-xl border border-brand-soft bg-brand-tint p-4 text-sm text-stone-700">We'll review your request and confirm by email within 24 hours.</p>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary">Submit booking request</button>
                    <a href="{{ route('services.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
