<x-superadmin-layout title="Homepage & discounts">
    @php
        $liveSlide = $slides->firstWhere('is_active');
        $tabs = ['live' => 'Live', 'scheduled' => 'Scheduled', 'paused' => 'Paused', 'finished' => 'Finished', 'archived' => 'Archived'];
        $when = function ($date) {
            if (! $date) return null;
            return $date->isToday() ? 'today '.$date->format('H:i') : ($date->isTomorrow() ? 'tomorrow '.$date->format('H:i') : $date->format('M j, H:i'));
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="brand-badge">Store setup</p>
                <h1 class="display-heading mt-3 text-3xl text-stone-950">Homepage &amp; discounts</h1>
                <p class="mt-1 text-sm text-stone-600">Run discounts and choose the picture visitors see first on the homepage.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-secondary">View homepage &#8599;</a>
        </div>

        {{-- Section switch --}}
        <nav class="flex gap-1 rounded-2xl border border-stone-200 bg-white p-1 sm:w-fit" aria-label="Sections">
            @foreach(['discounts' => 'Discounts', 'homepage' => 'Homepage image'] as $key => $label)
                <a href="{{ route('superadmin.marketing.index', ['section' => $key]) }}" class="flex-1 rounded-xl px-5 py-2 text-center text-sm font-extrabold transition sm:flex-none {{ $section === $key ? 'bg-stone-950 text-white' : 'text-stone-600 hover:bg-stone-100' }}" @if($section === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        @if($section === 'discounts')
            {{-- ===================================================== DISCOUNTS --}}
            <div x-data="{ creating: {{ $editingKey === 'new' ? 'true' : 'false' }} }" x-on:close-form="creating = false" class="space-y-5">
                {{-- What shoppers get right now --}}
                <div class="flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $homepageCampaign ? 'bg-brand text-stone-950' : 'bg-stone-100 text-stone-400' }} text-lg font-extrabold">%</span>
                        <div class="text-sm">
                            @if($liveAutomatic->isNotEmpty())
                                <p class="font-extrabold text-stone-950">Shoppers get {{ $liveAutomatic->first()->discount_percent }}% off every order right now</p>
                                <p class="text-stone-500">From “{{ $liveAutomatic->first()->name }}”, applied automatically at checkout.</p>
                            @else
                                <p class="font-extrabold text-stone-950">No automatic discount is running</p>
                                <p class="text-stone-500">{{ $tabCounts['live'] ? 'Only promo-code campaigns are live.' : 'Shoppers pay full price unless they have a code.' }}</p>
                            @endif
                            @if($homepageCampaign)
                                <p class="mt-0.5 text-xs text-stone-500">Homepage badge: <b class="text-stone-700">{{ $homepageCampaign->discount_percent }}% off · {{ $homepageCampaign->name }}{{ $homepageCampaign->code ? ' · '.$homepageCampaign->code : '' }}</b></p>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="backend-btn-primary shrink-0" x-on:click="creating = true; $nextTick(() => $refs.newForm.scrollIntoView({ behavior: 'smooth', block: 'center' }))">+ New campaign</button>
                </div>

                @if($liveAutomatic->count() > 1)
                    <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        <b>{{ $liveAutomatic->count() }} automatic discounts are live.</b> Only the biggest ({{ $liveAutomatic->first()->discount_percent }}%, “{{ $liveAutomatic->first()->name }}”) is applied. The others do nothing until it ends. Pause the ones you don't need.
                    </p>
                @endif

                {{-- New campaign --}}
                <section x-ref="newForm" x-cloak x-show="creating" class="rounded-2xl border-2 border-brand bg-white p-5">
                    <h2 class="mb-4 text-lg font-extrabold text-stone-950">New campaign</h2>
                    @include('superadmin.marketing._campaign-form', ['campaign' => null])
                </section>

                <x-admin-tabs route="superadmin.marketing.index" :current="$tab" :counts="$tabCounts" :tabs="$tabs" :keep="['section' => 'discounts']" />

                <div class="grid gap-4 lg:grid-cols-2 lg:items-start">
                    @forelse($shown as $campaign)
                        @php
                            $archived = $campaign->trashed();
                            $state = $campaign->state();
                            [$stateLabel, $stateClass] = $archived ? ['Archived', 'bg-stone-200 text-stone-600'] : \App\Models\DiscountCampaign::STATES[$state];
                            $usedPct = $campaign->max_uses ? min(100, round($campaign->used_count / $campaign->max_uses * 100)) : null;
                            $schedule = match (true) {
                                $state === 'scheduled' => 'Starts '.$when($campaign->starts_at).' · in '.$campaign->starts_at->diffForHumans(null, true),
                                $state === 'ended' => 'Ended '.$when($campaign->ends_at),
                                $campaign->ends_at !== null => 'Ends '.$when($campaign->ends_at).' · '.$campaign->ends_at->diffForHumans(null, true).' left',
                                default => 'No end date',
                            };
                        @endphp
                        <article class="flex flex-col rounded-2xl border bg-white {{ $state === 'live' && ! $archived ? 'border-emerald-200' : 'border-stone-200' }}" x-bind:class="editing && 'lg:col-span-2'" x-data="{ editing: {{ $editingKey === (string) $campaign->id ? 'true' : 'false' }} }" x-on:close-form="editing = false">
                            <div class="flex-1 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-extrabold text-stone-950">{{ $campaign->name }}</h3>
                                        <p class="mt-0.5 text-sm text-stone-600">{{ $schedule }}</p>
                                    </div>
                                    <span class="flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-extrabold uppercase {{ $stateClass }}">
                                        @if($state === 'live' && ! $archived)<span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>@endif
                                        {{ $stateLabel }}
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <span class="rounded-xl bg-stone-950 px-3 py-1.5 text-lg font-extrabold text-brand">{{ $campaign->discount_percent }}% off</span>
                                    @if($campaign->isAutomatic())
                                        <span class="rounded-xl bg-brand-tint px-3 py-2 text-xs font-bold text-brand-deep">Automatic for everyone</span>
                                    @else
                                        <button type="button" class="group inline-flex items-center gap-2 rounded-xl border border-dashed border-stone-400 px-3 py-1.5 font-mono text-sm font-bold text-stone-900 hover:border-brand"
                                            x-data="{ copied: false }" x-on:click="navigator.clipboard?.writeText(@js($campaign->code)); copied = true; setTimeout(() => copied = false, 1500)" title="Copy code">
                                            {{ $campaign->code }}
                                            <span class="font-sans text-[11px] text-stone-400 group-hover:text-brand-deep" x-text="copied ? 'Copied' : 'Copy'">Copy</span>
                                        </button>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <div class="flex justify-between text-xs">
                                        <span class="font-bold text-stone-600">Used {{ $campaign->used_count }} {{ Str::plural('time', $campaign->used_count) }}{{ $campaign->max_uses ? ' of '.$campaign->max_uses : '' }}</span>
                                        <span class="text-stone-400">{{ $campaign->max_uses ? max(0, $campaign->max_uses - $campaign->used_count).' left' : 'No limit' }}</span>
                                    </div>
                                    @if($usedPct !== null)
                                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full {{ $usedPct >= 100 ? 'bg-stone-400' : 'bg-brand' }}" style="width: {{ $usedPct }}%"></div></div>
                                    @endif
                                </div>
                                @if($campaign->description)
                                    <p class="mt-3 text-xs text-stone-500">{{ $campaign->description }}</p>
                                @endif
                            </div>

                            {{-- Actions for the current state --}}
                            <div class="flex flex-wrap items-center gap-2 border-t border-stone-100 bg-stone-50/70 px-5 py-3">
                                @if($archived)
                                    <form method="POST" action="{{ route('superadmin.marketing.campaigns.restore', $campaign->id) }}">@csrf<button class="backend-btn-primary h-9 px-3 text-xs">Restore</button></form>
                                    <span class="text-xs text-stone-500">Archived {{ $campaign->deleted_at->diffForHumans() }}</span>
                                @else
                                    @if(in_array($state, ['live', 'scheduled'], true))
                                        <form method="POST" action="{{ route('superadmin.marketing.campaigns.state', $campaign) }}">@csrf @method('PATCH')<button name="action" value="pause" class="inline-flex h-9 items-center rounded-xl bg-amber-100 px-3 text-xs font-extrabold text-amber-900 hover:bg-amber-200">Pause</button></form>
                                    @elseif($state === 'paused')
                                        <form method="POST" action="{{ route('superadmin.marketing.campaigns.state', $campaign) }}">@csrf @method('PATCH')<button name="action" value="resume" class="inline-flex h-9 items-center rounded-xl bg-emerald-600 px-3 text-xs font-extrabold text-white hover:bg-emerald-700">{{ $campaign->starts_at?->isFuture() ? 'Switch on (starts '.$when($campaign->starts_at).')' : 'Resume' }}</button></form>
                                    @endif
                                    @if($state === 'live')
                                        <form method="POST" action="{{ route('superadmin.marketing.campaigns.state', $campaign) }}" onsubmit="return confirm('End “{{ addslashes($campaign->name) }}” now? Shoppers will stop getting this discount.')">@csrf @method('PATCH')<button name="action" value="end" class="h-9 px-2 text-xs font-bold text-stone-500 hover:text-red-600">End now</button></form>
                                    @endif
                                    <button type="button" class="h-9 px-2 text-xs font-bold text-stone-700 hover:text-brand-deep" x-on:click="editing = !editing" x-text="editing ? 'Close' : '{{ in_array($state, ['ended', 'used_up'], true) ? 'Edit to run again' : 'Edit' }}'">Edit</button>
                                    <span class="ml-auto flex items-center gap-1">
                                        <form method="POST" action="{{ route('superadmin.marketing.campaigns.duplicate', $campaign) }}">@csrf<button class="h-9 px-2 text-xs font-bold text-stone-500 hover:text-stone-950" title="Make a paused copy">Duplicate</button></form>
                                        @unless($state === 'live')
                                            <form method="POST" action="{{ route('superadmin.marketing.campaigns.destroy', $campaign) }}" onsubmit="return confirm('Archive “{{ addslashes($campaign->name) }}”? You can restore it later.')">@csrf @method('DELETE')<button class="h-9 px-2 text-xs font-bold text-stone-500 hover:text-red-600">Archive</button></form>
                                        @endunless
                                    </span>
                                @endif
                            </div>

                            @unless($archived)
                                <div x-cloak x-show="editing" class="border-t border-stone-200 p-5">
                                    @include('superadmin.marketing._campaign-form', ['campaign' => $campaign])
                                </div>
                            @endunless
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center lg:col-span-2">
                            <p class="font-extrabold text-stone-950">{{ ['live' => 'No campaigns are live', 'scheduled' => 'Nothing scheduled', 'paused' => 'Nothing paused', 'finished' => 'No finished campaigns', 'archived' => 'Archive is empty'][$tab] }}</p>
                            @if(in_array($tab, ['live', 'scheduled'], true))
                                <button type="button" class="btn-link mt-2" x-on:click="creating = true">Create a campaign</button>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- ===================================================== HOMEPAGE IMAGE --}}
            <div class="space-y-6" x-data="{ adding: {{ $slides->isEmpty() || (old('section') === 'homepage' && $errors->any()) ? 'true' : 'false' }} }">
                <section class="grid gap-6 rounded-2xl border border-stone-200 bg-white p-5 sm:p-6 lg:grid-cols-[minmax(0,20rem)_1fr] lg:items-center">
                    {{-- Preview in the same round striped badge the homepage uses --}}
                    <div class="relative mx-auto w-full max-w-[16rem]">
                        <div class="flyer-stripes absolute -inset-[7%] rounded-full"></div>
                        <div class="absolute -inset-[2.5%] rounded-full bg-stone-950"></div>
                        <div class="relative aspect-square overflow-hidden rounded-full bg-brand-tint ring-4 ring-brand">
                            @if($liveSlide)
                                <img src="{{ $liveSlide->image_url }}" alt="{{ $liveSlide->alt_text ?: 'Current homepage image' }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center p-6 text-center display-heading text-2xl text-brand-deep">Built to last.</div>
                            @endif
                        </div>
                        @if($liveSlide?->caption)
                            <p class="absolute -bottom-3 left-1/2 z-10 w-max max-w-[95%] -translate-x-1/2 -rotate-2 rounded-xl border-2 border-stone-950 bg-brand px-3 py-1.5 text-center text-xs font-extrabold text-stone-950 shadow-[3px_3px_0_0_#0c0a09]">{{ $liveSlide->caption }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-emerald-700">{{ $liveSlide ? 'On the homepage now' : 'No image chosen' }}</p>
                        <h2 class="mt-1 text-2xl font-extrabold text-stone-950">{{ $liveSlide ? ($liveSlide->title ?: 'Untitled image') : 'Visitors see the default design' }}</h2>
                        <p class="mt-2 max-w-lg text-sm leading-6 text-stone-600">
                            {{ $liveSlide ? 'This is how it appears in the homepage hero. The orange sticker shows the image’s caption.' : 'Pick an image from the library below, or add a new one.' }}
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" class="backend-btn-primary" x-on:click="adding = true; $nextTick(() => $refs.addForm.scrollIntoView({ behavior: 'smooth', block: 'center' }))">+ Add image</button>
                            @if($liveSlide)
                                <form method="POST" action="{{ route('superadmin.marketing.slides.take-off', $liveSlide) }}" onsubmit="return confirm('Take this image off the homepage? Visitors will see the default design.')">@csrf<button class="btn-secondary">Take off homepage</button></form>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Add image --}}
                <section x-ref="addForm" x-cloak x-show="adding" class="rounded-2xl border-2 border-brand bg-white p-5">
                    <h2 class="text-lg font-extrabold text-stone-950">Add an image</h2>
                    <p class="mt-1 text-sm text-stone-500">Square photos work best: the homepage shows it in a circle. JPG, PNG or WebP up to 5 MB.</p>
                    <form method="POST" action="{{ route('superadmin.marketing.slides.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="section" value="homepage">
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-bold text-stone-600" for="new-file">Upload a photo</label>
                            <input id="new-file" type="file" name="image_file" accept="image/*" class="backend-field py-2">
                            <details class="mt-2 text-xs text-stone-500"><summary class="cursor-pointer font-bold">Or use an image link instead</summary><input name="image_url" type="url" placeholder="https://…" class="backend-field mt-2"></details>
                            @error('image_url')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            @error('image_file')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-stone-600" for="new-title">Name <span class="font-normal text-stone-400">(only you see this)</span></label>
                            <input id="new-title" name="title" class="backend-field" placeholder="e.g. Laptop on desk">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-stone-600" for="new-caption">Sticker text <span class="font-normal text-stone-400">(optional, max 120)</span></label>
                            <input id="new-caption" name="caption" maxlength="120" class="backend-field" placeholder="e.g. Tested. Trusted. On campus.">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-bold text-stone-600" for="new-alt">Describe the image <span class="font-normal text-stone-400">(for screen readers)</span></label>
                            <input id="new-alt" name="alt_text" class="backend-field" placeholder="e.g. A silver laptop on a desk with a coffee cup">
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4 sm:col-span-2">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-stone-800"><input type="checkbox" name="make_live" value="1" class="rounded text-brand-deep focus:ring-brand" @checked($slides->isEmpty())> Put it on the homepage now</label>
                            <div class="flex gap-2">
                                <button type="button" class="btn-secondary h-10 px-4" x-on:click="adding = false">Cancel</button>
                                <button class="backend-btn-primary h-10">Add image</button>
                            </div>
                        </div>
                    </form>
                </section>

                {{-- Library --}}
                <section>
                    <h2 class="mb-3 text-xs font-extrabold uppercase tracking-[0.16em] text-stone-500">Image library · {{ $slides->count() }}</h2>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($slides as $slide)
                            <article class="flex flex-col overflow-hidden rounded-2xl border bg-white {{ $slide->is_active ? 'border-emerald-300 ring-2 ring-emerald-100' : 'border-stone-200' }}" x-data="{ editing: false }">
                                <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                                    <img src="{{ $slide->image_url }}" alt="{{ $slide->alt_text ?: '' }}" class="h-full w-full object-cover" loading="lazy">
                                    @if($slide->is_active)<span class="absolute left-3 top-3 rounded-full bg-emerald-600 px-2.5 py-1 text-[11px] font-extrabold uppercase text-white">On homepage</span>@endif
                                </div>
                                <div class="flex flex-1 flex-col p-4">
                                    <h3 class="truncate font-extrabold text-stone-950">{{ $slide->title ?: 'Untitled image' }}</h3>
                                    <p class="mt-0.5 truncate text-xs text-stone-500">{{ $slide->caption ? 'Sticker: “'.$slide->caption.'”' : 'No sticker text' }}</p>
                                    <div class="mt-auto flex flex-wrap items-center gap-2 pt-4">
                                        @unless($slide->is_active)
                                            <form method="POST" action="{{ route('superadmin.marketing.slides.make-live', $slide) }}">@csrf<button class="backend-btn-primary h-9 px-3 text-xs">Put on homepage</button></form>
                                        @endunless
                                        <button type="button" class="h-9 px-2 text-xs font-bold text-stone-700 hover:text-brand-deep" x-on:click="editing = !editing" x-text="editing ? 'Close' : 'Edit'">Edit</button>
                                        <form method="POST" action="{{ route('superadmin.marketing.slides.destroy', $slide) }}" class="ml-auto" onsubmit="return confirm('{{ $slide->is_active ? 'This image is on the homepage. Remove it anyway?' : 'Remove this image? You can restore it later.' }}')">@csrf @method('DELETE')<button class="h-9 px-2 text-xs font-bold text-stone-500 hover:text-red-600">Remove</button></form>
                                    </div>
                                </div>
                                <form x-cloak x-show="editing" method="POST" action="{{ route('superadmin.marketing.slides.update', $slide) }}" enctype="multipart/form-data" class="space-y-3 border-t border-stone-200 p-4">
                                    @csrf @method('PATCH')
                                    <div><label class="mb-1 block text-xs font-bold text-stone-600">Name</label><input name="title" value="{{ $slide->title }}" class="backend-field"></div>
                                    <div><label class="mb-1 block text-xs font-bold text-stone-600">Sticker text</label><input name="caption" value="{{ $slide->caption }}" maxlength="120" class="backend-field"></div>
                                    <div><label class="mb-1 block text-xs font-bold text-stone-600">Describe the image</label><input name="alt_text" value="{{ $slide->alt_text }}" class="backend-field"></div>
                                    <div><label class="mb-1 block text-xs font-bold text-stone-600">Replace photo <span class="font-normal text-stone-400">(optional)</span></label><input type="file" name="image_file" accept="image/*" class="backend-field py-2"></div>
                                    <button class="backend-btn-primary h-10 w-full">Save</button>
                                </form>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center sm:col-span-2 xl:col-span-3">
                                <p class="font-extrabold text-stone-950">No images yet</p>
                                <p class="mt-1 text-sm text-stone-500">Add one above to put it on the homepage.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                @if($archivedSlides->isNotEmpty())
                    <details class="rounded-2xl border border-stone-200 bg-white p-4">
                        <summary class="cursor-pointer text-sm font-bold text-stone-700">Removed images ({{ $archivedSlides->count() }})</summary>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach($archivedSlides as $slide)
                                <div class="flex items-center gap-3 rounded-xl border border-stone-200 p-2">
                                    <img src="{{ $slide->image_url }}" alt="" class="h-12 w-12 shrink-0 rounded-lg object-cover opacity-70" loading="lazy">
                                    <span class="min-w-0 flex-1 truncate text-sm font-bold text-stone-700">{{ $slide->title ?: 'Untitled image' }}</span>
                                    <form method="POST" action="{{ route('superadmin.marketing.slides.restore', $slide->id) }}">@csrf<button class="text-xs font-extrabold text-brand-deep hover:underline">Restore</button></form>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        @endif
    </div>
</x-superadmin-layout>
