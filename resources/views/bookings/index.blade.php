<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Repairs" title="My bookings" subtitle="Repair and upgrade requests you've made with PluggedIn.">
                <x-slot:actions>
                    <a href="{{ route('services.index') }}" class="btn-primary">Book a service</a>
                </x-slot:actions>
            </x-page-header>

            @if($bookings->count() > 0)
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <article class="backend-card">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-extrabold text-stone-950">{{ $booking->service->name }}</h3>
                                    <p class="mt-1 text-sm text-stone-600">{{ $booking->device_model }} · Preferred {{ $booking->preferred_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-stone-500">Booked {{ $booking->created_at->format('M d, Y') }}</p>
                                </div>
                                <x-status-badge :status="$booking->status" />
                            </div>
                            @if($booking->notes)
                                <p class="mt-4 rounded-xl bg-stone-50 p-3 text-sm text-stone-600">{{ $booking->notes }}</p>
                            @endif
                            <div class="mt-4 border-t border-stone-100 pt-4">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn-link">View details →</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">{{ $bookings->links() }}</div>
            @else
                <x-empty-state title="No bookings yet" message="Need a repair or an upgrade? Book a service and we'll confirm within 24 hours." action-label="View services" :action-href="route('services.index')" />
            @endif
        </div>
    </section>
</x-app-layout>
