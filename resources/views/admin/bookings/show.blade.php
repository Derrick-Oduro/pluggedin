<x-admin-layout>
        <x-page-header
            badge="Admin"
            title="Booking #{{ $booking->id }}"
            subtitle="Review customer details and update booking status."
            :breadcrumbs="[
                ['label' => 'Admin Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Bookings', 'href' => route('admin.bookings.index')],
                ['label' => 'Booking #'.$booking->id],
            ]"
        >
            <x-slot name="actions">
                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Back to bookings</a>
            </x-slot>
        </x-page-header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Info -->
            <div class="backend-card rounded-xl">
                <h2 class="text-lg font-extrabold text-stone-950 mb-3">Customer Information</h2>
                <div class="space-y-2 text-stone-600">
                    <p><span class="font-semibold text-stone-950">Name:</span> {{ $booking->user->name }}</p>
                    <p><span class="font-semibold text-stone-950">Email:</span> {{ $booking->user->email }}</p>
                </div>
            </div>

            <!-- Service Info -->
            <div class="backend-card rounded-xl">
                <h2 class="text-lg font-extrabold text-stone-950 mb-3">Service Details</h2>
                <div class="space-y-2 text-stone-600">
                    <p><span class="font-semibold text-stone-950">Service:</span> {{ $booking->service->name }}</p>
                    <p><span class="font-semibold text-stone-950">Price:</span> <span class="text-brand font-bold">GH₵{{ number_format($booking->service->price, 2) }}</span></p>
                    <p><span class="font-semibold text-stone-950">Device Model:</span> {{ $booking->device_model }}</p>
                    <p><span class="font-semibold text-stone-950">Preferred Date:</span> {{ $booking->preferred_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="backend-card rounded-xl">
            <h2 class="text-lg font-extrabold text-stone-950 mb-3">Booking Tracking</h2>
            <div class="space-y-2">
                @forelse($booking->statusHistories as $history)
                    <div class="rounded-lg border border-stone-200 px-3 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-semibold">
                                {{ ucfirst($history->to_status) }}
                                @if($history->from_status)
                                    <span class="text-stone-500 font-normal">from {{ ucfirst($history->from_status) }}</span>
                                @endif
                            </p>
                            <span class="text-xs text-stone-500">{{ $history->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <p class="text-xs text-stone-600 mt-1">Updated by {{ $history->actor?->name ?? 'System' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-stone-600">No tracking updates yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Notes -->
        @if($booking->notes)
            <div class="backend-card rounded-xl">
                <h2 class="text-lg font-extrabold text-stone-950 mb-3">Customer Notes</h2>
                <p class="text-stone-600">{{ $booking->notes }}</p>
            </div>
        @endif

        <!-- Update Status -->
        <div class="backend-card rounded-xl">
            <h2 class="text-lg font-extrabold text-stone-950 mb-3">Update Booking Status</h2>
            <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-3">
                @csrf
                @method('PATCH')

                <div class="flex-1">
                    <label class="block text-sm font-medium mb-1.5">Status</label>
                    <select name="status" class="backend-field">
                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <button type="submit" class="backend-btn-primary">
                    Update Status
                </button>
            </form>
        </div>
</x-admin-layout>
