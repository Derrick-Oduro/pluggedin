@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1.5 block text-sm font-bold text-stone-800']) }}>
    {{ $value ?? $slot }}
</label>
