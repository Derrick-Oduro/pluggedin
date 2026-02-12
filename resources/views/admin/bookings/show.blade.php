<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Booking #{{ $booking->id }}</h1>
            <a href="{{ route('admin.bookings.index') }}" class="text-orange hover:text-orange-light">
                ← Back to Bookings
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Customer Info -->
            <div class="bg-dark-secondary rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Customer Information</h2>
                <div class="space-y-2 text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Name:</span> {{ $booking->user->name }}</p>
                    <p><span class="font-semibold text-text-primary">Email:</span> {{ $booking->user->email }}</p>
                </div>
            </div>

            <!-- Service Info -->
            <div class="bg-dark-secondary rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Service Details</h2>
                <div class="space-y-2 text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Service:</span> {{ $booking->service->name }}</p>
                    <p><span class="font-semibold text-text-primary">Price:</span> <span class="text-orange font-bold">${{ number_format($booking->service->price, 2) }}</span></p>
                    <p><span class="font-semibold text-text-primary">Device Model:</span> {{ $booking->device_model }}</p>
                    <p><span class="font-semibold text-text-primary">Preferred Date:</span> {{ $booking->preferred_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($booking->notes)
            <div class="bg-dark-secondary rounded-lg p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">Customer Notes</h2>
                <p class="text-text-secondary">{{ $booking->notes }}</p>
            </div>
        @endif

        <!-- Update Status -->
        <div class="bg-dark-secondary rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Update Booking Status</h2>
            <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="flex gap-4 items-end">
                @csrf
                @method('PATCH')

                <div class="flex-1">
                    <label class="block text-sm mb-2">Status</label>
                    <select name="status" class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <button type="submit" class="bg-orange hover:bg-orange-light text-white px-6 py-2 rounded-lg font-semibold transition">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
