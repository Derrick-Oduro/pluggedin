<x-superadmin-layout title="Shop applications">
    <div class="space-y-5">
        <x-page-header badge="To do" title="Shop applications" subtitle="Approve trustworthy campus sellers, set their plan, or suspend a shop. Owners are notified when the status changes." />

        <x-admin-tabs route="superadmin.shops.index" param="status" :current="$status" :counts="$tabCounts"
            :tabs="['pending' => 'Waiting for approval', 'approved' => 'Live', 'suspended' => 'Suspended', 'all' => 'All']" />

        <div class="space-y-4">
            @forelse($shops as $shop)
                <article class="overflow-hidden rounded-2xl border bg-white {{ $shop->status === 'pending' ? 'border-l-4 border-stone-200 border-l-brand' : 'border-stone-200' }}" x-data="{ more: false }">
                    <div class="flex flex-col gap-4 p-5 sm:flex-row">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-stone-950 text-xl font-extrabold text-brand">
                            @if($shop->logo_path)
                                <img src="{{ asset('storage/'.$shop->logo_path) }}" alt="" class="h-full w-full object-cover">
                            @else
                                {{ Str::upper(Str::substr($shop->name, 0, 1)) }}
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-extrabold text-stone-950">{{ $shop->name }}</h2>
                                <x-status-badge :status="$shop->status">{{ $shop->status === 'approved' ? 'Live' : ucfirst($shop->status) }}</x-status-badge>
                                <span class="rounded-full bg-brand-tint px-2.5 py-1 text-[11px] font-bold text-brand-deep">{{ $plans[$shop->plan] ?? Str::headline($shop->plan) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-stone-600">
                                {{ $shop->owner?->name ?? 'Deleted user' }}
                                @if($shop->owner)· <a href="mailto:{{ $shop->owner->email }}" class="text-brand-deep hover:underline">{{ $shop->owner->email }}</a>@endif
                                @if($shop->phone)· <a href="tel:{{ preg_replace('/[^0-9+]/', '', $shop->phone) }}" class="hover:underline">{{ $shop->phone }}</a>@endif
                                @if($shop->campus)· {{ $shop->campus }}@endif
                            </p>
                            @if($shop->description)
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-stone-700">{{ $shop->description }}</p>
                            @endif
                            <p class="mt-2 text-xs text-stone-500">
                                Applied {{ $shop->created_at->diffForHumans() }} · {{ $shop->live_products_count }} live of {{ $shop->products_count }} {{ Str::plural('product', $shop->products_count) }}
                                @if($shop->status === 'approved') · <a href="{{ route('shops.show', $shop) }}" target="_blank" class="font-bold text-brand-deep hover:underline">View shop page</a>@endif
                            </p>
                            @if($shop->admin_notes)
                                <p class="mt-2 rounded-xl bg-stone-50 px-3 py-2 text-xs text-stone-600"><b>Note:</b> {{ $shop->admin_notes }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 border-t border-stone-100 bg-stone-50/70 px-5 py-3">
                        {{-- Quick decisions keep the current plan and note --}}
                        @foreach(['approved' => ['Approve', 'backend-btn-primary h-10'], 'suspended' => ['Suspend', 'inline-flex h-10 items-center rounded-xl border border-red-200 bg-white px-4 text-sm font-bold text-red-600 hover:bg-red-50']] as $target => [$label, $classes])
                            @if($shop->status !== $target)
                                <form method="POST" action="{{ route('superadmin.shops.update', $shop) }}" @if($target === 'suspended') onsubmit="return confirm('Suspend {{ addslashes($shop->name) }}? It will be hidden from the marketplace.')" @endif>
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $target }}">
                                    <input type="hidden" name="plan" value="{{ $shop->plan }}">
                                    <input type="hidden" name="admin_notes" value="{{ $shop->admin_notes }}">
                                    <button class="{{ $classes }}">{{ $shop->status === 'suspended' && $target === 'approved' ? 'Reinstate' : $label }}</button>
                                </form>
                            @endif
                        @endforeach
                        <button type="button" class="ml-auto text-sm font-bold text-stone-500 hover:text-brand-deep" x-on:click="more = !more" x-text="more ? 'Close' : 'Change plan or note'">Change plan or note</button>
                    </div>

                    <form method="POST" action="{{ route('superadmin.shops.update', $shop) }}" x-cloak x-show="more" class="grid gap-3 border-t border-stone-100 px-5 py-4 sm:grid-cols-[160px_170px_1fr_auto] sm:items-end">
                        @csrf @method('PATCH')
                        <div>
                            <label class="mb-1 block text-xs font-bold text-stone-500" for="status-{{ $shop->id }}">Status</label>
                            <select id="status-{{ $shop->id }}" class="backend-field" name="status">
                                <option value="pending" @selected($shop->status === 'pending')>Pending</option>
                                <option value="approved" @selected($shop->status === 'approved')>Live</option>
                                <option value="suspended" @selected($shop->status === 'suspended')>Suspended</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-stone-500" for="plan-{{ $shop->id }}">Plan</label>
                            <select id="plan-{{ $shop->id }}" class="backend-field" name="plan">
                                @foreach($plans as $key => $label)<option value="{{ $key }}" @selected($shop->plan === $key)>{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-stone-500" for="note-{{ $shop->id }}">Note <span class="font-normal">(shown to the owner if suspended)</span></label>
                            <input id="note-{{ $shop->id }}" class="backend-field" name="admin_notes" value="{{ $shop->admin_notes }}">
                        </div>
                        <button class="backend-btn-primary">Save</button>
                    </form>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white py-14 text-center">
                    <p class="font-extrabold text-stone-950">Nothing here</p>
                    <p class="mt-1 text-sm text-stone-500">{{ $status === 'pending' ? 'No shop applications are waiting.' : 'No shops in this view.' }}</p>
                </div>
            @endforelse
        </div>

        <div>{{ $shops->links() }}</div>
    </div>
</x-superadmin-layout>
