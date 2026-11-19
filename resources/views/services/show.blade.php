<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-3xl">
            <x-page-header badge="Repairs & upgrades" :title="$service->name" :back-href="route('services.index')" back-label="All services" />

            <div class="backend-card sm:p-8">
                <p class="text-base leading-7 text-stone-600">{{ $service->description }}</p>

                <div class="mt-6 flex items-center justify-between border-t border-stone-200 pt-6">
                    <span class="text-sm font-bold text-stone-500">Service price</span>
                    <span class="text-3xl font-extrabold tracking-[-.04em] text-stone-950">GH₵{{ number_format($service->price, 2) }}</span>
                </div>

                @auth
                    <a href="{{ route('bookings.create', $service) }}" class="btn-primary mt-6 w-full">Book this service</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary mt-6 w-full">Log in to book</a>
                @endauth
            </div>
        </div>
    </section>
</x-app-layout>
