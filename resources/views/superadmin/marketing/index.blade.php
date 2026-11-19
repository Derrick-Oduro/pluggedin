<x-superadmin-layout>
    @php($liveSlide = $slides->firstWhere('is_active'))

    <div class="mx-auto max-w-6xl space-y-8 py-4">
        <header class="flex flex-col justify-between gap-5 border-b border-stone-200 pb-7 sm:flex-row sm:items-end">
            <div>
                <span class="brand-badge">Content studio</span>
                <h1 class="display-heading mt-3 text-3xl text-stone-950">Homepage & promotions</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-stone-600">Choose the image visitors see first and manage time-sensitive offers from one calm, focused workspace.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="btn-secondary shrink-0">Preview homepage ↗</a>
        </header>

        <section>
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div><h2 class="text-xl font-extrabold tracking-[-0.03em]">Homepage hero</h2><p class="mt-1 text-sm text-stone-600">One image is live at a time. Pick it from your image library below.</p></div>
                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-bold text-stone-600">{{ $slides->count() }} image{{ $slides->count() === 1 ? '' : 's' }} in library</span>
            </div>

            <div class="grid gap-5 lg:grid-cols-5">
                <article class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_16px_35px_-28px_rgba(41,30,23,0.34)] lg:col-span-3">
                    <div class="relative h-64 bg-brand-tint sm:h-80">
                        @if($liveSlide)
                            <img src="{{ $liveSlide->image_url }}" alt="{{ $liveSlide->alt_text ?: $liveSlide->title ?: 'Live homepage hero image' }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-stone-950/55 via-transparent to-transparent"></div>
                            <div class="absolute bottom-5 left-5 right-5 text-white"><p class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Currently live</p><p class="mt-1 text-xl font-extrabold">{{ $liveSlide->title ?: 'Homepage hero image' }}</p></div>
                        @else
                            <div class="flex h-full flex-col items-center justify-center p-8 text-center"><div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-soft text-xl font-extrabold text-brand-deep">+</div><p class="mt-4 font-extrabold text-stone-900">No hero image selected</p><p class="mt-1 max-w-xs text-sm leading-6 text-stone-600">Add an image below, then make it live when you are ready.</p></div>
                        @endif
                    </div>
                </article>

                <div class="rounded-2xl border border-brand-soft bg-brand-tint p-6 lg:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-[0.15em] text-brand-deep">Change homepage image</p>
                    <p class="mt-2 text-sm leading-6 text-stone-700">Choose the image you want visitors to see. Saving this choice automatically turns off every other image.</p>
                    @if($slides->isNotEmpty())
                        <form action="{{ route('superadmin.marketing.slides.select-live') }}" method="POST" class="mt-5 space-y-3">
                            @csrf
                            <label for="homepage-image" class="text-sm font-bold text-stone-900">Homepage image</label>
                            <select id="homepage-image" name="slide_id" class="backend-field">
                                @foreach($slides as $slide)
                                    <option value="{{ $slide->id }}" @selected($slide->is_active)>{{ $slide->title ?: 'Untitled image #'.$slide->id }}</option>
                                @endforeach
                            </select>
                            <button class="backend-btn-primary w-full">Set as homepage image</button>
                        </form>
                    @else
                        <p class="mt-5 rounded-xl border border-brand-soft bg-white/70 p-3 text-sm text-stone-600">Add an image to your library, then you can select it here.</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-[0_16px_35px_-28px_rgba(41,30,23,0.25)] sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div><h2 class="text-xl font-extrabold tracking-[-0.03em]">Hero image library</h2><p class="mt-1 text-sm text-stone-600">Select one image to put on the homepage. Edit details only when you need them.</p></div>
                <details class="group relative">
                    <summary class="btn-primary cursor-pointer list-none">Add image <span class="ml-1 text-lg leading-none">+</span></summary>
                    <div class="absolute right-0 z-20 mt-3 w-[min(26rem,calc(100vw-3rem))] rounded-2xl border border-stone-200 bg-white p-5 shadow-2xl">
                        <h3 class="text-base font-extrabold">Add a new hero image</h3><p class="mt-1 text-xs leading-5 text-stone-500">Upload a JPG, PNG, or WebP (up to 5 MB), or paste a hosted image URL.</p>
                        <form action="{{ route('superadmin.marketing.slides.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            <input name="title" placeholder="Short title (optional)" class="backend-field">
                            <textarea name="caption" rows="2" placeholder="Short supporting text (optional)" class="backend-field"></textarea>
                            <input type="file" name="image_file" accept="image/*" class="backend-field">
                            <p class="text-center text-xs font-semibold text-stone-400">or</p>
                            <input name="image_url" placeholder="https://example.com/image.jpg" class="backend-field">
                            <input name="alt_text" placeholder="Image description for accessibility" class="backend-field">
                            <input type="hidden" name="sort_order" value="999">
                            <input type="hidden" name="is_active" value="0">
                            <button class="backend-btn-primary w-full">Add to library</button>
                        </form>
                    </div>
                </details>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($slides as $slide)
                    <article class="overflow-hidden rounded-2xl border {{ $slide->is_active ? 'border-brand ring-2 ring-brand-soft' : 'border-stone-200' }} bg-white">
                        <img src="{{ $slide->image_url }}" alt="{{ $slide->alt_text ?: $slide->title ?: 'Hero image preview' }}" class="h-44 w-full object-cover">
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="truncate font-extrabold text-stone-950">{{ $slide->title ?: 'Untitled image' }}</h3><p class="mt-1 line-clamp-2 text-xs leading-5 text-stone-500">{{ $slide->caption ?: 'No supporting text added.' }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $slide->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">{{ $slide->is_active ? 'LIVE' : 'SAVED' }}</span></div>

                            @if($slide->is_active)
                                <div class="mt-4 rounded-xl bg-emerald-50 px-3 py-2.5 text-center text-sm font-bold text-emerald-800">This image is on the homepage.</div>
                            @else
                                <p class="mt-4 rounded-xl bg-stone-50 px-3 py-2.5 text-center text-sm font-semibold text-stone-500">Select this from the Homepage Image picker above.</p>
                            @endif

                            <details class="mt-3 border-t border-stone-100 pt-3">
                                <summary class="cursor-pointer text-sm font-bold text-stone-600 hover:text-brand-deep">Edit image details</summary>
                                <form action="{{ route('superadmin.marketing.slides.update', $slide) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                                    @csrf @method('PATCH')
                                    <input name="title" value="{{ $slide->title }}" placeholder="Short title" class="backend-field">
                                    <textarea name="caption" rows="2" placeholder="Supporting text" class="backend-field">{{ $slide->caption }}</textarea>
                                    <input name="image_url" value="{{ $slide->image_url }}" placeholder="Image URL" class="backend-field">
                                    <input type="file" name="image_file" accept="image/*" class="backend-field">
                                    <input name="alt_text" value="{{ $slide->alt_text }}" placeholder="Image description" class="backend-field">
                                    <input type="hidden" name="sort_order" value="{{ $slide->sort_order }}"><input type="hidden" name="is_active" value="{{ $slide->is_active ? 1 : 0 }}">
                                    <button class="backend-btn-muted w-full">Save changes</button>
                                </form>
                                <form action="{{ route('superadmin.marketing.slides.destroy', $slide) }}" method="POST" class="mt-3" onsubmit="return confirm('Remove this image from your library?')">@csrf @method('DELETE')<button class="w-full rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50">Remove image</button></form>
                            </details>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-10 text-center"><p class="font-extrabold text-stone-900">Your image library is empty.</p><p class="mt-1 text-sm text-stone-600">Use “Add image” above to get started.</p></div>
                @endforelse
            </div>
        </section>

        <section class="border-t border-stone-200 pt-8">
            <div class="flex flex-wrap items-end justify-between gap-4"><div><span class="brand-badge">Promotions</span><h2 class="mt-3 text-xl font-extrabold tracking-[-0.03em]">Discount campaigns</h2><p class="mt-1 text-sm text-stone-600">Create and maintain offers without mixing them into homepage content.</p></div><details class="group relative"><summary class="btn-secondary cursor-pointer list-none">Create campaign +</summary><div class="absolute right-0 z-20 mt-3 w-[min(28rem,calc(100vw-3rem))] rounded-2xl border border-stone-200 bg-white p-5 shadow-2xl"><form action="{{ route('superadmin.marketing.campaigns.store') }}" method="POST" class="space-y-3">@csrf <input name="name" required placeholder="Campaign name" class="backend-field"><div class="grid grid-cols-2 gap-3"><input name="code" placeholder="Code (optional)" class="backend-field"><input type="number" name="discount_percent" min="1" max="95" required placeholder="Discount %" class="backend-field"></div><textarea name="description" rows="2" placeholder="Description (optional)" class="backend-field"></textarea><div class="grid grid-cols-2 gap-3"><input type="date" name="starts_date" class="backend-field"><input type="date" name="ends_date" class="backend-field"></div><input type="hidden" name="is_active" value="1"><button class="backend-btn-primary w-full">Create campaign</button></form></div></details></div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                @forelse($campaigns as $campaign)
                    @php($isLive = $campaign->is_active && (! $campaign->starts_at || $campaign->starts_at->lte(now())) && (! $campaign->ends_at || $campaign->ends_at->gte(now())))
                    <details class="rounded-2xl border border-stone-200 bg-white p-5 shadow-[0_12px_30px_-26px_rgba(41,30,23,0.25)]"><summary class="flex cursor-pointer list-none items-start justify-between gap-4"><div><p class="font-extrabold text-stone-950">{{ $campaign->name }}</p><p class="mt-1 text-sm text-stone-600">{{ $campaign->discount_percent }}% off{{ $campaign->code ? ' · '.$campaign->code : '' }}</p></div><span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $isLive ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">{{ $isLive ? 'LIVE' : 'NOT LIVE' }}</span></summary><form action="{{ route('superadmin.marketing.campaigns.update', $campaign) }}" method="POST" class="mt-5 space-y-3 border-t border-stone-100 pt-4">@csrf @method('PATCH')<input name="name" value="{{ $campaign->name }}" required class="backend-field"><div class="grid grid-cols-2 gap-3"><input name="code" value="{{ $campaign->code }}" placeholder="Code" class="backend-field"><input type="number" name="discount_percent" min="1" max="95" value="{{ $campaign->discount_percent }}" required class="backend-field"></div><textarea name="description" rows="2" class="backend-field">{{ $campaign->description }}</textarea><input type="hidden" name="is_active" value="{{ $campaign->is_active ? 1 : 0 }}"><button class="backend-btn-muted w-full">Save campaign</button></form></details>
                @empty
                    <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center text-sm text-stone-600 md:col-span-2">No campaigns yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-superadmin-layout>
