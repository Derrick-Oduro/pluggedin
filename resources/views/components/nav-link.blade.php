@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 pt-1.5 pb-1 border-b-2 border-orange text-sm font-semibold text-orange transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 pt-1.5 pb-1 border-b-2 border-transparent text-sm font-medium text-gray-600 dark:text-text-secondary hover:text-orange hover:border-orange/40 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
