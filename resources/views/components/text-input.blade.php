@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'backend-field disabled:bg-stone-100']) }}>
