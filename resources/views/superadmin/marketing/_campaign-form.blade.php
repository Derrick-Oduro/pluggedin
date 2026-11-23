{{-- Create/edit form for a discount campaign. Every field is always sent so saving never wipes anything. --}}
@php
    $key = $campaign?->id ?? 'new';
    $useOld = (string) (session('editing') ?? old('form_key')) === (string) $key;
    $val = fn ($field, $default = null) => $useOld ? old($field, $default) : $default;
    $err = fn ($field) => $useOld && $errors->has($field) ? $errors->first($field) : null;
    $id = fn ($field) => "c{$key}-{$field}";
@endphp

<form method="POST" action="{{ $campaign ? route('superadmin.marketing.campaigns.update', $campaign) : route('superadmin.marketing.campaigns.store') }}" class="space-y-4"
    x-data="{ kind: @js($val('code', $campaign?->code) ? 'code' : 'auto') }">
    @csrf
    @if($campaign) @method('PATCH') @endif
    <input type="hidden" name="form_key" value="{{ $key }}">
    <input type="hidden" name="section" value="discounts">

    <div class="grid gap-4 sm:grid-cols-[1fr_150px]">
        <div>
            <label for="{{ $id('name') }}" class="mb-1 block text-xs font-bold text-stone-600">Campaign name</label>
            <input id="{{ $id('name') }}" name="name" value="{{ $val('name', $campaign?->name) }}" required maxlength="255" class="backend-field" placeholder="e.g. Back to school">
            @if($err('name'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('name') }}</p>@endif
        </div>
        <div>
            <label for="{{ $id('pct') }}" class="mb-1 block text-xs font-bold text-stone-600">Discount</label>
            <div class="relative">
                <input id="{{ $id('pct') }}" type="number" name="discount_percent" min="1" max="95" required value="{{ $val('discount_percent', $campaign?->discount_percent) }}" class="backend-field pr-9">
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm font-bold text-stone-400">% off</span>
            </div>
            @if($err('discount_percent'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('discount_percent') }}</p>@endif
        </div>
    </div>

    {{-- Who gets it --}}
    <fieldset>
        <legend class="mb-1 text-xs font-bold text-stone-600">Who gets it</legend>
        <div class="grid gap-2 sm:grid-cols-2">
            <label class="flex cursor-pointer gap-2 rounded-xl border p-3 text-sm" x-bind:class="kind === 'auto' ? 'border-brand bg-brand-tint' : 'border-stone-200'">
                <input type="radio" value="auto" x-model="kind" class="mt-0.5 text-brand-deep focus:ring-brand">
                <span><b class="block text-stone-950">Everyone, automatically</b><span class="text-xs text-stone-600">Applied at checkout and shown on the homepage.</span></span>
            </label>
            <label class="flex cursor-pointer gap-2 rounded-xl border p-3 text-sm" x-bind:class="kind === 'code' ? 'border-brand bg-brand-tint' : 'border-stone-200'">
                <input type="radio" value="code" x-model="kind" class="mt-0.5 text-brand-deep focus:ring-brand">
                <span><b class="block text-stone-950">Only with a promo code</b><span class="text-xs text-stone-600">Shoppers type the code at checkout.</span></span>
            </label>
        </div>
        <div class="mt-2" x-show="kind === 'code'" x-cloak>
            <label for="{{ $id('code') }}" class="sr-only">Promo code</label>
            <input id="{{ $id('code') }}" name="code" value="{{ $val('code', $campaign?->code) }}" maxlength="40" class="backend-field uppercase" placeholder="e.g. BACK2SCHOOL" x-bind:disabled="kind !== 'code'" x-bind:required="kind === 'code'">
            @if($err('code'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('code') }}</p>@endif
        </div>
    </fieldset>

    {{-- When --}}
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <p class="mb-1 text-xs font-bold text-stone-600">Starts <span class="font-normal text-stone-400">(empty = straight away)</span></p>
            <div class="flex gap-2">
                <input type="date" name="starts_date" aria-label="Start date" value="{{ $val('starts_date', $campaign?->starts_at?->toDateString()) }}" class="backend-field">
                <input type="time" name="starts_time" aria-label="Start time" value="{{ $val('starts_time', $campaign?->starts_at?->format('H:i')) }}" class="backend-field w-32">
            </div>
        </div>
        <div>
            <p class="mb-1 text-xs font-bold text-stone-600">Ends <span class="font-normal text-stone-400">(empty = no end date)</span></p>
            <div class="flex gap-2">
                <input type="date" name="ends_date" aria-label="End date" value="{{ $val('ends_date', $campaign?->ends_at?->toDateString()) }}" class="backend-field">
                <input type="time" name="ends_time" aria-label="End time" value="{{ $val('ends_time', $campaign?->ends_at?->format('H:i')) }}" class="backend-field w-32">
            </div>
            @if($err('ends_date'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('ends_date') }}</p>@endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="{{ $id('max') }}" class="mb-1 block text-xs font-bold text-stone-600">Usage limit <span class="font-normal text-stone-400">(empty = unlimited)</span></label>
            <input id="{{ $id('max') }}" type="number" name="max_uses" min="1" value="{{ $val('max_uses', $campaign?->max_uses) }}" class="backend-field" placeholder="e.g. 50 orders">
            @if($campaign && $campaign->used_count)<p class="mt-1 text-xs text-stone-500">Used {{ $campaign->used_count }} {{ Str::plural('time', $campaign->used_count) }} so far.</p>@endif
            @if($err('max_uses'))<p class="mt-1 text-xs font-semibold text-red-600">{{ $err('max_uses') }}</p>@endif
        </div>
        <div>
            <label for="{{ $id('desc') }}" class="mb-1 block text-xs font-bold text-stone-600">Note <span class="font-normal text-stone-400">(internal, optional)</span></label>
            <input id="{{ $id('desc') }}" name="description" value="{{ $val('description', $campaign?->description) }}" maxlength="2000" class="backend-field" placeholder="e.g. For the Legon freshers fair">
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4">
        <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-bold text-stone-800">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded text-brand-deep focus:ring-brand" @checked($val('is_active', $campaign ? $campaign->is_active : true))>
            Switched on <span class="font-normal text-stone-500">(untick to save it paused)</span>
        </label>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary h-10 px-4" x-on:click="$dispatch('close-form')">Cancel</button>
            <button class="backend-btn-primary h-10">{{ $campaign ? 'Save changes' : 'Create campaign' }}</button>
        </div>
    </div>
</form>
