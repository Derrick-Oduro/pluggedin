<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">Book {{ $service->name }}</h1>

        <div class="bg-dark-secondary rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-2">{{ $service->name }}</h2>
            <p class="text-text-secondary mb-4">{{ $service->description }}</p>
            <p class="text-3xl font-bold text-orange">${{ number_format($service->price, 2) }}</p>
        </div>

        <form action="{{ route('bookings.store') }}" method="POST" class="bg-dark-secondary rounded-lg p-8">
            @csrf

            <input type="hidden" name="service_id" value="{{ $service->id }}">

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Device Model *</label>
                    <input type="text" name="device_model" value="{{ old('device_model') }}" required
                           placeholder="e.g., Dell Inspiron 15, MacBook Pro 2020"
                           class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('device_model')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Preferred Date *</label>
                    <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('preferred_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Additional Notes (Optional)</label>
                    <textarea name="notes" rows="4"
                              placeholder="Any specific requirements or information we should know..."
                              class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-dark border border-gray-700 rounded p-4">
                    <p class="text-sm text-text-secondary">
                        📅 After submitting, we'll review your booking request and confirm with you via email within 24 hours.
                    </p>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Booking Request
                    </button>
                    <a href="{{ route('services.index') }}" class="px-8 py-3 rounded-lg font-semibold transition border border-gray-700 hover:bg-gray-800 text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
