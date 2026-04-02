@props([
    'title' => 'Nothing to show yet',
    'message' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="backend-card rounded-xl p-8 text-center">
    <p class="text-base font-semibold">{{ $title }}</p>
    @if($message)
        <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">{{ $message }}</p>
    @endif

    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="backend-btn-primary mt-4">{{ $actionLabel }}</a>
    @endif
</div>
