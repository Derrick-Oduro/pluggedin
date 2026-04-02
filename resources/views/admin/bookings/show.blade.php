<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="backend-sidebar sticky top-24">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="backend-tab">Dashboard</a>
                        <a href="{{ route('admin.products.pending') }}" class="backend-tab">Moderation Queue</a>
                        <a href="{{ route('admin.reviews.index') }}" class="backend-tab">Review Moderation</a>
                        <a href="{{ route('admin.products.index') }}" class="backend-tab">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="backend-tab">Orders</a>
                        <a href="{{ route('admin.bookings.index') }}" class="backend-tab backend-tab-active">Bookings</a>
                        <a href="{{ route('admin.referrals.index') }}" class="backend-tab">Referrals</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
        <x-page-header
            title="Booking #{{ $booking->id }}"
            subtitle="Review customer details and update booking status."
            :breadcrumbs="[
                ['label' => 'Admin Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Bookings', 'href' => route('admin.bookings.index')],
                ['label' => 'Booking #'.$booking->id],
            ]"
        >
            <x-slot name="actions">
                <a href="{{ route('admin.bookings.index') }}" class="backend-btn-muted">Back to Bookings</a>
            </x-slot>
        </x-page-header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Info -->
            <div class="backend-card rounded-xl">
                <h2 class="text-lg font-semibold mb-3">Customer Information</h2>
                <div class="space-y-2 text-gray-600 dark:text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Name:</span> {{ $booking->user->name }}</p>
                    <p><span class="font-semibold text-text-primary">Email:</span> {{ $booking->user->email }}</p>
                </div>
            </div>

            <!-- Service Info -->
            <div class="backend-card rounded-xl">
                <h2 class="text-lg font-semibold mb-3">Service Details</h2>
                <div class="space-y-2 text-gray-600 dark:text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Service:</span> {{ $booking->service->name }}</p>
                    <p><span class="font-semibold text-text-primary">Price:</span> <span class="text-orange font-bold">${{ number_format($booking->service->price, 2) }}</span></p>
                    <p><span class="font-semibold text-text-primary">Device Model:</span> {{ $booking->device_model }}</p>
                    <p><span class="font-semibold text-text-primary">Preferred Date:</span> {{ $booking->preferred_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="backend-card rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Booking Tracking</h2>
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

        <!-- Notes -->
        @if($booking->notes)
            <div class="backend-card rounded-xl">
                <h2 class="text-lg font-semibold mb-3">Customer Notes</h2>
                <p class="text-gray-600 dark:text-text-secondary">{{ $booking->notes }}</p>
            </div>
        @endif

        <!-- Update Status -->
        <div class="backend-card rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Update Booking Status</h2>
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
            </div>
        </div>
    </div>
</x-app-layout>
