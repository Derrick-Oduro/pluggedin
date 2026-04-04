@props([
    'title' => null,
])

<div class="backend-table-wrap">
    @if($title)
        <div class="px-4 pt-4">
            <h2 class="text-lg font-semibold">{{ $title }}</h2>
        </div>
    @endif

    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
</div>
