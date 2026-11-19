@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-brand px-3 pb-1 pt-1.5 text-sm font-bold text-brand-deep transition'
            : 'inline-flex items-center border-b-2 border-transparent px-3 pb-1 pt-1.5 text-sm font-semibold text-stone-600 transition hover:border-brand-soft hover:text-stone-950';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
