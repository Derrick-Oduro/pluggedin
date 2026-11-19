@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'backHref',
    'backLabel' => 'Back',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Save product',
    'categories',
    'product' => null,
    'defaultCondition' => null,
    'defaultStock' => 1,
])

{{-- One layout for every product create/edit page: admin, seller uploads and shop inventory. --}}
<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-4xl">
            <a href="{{ $backHref }}" class="inline-flex items-center gap-2 text-sm font-bold text-stone-600 hover:text-brand-deep">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                {{ $backLabel }}
            </a>

            <div class="mt-6">
                @if ($badge)<p class="brand-badge">{{ $badge }}</p>@endif
                <h1 class="section-title mt-3">{{ $title }}</h1>
                @if ($subtitle)<p class="section-subtitle mt-2">{{ $subtitle }}</p>@endif
            </div>

            @if (session('error'))
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ session('error') }}</div>
            @endif

            {{ $slot }}

            <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="mt-8">
                @csrf
                @if (strtoupper($method) !== 'POST') @method($method) @endif

                <x-product-form-fields :categories="$categories" :product="$product" :default-condition="$defaultCondition" :default-stock="$defaultStock" />

                <div class="sticky bottom-3 z-10 mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-stone-200 bg-white/95 p-3 shadow-[0_12px_35px_-26px_rgba(41,30,23,0.4)] backdrop-blur">
                    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
                    <a href="{{ $backHref }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
