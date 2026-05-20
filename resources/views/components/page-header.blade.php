@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div class="backend-card rounded-xl p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            @if(count($breadcrumbs) > 0)
                <nav class="mb-1 flex flex-wrap items-center gap-1 text-[10px] uppercase tracking-[0.12em] text-gray-500 dark:text-text-secondary" aria-label="Breadcrumb">
                    @foreach($breadcrumbs as $crumb)
                        @if(!$loop->first)
                            <span class="opacity-50">/</span>
                        @endif

                        @if(!empty($crumb['href']))
                            <a href="{{ $crumb['href'] }}" class="hover:text-orange transition">{{ $crumb['label'] }}</a>
                        @else
                            <span class="text-gray-700 dark:text-text-primary">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif

            <h1 class="text-lg sm:text-xl font-bold tracking-tight">{{ $title }}</h1>
            @if($subtitle)
                <p class="mt-1 text-sm text-gray-600 dark:text-text-secondary max-w-3xl">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-2 sm:justify-end">{{ $actions }}</div>
        @endisset
    </div>
</div>
