@props(['dark' => false, 'size' => 'md'])

{{-- PluggedIn wordmark: heavy display type with the plug mark, as on the flyer. --}}
@php
    $text = ['sm' => 'text-xl', 'md' => 'text-2xl', 'lg' => 'text-4xl'][$size] ?? 'text-2xl';
    $badge = ['sm' => 'h-8 w-8', 'md' => 'h-9 w-9', 'lg' => 'h-12 w-12'][$size] ?? 'h-9 w-9';
@endphp
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-2']) }}>
    <span @class(['font-display leading-none tracking-normal', $text, 'text-white' => $dark, 'text-stone-950' => ! $dark])>PluggedIn</span>
    <span class="{{ $badge }} flex items-center justify-center rounded-full bg-brand">
        <img src="{{ asset('images/icons8-plug-64.png') }}" alt="" class="h-3/5 w-3/5">
    </span>
</a>
