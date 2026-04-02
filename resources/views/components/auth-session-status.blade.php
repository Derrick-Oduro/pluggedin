@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-green-300/70 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-500/30 dark:bg-green-900/20 dark:text-green-300']) }}>
        {{ $status }}
    </div>
@endif
