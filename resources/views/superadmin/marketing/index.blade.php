@section('page-title', 'Marketing Control')

<x-superadmin-layout>
    <div class="space-y-4">
            <x-page-header
                title="Homepage and Promotions"
                subtitle="Cleaner workspace for hero slides and discount campaigns. Create on top, manage below."
                :breadcrumbs="[
                    ['label' => 'Dashboard', 'href' => route('superadmin.dashboard')],
                    ['label' => 'Marketing'],
                ]"
            />

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div id="slides-create" class="backend-card p-4 rounded-xl">
                    <h2 class="text-lg font-semibold">Create Carousel Slide</h2>
                    <p class="text-xs text-gray-500 dark:text-text-secondary mt-1 mb-3">Use short captions and keep sort order low for priority.</p>
                    <form action="{{ route('superadmin.marketing.slides.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input name="title" placeholder="Slide title (optional)" class="backend-field">
                        <textarea name="caption" rows="2" placeholder="Caption" class="backend-field"></textarea>
                        <input name="image_url" required placeholder="Image URL" class="backend-field">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input name="alt_text" placeholder="Alt text" class="backend-field">
                            <input type="number" name="sort_order" min="0" value="0" placeholder="Sort order" class="backend-field">
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" name="is_active" value="1" checked>
                                Active
                            </label>
                            <button class="backend-btn-primary">Add Slide</button>
                        </div>
                    </form>
                </div>

                <div id="campaigns-create" class="backend-card p-4 rounded-xl">
                    <h2 class="text-lg font-semibold">Create Discount Campaign</h2>
                    <p class="text-xs text-gray-500 dark:text-text-secondary mt-1 mb-3">Set schedule and usage limit to avoid conflicts.</p>
                    <form action="{{ route('superadmin.marketing.campaigns.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input name="name" required placeholder="Campaign name" class="backend-field">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input name="code" placeholder="Code (optional)" class="backend-field">
                            <input type="number" name="discount_percent" min="1" max="95" required placeholder="Discount %" class="backend-field">
                        </div>
                        <input type="number" name="max_uses" min="1" placeholder="Maximum uses (optional)" class="backend-field">
                        <textarea name="description" rows="2" placeholder="Description" class="backend-field"></textarea>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-2">
                                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-text-secondary">Start</label>
                                <input type="date" name="starts_date" class="backend-field">
                                <input type="time" name="starts_time" step="60" class="backend-field">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-text-secondary">End</label>
                                <input type="date" name="ends_date" class="backend-field">
                                <input type="time" name="ends_time" step="60" class="backend-field">
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" name="is_active" value="1" checked>
                                Active
                            </label>
                            <button class="backend-btn-primary">Add Campaign</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="slides-manage" class="backend-card p-4 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold">Manage Slides</h2>
                    <span class="text-xs rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1">{{ $slides->count() }} total</span>
                </div>
                <div class="space-y-3">
                    @forelse($slides as $slide)
                        <details class="rounded-lg border border-gray-200 p-3 dark:border-gray-700 group">
                            <summary class="list-none cursor-pointer">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ $slide->title ?: 'Untitled Slide' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-text-secondary">Sort: {{ $slide->sort_order }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $slide->is_active ? 'bg-green-500/20 text-green-500' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-text-secondary' }}">{{ $slide->is_active ? 'Active' : 'Inactive' }}</span>
                                        <span class="text-xs text-gray-500 dark:text-text-secondary">Expand</span>
                                    </div>
                                </div>
                            </summary>

                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-3">
                                <form action="{{ route('superadmin.marketing.slides.update', $slide) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <input name="title" value="{{ $slide->title }}" placeholder="Title" class="backend-field">
                                    <textarea name="caption" rows="2" class="backend-field">{{ $slide->caption }}</textarea>
                                    <input name="image_url" value="{{ $slide->image_url }}" class="backend-field">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input name="alt_text" value="{{ $slide->alt_text }}" placeholder="Alt text" class="backend-field">
                                        <input type="number" name="sort_order" min="0" value="{{ $slide->sort_order }}" class="backend-field">
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }}>
                                            Active
                                        </label>
                                        <button class="backend-btn-muted">Save Changes</button>
                                    </div>
                                </form>
                                <form action="{{ route('superadmin.marketing.slides.destroy', $slide) }}" method="POST" onsubmit="return confirm('Delete this slide?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Delete Slide</button>
                                </form>
                            </div>
                        </details>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-600 dark:border-gray-700 dark:text-text-secondary">
                            No slides yet. The homepage hero can run without carousel images, so you can remove all existing slides and add new ones anytime.
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="campaigns-manage" class="backend-card p-4 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold">Manage Campaigns</h2>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('superadmin.marketing.index') }}#campaigns-manage" class="text-xs px-2 py-1 rounded-full border border-gray-200 dark:border-gray-700 {{ ($scope ?? '') === '' ? 'bg-orange/15 text-orange border-orange/30' : 'text-gray-600 dark:text-text-secondary' }}">All</a>
                        <a href="{{ route('superadmin.marketing.index', ['scope' => 'live']) }}#campaigns-manage" class="text-xs px-2 py-1 rounded-full border border-gray-200 dark:border-gray-700 {{ ($scope ?? '') === 'live' ? 'bg-orange/15 text-orange border-orange/30' : 'text-gray-600 dark:text-text-secondary' }}">Live only</a>
                        <span class="text-xs rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1">{{ $campaigns->count() }} shown</span>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($campaigns as $campaign)
                        @php
                            $now = now();
                            $isScheduled = $campaign->starts_at && $campaign->starts_at->gt($now);
                            $isExpired = $campaign->ends_at && $campaign->ends_at->lt($now);
                            $isLive = $campaign->is_active && ! $isScheduled && ! $isExpired;
                        @endphp

                        <details class="rounded-lg border border-gray-200 p-3 dark:border-gray-700 group">
                            <summary class="list-none cursor-pointer">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ $campaign->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-text-secondary">{{ $campaign->discount_percent }}% off{{ $campaign->code ? ' • '.$campaign->code : '' }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="px-2 py-1 rounded-full {{ $isLive ? 'bg-green-500/20 text-green-500' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-text-secondary' }}">{{ $isLive ? 'Live' : 'Not live' }}</span>
                                        @if($isScheduled)
                                            <span class="px-2 py-1 rounded-full bg-blue-500/20 text-blue-500">Scheduled</span>
                                        @endif
                                        @if($isExpired)
                                            <span class="px-2 py-1 rounded-full bg-red-500/20 text-red-500">Expired</span>
                                        @endif
                                        <span class="px-2 py-1 rounded-full bg-orange/20 text-orange">Used {{ $campaign->used_count }}{{ $campaign->max_uses ? '/'.$campaign->max_uses : '' }}</span>
                                        <span class="text-gray-500 dark:text-text-secondary">Expand</span>
                                    </div>
                                </div>
                            </summary>

                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-3">
                                <form action="{{ route('superadmin.marketing.campaigns.update', $campaign) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <input name="name" value="{{ $campaign->name }}" required class="backend-field">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input name="code" value="{{ $campaign->code }}" placeholder="Code" class="backend-field">
                                        <input type="number" name="discount_percent" min="1" max="95" value="{{ $campaign->discount_percent }}" required class="backend-field">
                                    </div>
                                    <input type="number" name="max_uses" min="1" value="{{ $campaign->max_uses }}" placeholder="Maximum uses" class="backend-field">
                                    <textarea name="description" rows="2" class="backend-field">{{ $campaign->description }}</textarea>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="space-y-2">
                                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-text-secondary">Start</label>
                                            <input type="date" name="starts_date" value="{{ optional($campaign->starts_at)->format('Y-m-d') }}" class="backend-field">
                                            <input type="time" name="starts_time" value="{{ optional($campaign->starts_at)->format('H:i') }}" step="60" class="backend-field">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-text-secondary">End</label>
                                            <input type="date" name="ends_date" value="{{ optional($campaign->ends_at)->format('Y-m-d') }}" class="backend-field">
                                            <input type="time" name="ends_time" value="{{ optional($campaign->ends_at)->format('H:i') }}" step="60" class="backend-field">
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="is_active" value="1" {{ $campaign->is_active ? 'checked' : '' }}>
                                            Active
                                        </label>
                                        <button class="backend-btn-muted">Save Changes</button>
                                    </div>
                                </form>
                                <form action="{{ route('superadmin.marketing.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Delete Campaign</button>
                                </form>
                            </div>
                        </details>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-600 dark:border-gray-700 dark:text-text-secondary">
                            No campaigns yet. Create one above to start discount scheduling.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="backend-card p-4 rounded-xl">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-semibold">Archived Slides</h2>
                        <span class="text-xs rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1">{{ ($archivedSlides ?? collect())->count() }} archived</span>
                    </div>

                    <div class="space-y-2">
                        @forelse(($archivedSlides ?? collect()) as $slide)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                                <p class="text-sm font-semibold">{{ $slide->title ?: 'Untitled Slide' }}</p>
                                <p class="text-xs text-gray-500 dark:text-text-secondary">Deleted {{ $slide->deleted_at?->format('M d, Y H:i') }}</p>
                                <form action="{{ route('superadmin.marketing.slides.restore', $slide->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button class="text-xs font-semibold text-orange hover:text-orange-light">Restore Slide</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600 dark:text-text-secondary">No archived slides.</p>
                        @endforelse
                    </div>
                </div>

                <div class="backend-card p-4 rounded-xl">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-semibold">Archived Campaigns</h2>
                        <span class="text-xs rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1">{{ ($archivedCampaigns ?? collect())->count() }} archived</span>
                    </div>

                    <div class="space-y-2">
                        @forelse(($archivedCampaigns ?? collect()) as $campaign)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                                <p class="text-sm font-semibold">{{ $campaign->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-text-secondary">Deleted {{ $campaign->deleted_at?->format('M d, Y H:i') }}</p>
                                <form action="{{ route('superadmin.marketing.campaigns.restore', $campaign->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button class="text-xs font-semibold text-orange hover:text-orange-light">Restore Campaign</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600 dark:text-text-secondary">No archived campaigns.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
</x-superadmin-layout>
