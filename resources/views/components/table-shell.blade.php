@props(['title' => null])

<div {{ $attributes->merge(['class' => 'backend-table-wrap']) }}>
    @if ($title)
        <div class="border-b border-stone-100 px-5 py-4">
            <h2 class="text-base font-extrabold text-stone-950">{{ $title }}</h2>
        </div>
    @endif
    <div class="overflow-x-auto">{{ $slot }}</div>
</div>
