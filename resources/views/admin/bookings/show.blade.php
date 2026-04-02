<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-4 sticky top-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Dashboard</a>
                        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Orders</a>
                        <a href="{{ route('admin.reviews.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Review Moderation</a>
                        <a href="{{ route('admin.bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm bg-orange/15 text-orange">Bookings</a>
                        <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Products</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Booking #{{ $booking->id }}</h1>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-orange hover:text-orange-light">Back to Bookings</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Info -->
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-3">Customer Information</h2>
                <div class="space-y-2 text-gray-600 dark:text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Name:</span> {{ $booking->user->name }}</p>
                    <p><span class="font-semibold text-text-primary">Email:</span> {{ $booking->user->email }}</p>
                </div>
            </div>

            <!-- Service Info -->
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-3">Service Details</h2>
                <div class="space-y-2 text-gray-600 dark:text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Service:</span> {{ $booking->service->name }}</p>
                    <p><span class="font-semibold text-text-primary">Price:</span> <span class="text-orange font-bold">${{ number_format($booking->service->price, 2) }}</span></p>
                    <p><span class="font-semibold text-text-primary">Device Model:</span> {{ $booking->device_model }}</p>
                    <p><span class="font-semibold text-text-primary">Preferred Date:</span> {{ $booking->preferred_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($booking->notes)
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-3">Customer Notes</h2>
                <p class="text-gray-600 dark:text-text-secondary">{{ $booking->notes }}</p>
            </div>
        @endif

        <!-- Update Status -->
        <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
            <h2 class="text-lg font-semibold mb-3">Update Booking Status</h2>
            <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="flex gap-4 items-end">
                @csrf
                @method('PATCH')

                <div class="flex-1">
                    <label class="block text-sm mb-2">Status</label>
                    <select name="status" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
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
        </div>
    </div>
</x-app-layout>
