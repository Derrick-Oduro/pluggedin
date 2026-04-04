<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="backend-sidebar sticky top-24">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="backend-tab">Dashboard</a>
                        <a href="{{ route('admin.products.pending') }}" class="backend-tab">Moderation Queue</a>
                        <a href="{{ route('admin.reviews.index') }}" class="backend-tab backend-tab-active">Reviews</a>
                        <a href="{{ route('admin.products.index') }}" class="backend-tab">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="backend-tab">Orders</a>
                        <a href="{{ route('admin.bookings.index') }}" class="backend-tab">Bookings</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Review Moderation</h1>
                </div>

                @if(session('success'))
                    <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-3 text-sm">{{ session('success') }}</div>
                @endif

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="backend-card p-3"><p class="text-xs text-gray-500 dark:text-text-secondary">Pending</p><p class="text-lg font-semibold">{{ $stats['pending'] }}</p></div>
                    <div class="backend-card p-3"><p class="text-xs text-gray-500 dark:text-text-secondary">Approved</p><p class="text-lg font-semibold">{{ $stats['approved'] }}</p></div>
                    <div class="backend-card p-3"><p class="text-xs text-gray-500 dark:text-text-secondary">Hidden</p><p class="text-lg font-semibold">{{ $stats['hidden'] }}</p></div>
                    <div class="backend-card p-3"><p class="text-xs text-gray-500 dark:text-text-secondary">Reported</p><p class="text-lg font-semibold">{{ $stats['reported'] }}</p></div>
                </div>

                <x-filter-bar :action="route('admin.reviews.index')" title="Filter Reviews" formClass="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <select name="status" class="backend-field h-10 text-sm">
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                    <label class="inline-flex items-center gap-2 text-sm h-10 px-3 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark">
                        <input type="checkbox" name="reported_only" value="1" {{ request()->boolean('reported_only') ? 'checked' : '' }}>
                        Reported only
                    </label>
                    <div class="flex gap-2">
                        <button class="h-10 bg-orange hover:bg-orange-light text-white px-4 rounded-lg text-sm font-semibold transition">Filter</button>
                        <a href="{{ route('admin.reviews.index') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Reset</a>
                    </div>
                </x-filter-bar>

                <div class="space-y-3">
                    @forelse($reviews as $review)
                        <div class="backend-card p-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="space-y-2">
                                    <p class="font-semibold">{{ $review->product?->name ?? 'Product removed' }} · {{ $review->rating }}/5</p>
                                    <p class="text-xs text-gray-600 dark:text-text-secondary">By {{ $review->user?->name ?? 'User removed' }} · Order #{{ $review->order_id }}</p>
                                    @if($review->comment)
                                        <p class="text-sm text-gray-700 dark:text-text-secondary">{{ $review->comment }}</p>
                                    @endif
                                    @if($review->is_reported)
                                        <div class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-500 inline-block">Reported: {{ $review->report_reason ?: 'No reason provided' }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500 dark:text-text-secondary">Current status: {{ ucfirst($review->moderation_status) }}</div>
                                </div>

                                <form action="{{ route('admin.reviews.moderate', $review) }}" method="POST" class="space-y-2 w-full md:w-72">
                                    @csrf
                                    @method('PATCH')
                                    <select name="moderation_status" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-3 py-2 text-sm" required>
                                        <option value="approved" {{ $review->moderation_status === 'approved' ? 'selected' : '' }}>Approve</option>
                                        <option value="hidden" {{ $review->moderation_status === 'hidden' ? 'selected' : '' }}>Hide</option>
                                    </select>
                                    <textarea name="moderation_note" rows="2" placeholder="Optional moderation note" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-3 py-2 text-sm">{{ $review->moderation_note }}</textarea>
                                    <button class="w-full bg-gray-900 dark:bg-gray-700 hover:bg-black dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Save Moderation</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="backend-card p-6 text-center text-sm text-gray-600 dark:text-text-secondary">No reviews found for this filter.</div>
                    @endforelse
                </div>

                <div>{{ $reviews->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
