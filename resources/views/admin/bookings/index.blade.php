<x-admin-layout>
                <x-page-header badge="Admin" title="Bookings" subtitle="Repair and upgrade requests from customers." />

                @if($bookings->count())
                    <x-table-shell>
                        <table class="backend-table">
                            <thead class="border-b border-stone-200">
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
                            <td class="backend-td text-stone-600">{{ $booking->device_model }}</td>
                            <td class="backend-td text-stone-600">{{ $booking->preferred_date->format('M d, Y') }}</td>
                            <td class="backend-td">
                                <x-status-badge :status="$booking->status" />
                            </td>
                            <td class="backend-td">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-brand-deep hover:text-brand-deep">
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
</x-admin-layout>
