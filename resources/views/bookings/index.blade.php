<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Bookings</p>
                <h1 class="text-5xl sm:text-6xl font-bold">My Bookings</h1>
            </div>

            @if($bookings->count() > 0)
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold mb-1">{{ $booking->service->name }}</h3>
                                    <p class="text-gray-600 dark:text-text-secondary text-sm">Device: {{ $booking->device_model }}</p>
                                    <p class="text-gray-600 dark:text-text-secondary text-sm">Preferred Date: {{ $booking->preferred_date->format('M d, Y') }}</p>
                                    <p class="text-gray-600 dark:text-text-secondary text-sm">Booked: {{ $booking->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full
                                    @if($booking->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($booking->status === 'approved') bg-blue-500/20 text-blue-500
                                    @elseif($booking->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>

                            @if($booking->notes)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <p class="text-gray-600 dark:text-text-secondary text-sm"><span class="font-semibold text-text-primary">Notes:</span> {{ $booking->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 bg-white dark:bg-dark-secondary rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 text-center py-14 px-6">
                    <p class="text-gray-600 dark:text-text-secondary text-xl mb-6">You haven't booked any services yet</p>
                    <a href="{{ route('services.index') }}" class="inline-block bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        View Services
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
