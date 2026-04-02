<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Booking</p>
                <h1 class="text-5xl sm:text-6xl font-bold mb-4">Book {{ $service->name }}</h1>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-8 border border-gray-200 dark:border-gray-800">
                <h2 class="text-2xl font-bold mb-2">{{ $service->name }}</h2>
                <p class="text-gray-600 dark:text-text-secondary mb-4">{{ $service->description }}</p>
                <p class="text-3xl font-bold text-orange">${{ number_format($service->price, 2) }}</p>
            </div>

            <form action="{{ route('bookings.store') }}" method="POST" class="bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200 dark:border-gray-800">
                @csrf

                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Device Model *</label>
                        <input type="text" name="device_model" value="{{ old('device_model') }}" required
                               placeholder="e.g., Dell Inspiron 15, MacBook Pro 2020"
                               class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                        @error('device_model')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Preferred Date *</label>
                        <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" required
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                        @error('preferred_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Additional Notes (Optional)</label>
                        <textarea name="notes" rows="4"
                                  placeholder="Any specific requirements or information we should know..."
                                  class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-orange-50 dark:bg-dark border border-orange/20 rounded-lg p-4 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m6.75 0A9.75 9.75 0 1 1 3.25 12a9.75 9.75 0 0 1 19.5 0Z" />
                        </svg>
                        <p class="text-sm text-gray-600 dark:text-text-secondary">
                            After submitting, we will review your booking request and confirm via email within 24 hours.
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                            Submit Booking Request
                        </button>
                        <a href="{{ route('services.index') }}" class="btn-secondary px-8 py-3 text-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
