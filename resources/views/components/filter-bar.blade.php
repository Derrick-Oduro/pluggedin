@props([
    'action',
    'title' => 'Filters',
    'count' => null,
    'formClass' => 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-2',
])

<div class="bg-white/95 dark:bg-dark-secondary border border-gray-200/70 dark:border-gray-800 rounded-2xl p-4 sm:p-5 mb-10 shadow-sm">
    <div class="flex items-center justify-between gap-4 mb-2">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-text-secondary">{{ $title }}</h2>
        @if($count !== null)
            <p class="text-xs text-gray-500 dark:text-text-secondary">{{ $count }} items</p>
        @endif
    </div>

    <form method="GET" action="{{ $action }}" class="{{ $formClass }}">
        {{ $slot }}
    </form>
</div>
