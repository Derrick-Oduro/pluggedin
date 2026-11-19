@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-brand bg-brand-tint py-2 pe-4 ps-3 text-start text-base font-bold text-brand-deep focus:outline-none'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-semibold text-stone-600 transition hover:border-brand-soft hover:bg-stone-50 hover:text-stone-950 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
