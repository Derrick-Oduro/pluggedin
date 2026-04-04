<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="backend-sidebar sticky top-24">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="backend-tab">Dashboard</a>
                        <a href="{{ route('admin.products.pending') }}" class="backend-tab">Moderation Queue</a>
                        <a href="{{ route('admin.products.index') }}" class="backend-tab">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="backend-tab">Orders</a>
                        <a href="{{ route('admin.reviews.index') }}" class="backend-tab">Review Moderation</a>
                        <a href="{{ route('admin.bookings.index') }}" class="backend-tab backend-tab-active">Bookings</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
                <h1 class="text-2xl font-bold">Manage Bookings</h1>

                @if($bookings->count())
                    <x-table-shell>
                        <table class="backend-table">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="backend-th">ID</th>
                                    <th class="backend-th">Customer</th>
                                    <th class="backend-th">Service</th>
                                    <th class="backend-th">Device</th>
                                    <th class="backend-th">Preferred Date</th>
                                    <th class="backend-th">Status</th>
                                    <th class="backend-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                        <tr class="backend-row">
                            <td class="backend-td font-semibold">#{{ $booking->id }}</td>
                            <td class="backend-td">{{ $booking->user->name }}</td>
                            <td class="backend-td">{{ $booking->service->name }}</td>
                            <td class="backend-td text-gray-600 dark:text-text-secondary">{{ $booking->device_model }}</td>
                            <td class="backend-td text-gray-600 dark:text-text-secondary">{{ $booking->preferred_date->format('M d, Y') }}</td>
                            <td class="backend-td">
                                <span class="text-xs px-3 py-1 rounded-full
                                    @if($booking->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($booking->status === 'approved') bg-blue-500/20 text-blue-500
                                    @elseif($booking->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="backend-td">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-orange hover:text-orange-light">
                                    View Details
                                </a>
                            </td>
                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-table-shell>
                @else
                    <x-empty-state
                        title="No bookings found"
                        message="Service booking requests will appear here after customers submit them."
                    />
                @endif

                <div class="mt-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
