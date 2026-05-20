@props([
    'title' => 'Nothing to show yet',
    'message' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="backend-card rounded-xl p-6 text-center">
    <p class="text-sm font-semibold sm:text-base">{{ $title }}</p>
    @if($message)
        <p class="mt-1 text-sm text-gray-600 dark:text-text-secondary">{{ $message }}</p>
    @endif

    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="backend-btn-primary mt-4 px-3 text-sm">{{ $actionLabel }}</a>
    @endif
</div>
