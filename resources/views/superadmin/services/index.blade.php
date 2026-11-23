<x-superadmin-layout title="Repairs & services">
    @php
        $intro = [
            'repair' => 'Fixing something broken: screens, batteries, charging ports, water damage.',
            'upgrade' => 'Making a working device better: more RAM, a faster SSD, a clean refresh.',
            'diagnosis' => 'For customers who don’t know what’s wrong. Usually free or a small fee taken off the repair.',
        ];
        $newOpen = old('form_key') === 'new';
    @endphp

    <div class="space-y-6" x-data="{ adding: {{ $newOpen ? 'true' : 'false' }} }" x-on:close-form="adding = false">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="brand-badge">Store setup</p>
                <h1 class="display-heading mt-3 text-3xl text-stone-950">Repairs &amp; services</h1>
                <p class="mt-1 max-w-2xl text-sm text-stone-600">The workshop menu on the public Repairs page. Customers pick a service and book a drop-off or pickup; bookings arrive in the orders admin.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('services.index') }}" target="_blank" rel="noopener" class="btn-secondary">View Repairs page &#8599;</a>
                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Bookings</a>
                <button type="button" class="backend-btn-primary" x-on:click="adding = true; $nextTick(() => $refs.newForm.scrollIntoView({ behavior: 'smooth', block: 'center' }))">+ Add service</button>
            </div>
        </div>

        <section x-ref="newForm" x-cloak x-show="adding" class="rounded-2xl border-2 border-brand bg-white p-5">
            <h2 class="mb-4 text-lg font-extrabold text-stone-950">New service</h2>
            @include('superadmin.services._form', ['service' => null])
        </section>

        @foreach($groups as $type => $services)
            <section>
                <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="text-xl font-extrabold text-stone-950">{{ \App\Models\Service::TYPES[$type] }}{{ $type === 'diagnosis' ? '' : 's' }} <span class="text-sm font-bold text-stone-400">{{ $services->count() }}</span></h2>
                    <p class="text-xs text-stone-500">{{ $intro[$type] }}</p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                    @forelse($services as $service)
                        <div class="border-b border-stone-100 last:border-0 {{ $service->is_active ? '' : 'bg-stone-50' }}" x-data="{ editing: {{ (string) old('form_key') === (string) $service->id ? 'true' : 'false' }} }" x-on:close-form.stop="editing = false">
                            <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center">
                                {{-- Order --}}
                                <div class="hidden shrink-0 flex-col sm:flex">
                                    @foreach(['up' => 'M4.5 15.75l7.5-7.5 7.5 7.5', 'down' => 'M19.5 8.25l-7.5 7.5-7.5-7.5'] as $dir => $path)
                                        <form method="POST" action="{{ route('superadmin.services.move', $service) }}">@csrf @method('PATCH')
                                            <button name="direction" value="{{ $dir }}" class="rounded p-0.5 text-stone-300 hover:bg-stone-100 hover:text-stone-700 disabled:invisible" @disabled(($dir === 'up' && $loop->parent->first) || ($dir === 'down' && $loop->parent->last)) aria-label="Move {{ $dir }}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" /></svg>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-extrabold {{ $service->is_active ? 'text-stone-950' : 'text-stone-500' }}">{{ $service->name }}</p>
                                        @unless($service->is_active)<span class="rounded-full bg-stone-200 px-2 py-0.5 text-[10px] font-extrabold uppercase text-stone-600">Hidden</span>@endunless
                                    </div>
                                    <p class="mt-0.5 line-clamp-1 text-sm text-stone-600">{{ $service->description }}</p>
                                    <p class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-stone-500">
                                        @if($service->turnaround)<span>&#9201; {{ $service->turnaround }}</span>@endif
                                        <span>{{ $service->devicesLabel() ?? 'Any device' }}</span>
                                        @if($service->partsCategory)<span>Parts: {{ $service->partsCategory->name }}</span>@endif
                                        <span>{{ $service->bookings_count }} {{ Str::plural('booking', $service->bookings_count) }}@if($service->open_bookings_count) · <b class="text-brand-deep">{{ $service->open_bookings_count }} open</b>@endif</span>
                                    </p>
                                </div>

                                <p class="shrink-0 whitespace-nowrap text-lg font-extrabold text-stone-950 sm:w-44 sm:text-right">{{ $service->priceLabel() }}</p>

                                <div class="flex shrink-0 items-center gap-1">
                                    <button type="button" class="h-9 px-2 text-xs font-bold text-stone-700 hover:text-brand-deep" x-on:click="editing = !editing" x-text="editing ? 'Close' : 'Edit'">Edit</button>
                                    <form method="POST" action="{{ route('superadmin.services.toggle', $service) }}">@csrf @method('PATCH')
                                        <button class="h-9 px-2 text-xs font-bold {{ $service->is_active ? 'text-stone-500 hover:text-amber-700' : 'text-emerald-700 hover:text-emerald-900' }}">{{ $service->is_active ? 'Hide' : 'Show' }}</button>
                                    </form>
                                    @if($service->bookings_count === 0)
                                        <form method="POST" action="{{ route('superadmin.services.destroy', $service) }}" onsubmit="return confirm('Delete “{{ addslashes($service->name) }}”?')">@csrf @method('DELETE')
                                            <button class="h-9 px-2 text-xs font-bold text-stone-400 hover:text-red-600">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div x-cloak x-show="editing" class="border-t border-stone-100 bg-stone-50/60 px-5 py-5">
                                @include('superadmin.services._form', ['service' => $service])
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-stone-500">
                            No {{ strtolower(\App\Models\Service::TYPES[$type]) }} services yet.
                            <button type="button" class="font-bold text-brand-deep hover:underline" x-on:click="adding = true">Add one</button>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</x-superadmin-layout>
