<x-app-layout>
    @php
        $devices = collect(\App\Models\Service::DEVICE_TYPES)->only($service->device_types ?: array_keys(\App\Models\Service::DEVICE_TYPES));
        $isDiagnosis = $service->type === 'diagnosis';
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-3xl">
            <x-page-header :badge="\App\Models\Service::TYPES[$service->type] ?? 'Book a service'" :title="'Book: '.$service->name" :back-href="route('services.index')" back-label="All repairs & upgrades" />

            <div class="mb-6 flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-lg text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                <div class="shrink-0 sm:text-right">
                    <p class="text-2xl font-extrabold tracking-tight text-stone-950">{{ $service->priceLabel() }}</p>
                    @if($service->turnaround)<p class="text-xs text-stone-500">Usually {{ Str::lower($service->turnaround) }}</p>@endif
                </div>
            </div>

            <form action="{{ route('bookings.store') }}" method="POST" class="backend-card space-y-6" x-data="{ handover: @js(old('handover_method', 'drop_off')) }">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                {{-- Device --}}
                <fieldset>
                    <legend class="mb-2 text-sm font-bold text-stone-800">Your device <span class="text-brand-deep">*</span></legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach($devices as $value => $label)
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-bold has-[:checked]:border-brand has-[:checked]:bg-brand-tint">
                                <input type="radio" name="device_type" value="{{ $value }}" class="text-brand-deep focus:ring-brand" @checked(old('device_type', $devices->count() === 1 ? $value : null) === $value) required>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('device_type')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    <label for="device_model" class="sr-only">Make and model</label>
                    <input id="device_model" type="text" name="device_model" value="{{ old('device_model') }}" required maxlength="120" placeholder="Make and model, e.g. HP EliteBook 840 G5 or iPhone 11" class="backend-field mt-3">
                    @error('device_model')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </fieldset>

                <div>
                    <label for="notes" class="mb-1.5 block text-sm font-bold text-stone-800">{{ $isDiagnosis ? 'What’s happening?' : 'Anything we should know?' }} @if($isDiagnosis)<span class="text-brand-deep">*</span>@endif</label>
                    <textarea id="notes" name="notes" rows="3" @required($isDiagnosis) maxlength="2000" placeholder="{{ $isDiagnosis ? 'e.g. It turns off at 40% battery and gets very hot' : 'e.g. Screen has a crack in the top corner, touch still works' }}" class="backend-field h-auto py-3">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Handover --}}
                <fieldset>
                    <legend class="mb-2 text-sm font-bold text-stone-800">How will we get the device? <span class="text-brand-deep">*</span></legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach(['drop_off' => ['I’ll drop it off', 'At the PluggedIn point on campus.'], 'pickup' => ['Collect it from me', 'We come to your hall or address.']] as $value => [$label, $hint])
                            <label class="flex cursor-pointer gap-3 rounded-xl border p-4 transition" x-bind:class="handover === @js($value) ? 'border-brand bg-brand-tint' : 'border-stone-200 hover:border-stone-300'">
                                <input type="radio" name="handover_method" value="{{ $value }}" x-model="handover" class="mt-0.5 text-brand-deep focus:ring-brand">
                                <span><span class="block font-extrabold text-stone-950">{{ $label }}</span><span class="mt-0.5 block text-sm text-stone-600">{{ $hint }}</span></span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-3" x-show="handover === 'pickup'" x-cloak>
                        <label for="pickup_location" class="mb-1.5 block text-sm font-bold text-stone-800">Where should we collect it?</label>
                        <input id="pickup_location" type="text" name="pickup_location" value="{{ old('pickup_location') }}" maxlength="255" placeholder="Hall, room or landmark" class="backend-field" x-bind:required="handover === 'pickup'">
                        @error('pickup_location')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </fieldset>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="preferred_date" class="mb-1.5 block text-sm font-bold text-stone-800">Preferred day <span class="text-brand-deep">*</span></label>
                        <input id="preferred_date" type="date" name="preferred_date" value="{{ old('preferred_date') }}" required min="{{ now()->addDay()->toDateString() }}" max="{{ now()->addMonths(3)->toDateString() }}" class="backend-field">
                        @error('preferred_date')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="mb-1.5 block text-sm font-bold text-stone-800">Phone number <span class="text-brand-deep">*</span></label>
                        <input id="contact_phone" type="tel" name="contact_phone" value="{{ old('contact_phone') }}" required maxlength="40" placeholder="024 123 4567" class="backend-field">
                        @error('contact_phone')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <p class="rounded-xl border border-brand-soft bg-brand-tint p-4 text-sm text-stone-700">
                    Nothing is charged now. We’ll call you to confirm the time,
                    @if($service->price_is_from || $isDiagnosis) check the device and agree the final price before any work starts. @else and you pay when the job is done. @endif
                </p>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary">Send booking</button>
                    <a href="{{ route('services.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
