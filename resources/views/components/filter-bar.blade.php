@props([
    'action',
    'title' => 'Filters',
    'count' => null,
    'formClass' => 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3',
])

<div class="backend-card mb-6">
    <div class="mb-3 flex items-center justify-between gap-4">
        <h2 class="text-[11px] font-extrabold uppercase tracking-[.14em] text-stone-500">{{ $title }}</h2>
        @if ($count !== null)<p class="text-xs font-bold text-stone-500">{{ $count }} items</p>@endif
    </div>
    <form method="GET" action="{{ $action }}" class="{{ $formClass }}">
        {{ $slot }}
    </form>
</div>
