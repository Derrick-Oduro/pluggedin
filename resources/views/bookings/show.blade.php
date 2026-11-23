<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            <x-page-header
                badge="Booking detail"
                :title="'Booking #'.$booking->id"
                :subtitle="'Requested '.$booking->created_at->format('M d, Y · H:i')"
                :back-href="route('bookings.index')"
                back-label="Back to my bookings"
            >
                <x-slot:actions>
                    <x-status-badge :status="$booking->status" />
                </x-slot:actions>
            </x-page-header>

            <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                <div class="space-y-6">
                    <section class="backend-card">
                        <h2 class="text-lg font-extrabold text-stone-950">Service</h2>
                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            @foreach([
                                'Service' => $booking->service->name,
                                'Price' => $booking->service->priceLabel(),
                                'Device' => $booking->device_model,
                                'Preferred date' => $booking->preferred_date->format('M d, Y'),
                                'Handover' => $booking->handoverLabel(),
                                'Contact phone' => $booking->contact_phone ?: '—',
                            ] as $label => $value)
                                <div>
                                    <dt class="text-[11px] font-extrabold uppercase tracking-[.12em] text-stone-500">{{ $label }}</dt>
                                    <dd class="mt-1 font-bold text-stone-950">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    @if($booking->notes)
                        <section class="backend-card">
                            <h2 class="text-lg font-extrabold text-stone-950">Your notes</h2>
                            <p class="mt-3 whitespace-pre-line text-sm text-stone-600">{{ $booking->notes }}</p>
                        </section>
                    @endif
                </div>

                <section class="backend-card">
                    <h2 class="text-lg font-extrabold text-stone-950">Tracking</h2>
                    <ol class="mt-5">
                        @forelse($booking->statusHistories as $history)
                            <li class="relative flex gap-3 pb-5 last:pb-0">
                                @unless($loop->last)<span class="absolute left-[9px] top-6 h-full w-0.5 bg-brand-soft"></span>@endunless
                                <span class="relative z-10 mt-0.5 h-5 w-5 shrink-0 rounded-full border-2 border-brand bg-brand"></span>
                                <div>
                                    <p class="text-sm font-extrabold text-stone-950">{{ \Illuminate\Support\Str::headline($history->to_status) }}</p>
                                    <p class="text-xs text-stone-500">{{ $history->created_at->format('M d, Y · H:i') }} · by {{ $history->actor?->name ?? 'System' }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-stone-600">No updates yet. We'll confirm your booking within 24 hours.</li>
                        @endforelse
                    </ol>
                </section>
            </div>
        </div>
    </section>
</x-app-layout>
