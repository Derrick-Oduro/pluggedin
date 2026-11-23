{{-- Add/edit form for a workshop service. --}}
@php
    $key = $service?->id ?? 'new';
    $useOld = (string) old('form_key') === (string) $key;
    $val = fn ($field, $default = null) => $useOld ? old($field, $default) : $default;
    $err = fn ($field) => $useOld && $errors->has($field) ? $errors->first($field) : null;
    $devices = $val('device_types', $service?->device_types ?? []);
@endphp

<form method="POST" action="{{ $service ? route('superadmin.services.update', $service) : route('superadmin.services.store') }}" class="grid gap-4 sm:grid-cols-2"
    x-data="{ type: @js($val('type', $service?->type ?? $defaultType ?? 'repair')) }">
    @csrf
    @if($service) @method('PATCH') @endif
    <input type="hidden" name="form_key" value="{{ $key }}">

    <div>
        <label class="mb-1 block text-xs font-bold text-stone-600" for="s{{ $key }}-name">Service name</label>
        <input id="s{{ $key }}-name" name="name" value="{{ $val('name', $service?->name) }}" required maxlength="120" class="backend-field" placeholder="e.g. Battery replacement">
        @if($err('name'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('name') }}</p>@endif
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold text-stone-600" for="s{{ $key }}-type">Kind</label>
        <select id="s{{ $key }}-type" name="type" x-model="type" class="backend-field">
            @foreach(\App\Models\Service::TYPES as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
    </div>

    <div class="sm:col-span-2">
        <label class="mb-1 block text-xs font-bold text-stone-600" for="s{{ $key }}-desc">What's included <span class="font-normal text-stone-400">(customers see this)</span></label>
        <textarea id="s{{ $key }}-desc" name="description" rows="2" required maxlength="1000" class="backend-field h-auto py-2.5" placeholder="e.g. New battery fitted and tested. We dispose of the old one safely.">{{ $val('description', $service?->description) }}</textarea>
        @if($err('description'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('description') }}</p>@endif
    </div>

    <div>
        <label class="mb-1 block text-xs font-bold text-stone-600" for="s{{ $key }}-price">Price (GH₵) <span class="font-normal text-stone-400">0 = free</span></label>
        <input id="s{{ $key }}-price" type="number" step="0.01" min="0" name="price" value="{{ $val('price', $service?->price) }}" required class="backend-field">
        <label class="mt-2 inline-flex items-center gap-2 text-xs font-bold text-stone-700">
            <input type="checkbox" name="price_is_from" value="1" class="rounded text-brand-deep focus:ring-brand" @checked($val('price_is_from', $service?->price_is_from ?? false))>
            Starting price ("From GH₵…"), final quote depends on the device
        </label>
        @if($err('price'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('price') }}</p>@endif
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold text-stone-600" for="s{{ $key }}-turn">Usual turnaround <span class="font-normal text-stone-400">(optional)</span></label>
        <input id="s{{ $key }}-turn" name="turnaround" value="{{ $val('turnaround', $service?->turnaround) }}" maxlength="60" class="backend-field" placeholder="e.g. Same day, 1–2 days">
    </div>

    <fieldset class="sm:col-span-2">
        <legend class="mb-1 text-xs font-bold text-stone-600">Devices <span class="font-normal text-stone-400">(none ticked = any device)</span></legend>
        <div class="flex flex-wrap gap-2">
            @foreach(\App\Models\Service::DEVICE_TYPES as $value => $label)
                <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-stone-200 px-3 py-2 text-sm font-semibold has-[:checked]:border-brand has-[:checked]:bg-brand-tint">
                    <input type="checkbox" name="device_types[]" value="{{ $value }}" class="rounded text-brand-deep focus:ring-brand" @checked(in_array($value, (array) $devices, true))>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </fieldset>

    <div class="sm:col-span-2" x-show="type === 'upgrade'" x-cloak>
        <label class="mb-1 block text-xs font-bold text-stone-600" for="s{{ $key }}-cat">Parts to link to <span class="font-normal text-stone-400">(shows “Shop parts” on the Repairs page)</span></label>
        <select id="s{{ $key }}-cat" name="parts_category_id" class="backend-field sm:w-72">
            <option value="">No link</option>
            @foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) $val('parts_category_id', $service?->parts_category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach
        </select>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4 sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm font-bold text-stone-800">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded text-brand-deep focus:ring-brand" @checked($val('is_active', $service?->is_active ?? true))>
            Show on the Repairs page
        </label>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary h-10 px-4" x-on:click="$dispatch('close-form')">Cancel</button>
            <button class="backend-btn-primary h-10">{{ $service ? 'Save changes' : 'Add service' }}</button>
        </div>
    </div>
</form>
