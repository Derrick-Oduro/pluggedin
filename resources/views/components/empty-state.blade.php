@props([
    'title' => 'Nothing to show yet',
    'message' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'backend-card px-6 py-14 text-center']) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-tint text-brand">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6m16 0v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4m16 0h-4l-2 2h-4l-2-2H4" /></svg>
    </div>
    <p class="mt-4 font-extrabold text-stone-950">{{ $title }}</p>
    @if ($message)<p class="mx-auto mt-1 max-w-md text-sm text-stone-600">{{ $message }}</p>@endif
    {{ $slot }}
    @if ($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="btn-primary mt-6">{{ $actionLabel }}</a>
    @endif
</div>
