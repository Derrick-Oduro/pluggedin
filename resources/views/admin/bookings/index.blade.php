<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">Manage Bookings</h1>

        <div class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="text-left p-4">ID</th>
                        <th class="text-left p-4">Customer</th>
                        <th class="text-left p-4">Service</th>
                        <th class="text-left p-4">Device</th>
                        <th class="text-left p-4">Preferred Date</th>
                        <th class="text-left p-4">Status</th>
                        <th class="text-left p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr class="border-b border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="p-4 font-semibold">#{{ $booking->id }}</td>
                            <td class="p-4">{{ $booking->user->name }}</td>
                            <td class="p-4">{{ $booking->service->name }}</td>
                            <td class="p-4 text-gray-600 dark:text-text-secondary">{{ $booking->device_model }}</td>
                            <td class="p-4 text-gray-600 dark:text-text-secondary">{{ $booking->preferred_date->format('M d, Y') }}</td>
                            <td class="p-4">
                                <span class="text-xs px-3 py-1 rounded-full
                                    @if($booking->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($booking->status === 'approved') bg-blue-500/20 text-blue-500
                                    @elseif($booking->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-orange hover:text-orange-light">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-600 dark:text-text-secondary">
                                No bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>
</x-app-layout>
