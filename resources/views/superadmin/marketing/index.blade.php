@section('page-title', 'Marketing Control')

<x-superadmin-layout>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <aside class="xl:col-span-3">
            <div class="backend-sidebar sticky top-24">
                <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Marketing Tabs</p>
                <nav class="space-y-1">
                    <a href="#slides-create" class="backend-tab">Create Slide</a>
                    <a href="#slides-manage" class="backend-tab">Manage Slides</a>
                    <a href="#campaigns-create" class="backend-tab">Create Campaign</a>
                    <a href="#campaigns-manage" class="backend-tab">Manage Campaigns</a>
                </nav>
            </div>
        </aside>

        <div class="xl:col-span-9 space-y-5">
            <div>
                <h1 class="text-2xl font-bold">Homepage and Promotions</h1>
                <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Compact controls for carousel content and discount timelines.</p>
            </div>

            <div id="slides-create" class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Create Carousel Slide</h2>
                <form action="{{ route('superadmin.marketing.slides.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input name="title" placeholder="Slide title (optional)" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <textarea name="caption" rows="2" placeholder="Caption" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2"></textarea>
                    <input name="image_url" required placeholder="Image URL" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <div class="grid grid-cols-2 gap-3">
                        <input name="alt_text" placeholder="Alt text" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                        <input type="number" name="sort_order" min="0" value="0" placeholder="Sort order" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active
                    </label>
                    <button class="bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Add Slide</button>
                </form>
            </div>

            <div id="slides-manage" class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Manage Slides</h2>
                <div class="space-y-3">
                    @foreach($slides as $slide)
                        <div class="rounded-lg border border-gray-200 p-3 space-y-2 dark:border-gray-700">
                            <form action="{{ route('superadmin.marketing.slides.update', $slide) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <input name="title" value="{{ $slide->title }}" placeholder="Title" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                <textarea name="caption" rows="2" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">{{ $slide->caption }}</textarea>
                                <input name="image_url" value="{{ $slide->image_url }}" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                <div class="grid grid-cols-2 gap-3">
                                    <input name="alt_text" value="{{ $slide->alt_text }}" placeholder="Alt text" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                    <input type="number" name="sort_order" min="0" value="{{ $slide->sort_order }}" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }}>
                                    Active
                                </label>
                                <button class="bg-gray-900 dark:bg-gray-700 hover:bg-black dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Save</button>
                            </form>
                            <form action="{{ route('superadmin.marketing.slides.destroy', $slide) }}" method="POST" onsubmit="return confirm('Delete this slide?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="campaigns-create" class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Create Discount Campaign</h2>
                <form action="{{ route('superadmin.marketing.campaigns.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input name="name" required placeholder="Campaign name" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <div class="grid grid-cols-2 gap-3">
                        <input name="code" placeholder="Code (optional)" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                        <input type="number" name="discount_percent" min="1" max="95" required placeholder="Discount %" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    </div>
                    <input type="number" name="max_uses" min="1" placeholder="Maximum uses (optional)" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                    <textarea name="description" rows="2" placeholder="Description" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2"></textarea>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="text-sm text-gray-600 dark:text-text-secondary">Start date</label>
                            <input type="date" name="starts_date" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                            <input type="time" name="starts_time" step="60" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm text-gray-600 dark:text-text-secondary">End date</label>
                            <input type="date" name="ends_date" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                            <input type="time" name="ends_time" step="60" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2">
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active
                    </label>
                    <button class="bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Add Campaign</button>
                </form>
            </div>

            <div id="campaigns-manage" class="backend-card p-5">
                <h2 class="text-lg font-semibold mb-3">Manage Campaigns</h2>
                <div class="space-y-3">
                    @foreach($campaigns as $campaign)
                        <div class="rounded-lg border border-gray-200 p-3 space-y-2 dark:border-gray-700">
                            <form action="{{ route('superadmin.marketing.campaigns.update', $campaign) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <input name="name" value="{{ $campaign->name }}" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                <div class="grid grid-cols-2 gap-3">
                                    <input name="code" value="{{ $campaign->code }}" placeholder="Code" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                    <input type="number" name="discount_percent" min="1" max="95" value="{{ $campaign->discount_percent }}" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <input type="number" name="max_uses" min="1" value="{{ $campaign->max_uses }}" placeholder="Maximum uses" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                <textarea name="description" rows="2" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">{{ $campaign->description }}</textarea>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <label class="text-sm text-gray-600 dark:text-text-secondary">Start date</label>
                                        <input type="date" name="starts_date" value="{{ optional($campaign->starts_at)->format('Y-m-d') }}" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                        <input type="time" name="starts_time" value="{{ optional($campaign->starts_at)->format('H:i') }}" step="60" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm text-gray-600 dark:text-text-secondary">End date</label>
                                        <input type="date" name="ends_date" value="{{ optional($campaign->ends_at)->format('Y-m-d') }}" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                        <input type="time" name="ends_time" value="{{ optional($campaign->ends_at)->format('H:i') }}" step="60" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $campaign->is_active ? 'checked' : '' }}>
                                    Active
                                </label>

                                <div class="flex flex-wrap gap-2 text-xs">
                                    @php
                                        $now = now();
                                        $isScheduled = $campaign->starts_at && $campaign->starts_at->gt($now);
                                        $isExpired = $campaign->ends_at && $campaign->ends_at->lt($now);
                                        $isLive = $campaign->is_active && ! $isScheduled && ! $isExpired;
                                    @endphp
                                    <span class="px-2 py-1 rounded-full {{ $isLive ? 'bg-green-500/20 text-green-500' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-text-secondary' }}">{{ $isLive ? 'Live' : 'Not live' }}</span>
                                    @if($isScheduled)
                                        <span class="px-2 py-1 rounded-full bg-blue-500/20 text-blue-500">Scheduled</span>
                                    @endif
                                    @if($isExpired)
                                        <span class="px-2 py-1 rounded-full bg-red-500/20 text-red-500">Expired</span>
                                    @endif
                                    <span class="px-2 py-1 rounded-full bg-orange/20 text-orange">Used {{ $campaign->used_count }}{{ $campaign->max_uses ? '/'.$campaign->max_uses : '' }}</span>
                                </div>
                                <button class="bg-gray-900 dark:bg-gray-700 hover:bg-black dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Save</button>
                            </form>
                            <form action="{{ route('superadmin.marketing.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-superadmin-layout>
