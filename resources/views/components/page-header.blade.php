@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'breadcrumbs' => [],
    'backHref' => null,
    'backLabel' => 'Back',
])

{{-- Standard page heading used across storefront, account and admin pages. --}}
<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    @if ($backHref)
        <a href="{{ $backHref }}" class="mb-5 inline-flex items-center gap-2 text-sm font-bold text-stone-600 hover:text-brand-deep">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
            {{ $backLabel }}
        </a>
    @endif

    @if (count($breadcrumbs) > 0)
        <nav class="mb-4 flex flex-wrap items-center gap-1.5 text-xs font-bold text-stone-500" aria-label="Breadcrumb">
            @foreach ($breadcrumbs as $crumb)
                @if (! $loop->first)<span class="text-stone-300">/</span>@endif
                @if (! empty($crumb['href']))
                    <a href="{{ $crumb['href'] }}" class="hover:text-brand-deep">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-stone-800">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0">
            @if ($badge)<span class="brand-badge">{{ $badge }}</span>@endif
            <h1 @class(['section-title', 'mt-3' => $badge])>{{ $title }}</h1>
            @if ($subtitle)<p class="section-subtitle mt-2 max-w-2xl">{{ $subtitle }}</p>@endif
        </div>

        @isset($actions)
            <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>
</div>
