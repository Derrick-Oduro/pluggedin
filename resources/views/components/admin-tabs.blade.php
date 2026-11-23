{{-- Pill tabs with counts for admin queues. $tabs: key => label; $counts: key => number. --}}
@props(['tabs', 'counts' => [], 'current', 'route', 'param' => 'tab', 'keep' => []])

<nav {{ $attributes->merge(['class' => '-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:px-0 [&::-webkit-scrollbar]:hidden']) }} aria-label="Filter">
    @foreach($tabs as $key => $label)
        @php($active = (string) $current === (string) $key)
        <a href="{{ route($route, array_merge($keep, [$param => $key])) }}"
            class="inline-flex shrink-0 items-center gap-2 rounded-full border px-4 py-2 text-sm font-bold transition {{ $active ? 'border-stone-950 bg-stone-950 text-white' : 'border-stone-200 bg-white text-stone-700 hover:border-stone-400' }}"
            @if($active) aria-current="page" @endif>
            {{ $label }}
            @isset($counts[$key])
                <span class="rounded-full px-1.5 text-xs {{ $active ? 'bg-brand text-stone-950' : 'bg-stone-100 text-stone-500' }}">{{ $counts[$key] }}</span>
            @endisset
        </a>
    @endforeach
</nav>
