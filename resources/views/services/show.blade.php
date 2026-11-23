<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-3xl">
            <x-page-header :badge="\App\Models\Service::TYPES[$service->type] ?? 'Service'" :title="$service->name" :back-href="route('services.index')" back-label="All repairs & upgrades" />

            <div class="backend-card sm:p-8">
                <p class="text-base leading-7 text-stone-700">{{ $service->description }}</p>

                <dl class="mt-6 grid gap-4 border-t border-stone-200 pt-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.12em] text-stone-500">Price</dt>
                        <dd class="mt-1 text-2xl font-extrabold tracking-tight text-stone-950">{{ $service->priceLabel() }}</dd>
                        @if($service->price_is_from)<dd class="text-xs text-stone-500">Final quote after we see the device</dd>@endif
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.12em] text-stone-500">Usually takes</dt>
                        <dd class="mt-1 font-bold text-stone-950">{{ $service->turnaround ?: 'We’ll confirm when you book' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.12em] text-stone-500">For</dt>
                        <dd class="mt-1 font-bold text-stone-950">{{ $service->devicesLabel() ?? 'Any device' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ auth()->check() ? route('bookings.create', $service) : route('login') }}" class="btn-primary">{{ auth()->check() ? 'Book this' : 'Log in to book' }}</a>
                    @if($service->partsCategory)
                        <a href="{{ route('products.index', ['category' => $service->partsCategory->slug]) }}" class="btn-secondary">Shop {{ strtolower($service->partsCategory->name) }} parts</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
