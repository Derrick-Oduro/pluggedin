@props(['status'])

@php
    $key = strtolower((string) $status);
    $tone = match (true) {
        in_array($key, ['completed', 'approved', 'paid', 'active', 'resolved', 'accepted', 'delivered', 'success', 'successful', 'visible', 'verified']) => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        in_array($key, ['confirmed', 'processing', 'quoted', 'under_review', 'shipped', 'ready_for_pickup', 'out_for_delivery', 'in_progress']) => 'bg-sky-50 text-sky-700 ring-sky-200',
        in_array($key, ['pending', 'submitted', 'open', 'paused', 'awaiting_payment', 'unpaid']) => 'bg-amber-50 text-amber-700 ring-amber-200',
        in_array($key, ['cancelled', 'rejected', 'declined', 'failed', 'suspended', 'hidden', 'refunded']) => 'bg-red-50 text-red-700 ring-red-200',
        default => 'bg-stone-100 text-stone-700 ring-stone-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-[.08em] ring-1 ring-inset $tone"]) }}>
    {{ $slot->isEmpty() ? \Illuminate\Support\Str::headline($key) : $slot }}
</span>
