<x-admin-layout>
                <x-page-header badge="Admin" title="Reviews" subtitle="Moderate customer reviews and handle reports." />


                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="backend-card p-3"><p class="text-xs text-stone-500">Pending</p><p class="text-lg font-semibold">{{ $stats['pending'] }}</p></div>
                    <div class="backend-card p-3"><p class="text-xs text-stone-500">Approved</p><p class="text-lg font-semibold">{{ $stats['approved'] }}</p></div>
                    <div class="backend-card p-3"><p class="text-xs text-stone-500">Hidden</p><p class="text-lg font-semibold">{{ $stats['hidden'] }}</p></div>
                    <div class="backend-card p-3"><p class="text-xs text-stone-500">Reported</p><p class="text-lg font-semibold">{{ $stats['reported'] }}</p></div>
                </div>

                <x-filter-bar :action="route('admin.reviews.index')" title="Filter Reviews" formClass="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <select name="status" class="backend-field h-10 text-sm">
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                    <label class="inline-flex items-center gap-2 text-sm h-10 px-3 rounded border border-stone-300 bg-white">
                        <input type="checkbox" name="reported_only" value="1" {{ request()->boolean('reported_only') ? 'checked' : '' }}>
                        Reported only
                    </label>
                    <div class="flex gap-2">
                        <button class="btn-primary h-10 px-4 text-sm">Filter</button>
                        <a href="{{ route('admin.reviews.index') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Reset</a>
                    </div>
                </x-filter-bar>

                <div class="space-y-3">
                    @forelse($reviews as $review)
                        <div class="backend-card p-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="space-y-2">
                                    <p class="font-semibold">{{ $review->product?->name ?? 'Product removed' }} · {{ $review->rating }}/5</p>
                                    <p class="text-xs text-stone-600">By {{ $review->user?->name ?? 'User removed' }} · Order #{{ $review->order_id }}</p>
                                    @if($review->comment)
                                        <p class="text-sm text-stone-700">{{ $review->comment }}</p>
                                    @endif
                                    @if($review->is_reported)
                                        <div class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-500 inline-block">Reported: {{ $review->report_reason ?: 'No reason provided' }}</div>
                                    @endif
                                    <div class="text-xs text-stone-500">Current status: {{ ucfirst($review->moderation_status) }}</div>
                                </div>

                                <form action="{{ route('admin.reviews.moderate', $review) }}" method="POST" class="space-y-2 w-full md:w-72">
                                    @csrf
                                    @method('PATCH')
                                    <select name="moderation_status" class="backend-field" required>
                                        <option value="approved" {{ $review->moderation_status === 'approved' ? 'selected' : '' }}>Approve</option>
                                        <option value="hidden" {{ $review->moderation_status === 'hidden' ? 'selected' : '' }}>Hide</option>
                                    </select>
                                    <textarea name="moderation_note" rows="2" placeholder="Optional moderation note" class="backend-field">{{ $review->moderation_note }}</textarea>
                                    <button class="w-full bg-stone-900 hover:bg-black text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Save Moderation</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="backend-card p-6 text-center text-sm text-stone-600">No reviews found for this filter.</div>
                    @endforelse
                </div>

                <div>{{ $reviews->links() }}</div>
</x-admin-layout>
