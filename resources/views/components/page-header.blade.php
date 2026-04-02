@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div class="backend-card rounded-xl p-5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            @if(count($breadcrumbs) > 0)
                <nav class="mb-2 flex flex-wrap items-center gap-1 text-xs text-gray-500 dark:text-text-secondary" aria-label="Breadcrumb">
                    @foreach($breadcrumbs as $crumb)
                        @if(!$loop->first)
                            <span>/</span>
                        @endif

                        @if(!empty($crumb['href']))
                            <a href="{{ $crumb['href'] }}" class="hover:text-orange transition">{{ $crumb['label'] }}</a>
                        @else
                            <span class="text-gray-700 dark:text-text-primary">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif

            <h1 class="text-2xl font-bold">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>
</div>
