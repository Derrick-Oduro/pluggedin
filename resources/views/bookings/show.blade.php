<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Booking Detail</p>
                <h1 class="text-5xl sm:text-6xl font-bold">Booking #{{ $booking->id }}</h1>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Booking Status</h2>
                    <span class="text-xs px-3 py-1 rounded-full
                        @if($booking->status === 'pending') bg-yellow-500/20 text-yellow-500
                        @elseif($booking->status === 'approved') bg-blue-500/20 text-blue-500
                        @elseif($booking->status === 'completed') bg-green-500/20 text-green-500
                        @else bg-red-500/20 text-red-500
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-text-secondary">Requested on {{ $booking->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-800">
                <h2 class="text-xl font-bold mb-4">Booking Tracking</h2>
                <div class="space-y-2">
                    @forelse($booking->statusHistories as $history)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-semibold">
                                    {{ ucfirst($history->to_status) }}
                                    @if($history->from_status)
                                        <span class="text-gray-500 dark:text-text-secondary font-normal">from {{ ucfirst($history->from_status) }}</span>
                                    @endif
                                </p>
                                <span class="text-xs text-gray-500 dark:text-text-secondary">{{ $history->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-text-secondary mt-1">Updated by {{ $history->actor?->name ?? 'System' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600 dark:text-text-secondary">No tracking updates yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-800">
                <h2 class="text-xl font-bold mb-4">Service Information</h2>
                <p class="text-gray-600 dark:text-text-secondary mb-2"><span class="font-semibold text-gray-900 dark:text-text-primary">Service:</span> {{ $booking->service->name }}</p>
                <p class="text-gray-600 dark:text-text-secondary mb-2"><span class="font-semibold text-gray-900 dark:text-text-primary">Price:</span> ${{ number_format($booking->service->price, 2) }}</p>
                <p class="text-gray-600 dark:text-text-secondary mb-2"><span class="font-semibold text-gray-900 dark:text-text-primary">Device Model:</span> {{ $booking->device_model }}</p>
                <p class="text-gray-600 dark:text-text-secondary"><span class="font-semibold text-gray-900 dark:text-text-primary">Preferred Date:</span> {{ $booking->preferred_date->format('M d, Y') }}</p>
            </div>

            @if($booking->notes)
                <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-800">
                    <h2 class="text-xl font-bold mb-3">Your Notes</h2>
                    <p class="text-gray-600 dark:text-text-secondary">{{ $booking->notes }}</p>
                </div>
            @endif

            <div>
                <a href="{{ route('bookings.index') }}" class="inline-flex bg-orange hover:bg-orange-light text-white px-5 py-2.5 rounded-lg font-semibold transition">Back to My Bookings</a>
            </div>
        </div>
    </section>
</x-app-layout>
