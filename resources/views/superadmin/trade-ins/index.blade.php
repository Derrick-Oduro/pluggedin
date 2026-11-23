<x-superadmin-layout title="Trade-ins">
    @php
        $steps = ['submitted' => 'Submitted', 'under_review' => 'Reviewing', 'quoted' => 'Quoted', 'accepted' => 'Accepted', 'completed' => 'Completed'];
        $rank = array_flip(array_keys($steps));
    @endphp

    <div class="space-y-5">
        <x-page-header badge="To do" title="Trade-ins" subtitle="Review devices, send an offer, then complete the swap once the customer accepts. Customers are notified at each step." />

        <x-admin-tabs route="superadmin.trade-ins.index" :current="$tab" :counts="$tabCounts"
            :tabs="['to_quote' => 'Needs a quote', 'quoted' => 'Waiting on customer', 'accepted' => 'Accepted · arrange swap', 'closed' => 'Closed', 'all' => 'All']" />

        <div class="space-y-4">
            @forelse($requests as $tradeIn)
                @php
                    $images = collect($tradeIn->images ?? [])->filter();
                    $declined = $tradeIn->status === 'declined';
                    $current = $rank[$tradeIn->status] ?? -1;
                @endphp
                <article class="overflow-hidden rounded-2xl border bg-white {{ in_array($tradeIn->status, ['submitted', 'under_review', 'accepted'], true) ? 'border-l-4 border-stone-200 border-l-brand' : 'border-stone-200' }}">
                    <div class="flex flex-col gap-5 p-5 lg:flex-row">
                        {{-- Photos --}}
                        <div class="flex shrink-0 gap-2 lg:w-44 lg:flex-col">
                            @forelse($images->take(3) as $i => $path)
                                <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl bg-stone-100 {{ $i === 0 ? 'h-28 w-28 lg:h-36 lg:w-44' : 'h-16 w-16 lg:hidden' }}">
                                    <img src="{{ asset('storage/'.$path) }}" alt="Photo {{ $i + 1 }} of the {{ $tradeIn->brand }} {{ $tradeIn->model }}" class="h-full w-full object-cover" loading="lazy">
                                </a>
                            @empty
                                <span class="flex h-28 w-28 items-center justify-center rounded-xl bg-stone-100 text-center text-xs font-bold text-stone-400 lg:h-36 lg:w-44">No photos</span>
                            @endforelse
                            @if($images->count() > 1)
                                <span class="hidden text-xs font-bold text-stone-500 lg:block">{{ $images->count() }} photos · click to enlarge</span>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-extrabold text-stone-950">{{ $tradeIn->brand }} {{ $tradeIn->model }}</h2>
                                    <p class="text-sm text-stone-600">{{ $tradeIn->device_type }} · condition <b>{{ ucfirst($tradeIn->condition) }}</b> · sent {{ $tradeIn->created_at->diffForHumans() }}</p>
                                </div>
                                <x-status-badge :status="$tradeIn->status" />
                            </div>

                            <dl class="mt-3 grid gap-x-6 gap-y-1.5 text-sm sm:grid-cols-[auto_1fr]">
                                <dt class="font-bold text-stone-500">Customer</dt>
                                <dd class="text-stone-800">{{ $tradeIn->user?->name ?? 'Deleted user' }} @if($tradeIn->user)<a href="mailto:{{ $tradeIn->user->email }}" class="text-brand-deep hover:underline">{{ $tradeIn->user->email }}</a>@endif</dd>
                                @if($tradeIn->specifications)<dt class="font-bold text-stone-500">Specs</dt><dd class="text-stone-800">{{ $tradeIn->specifications }}</dd>@endif
                                @if($tradeIn->notes)<dt class="font-bold text-stone-500">Their notes</dt><dd class="whitespace-pre-line text-stone-800">{{ $tradeIn->notes }}</dd>@endif
                                @if($tradeIn->desiredProduct)<dt class="font-bold text-stone-500">Wants</dt><dd><a href="{{ route('products.show', $tradeIn->desiredProduct) }}" class="font-bold text-brand-deep hover:underline" target="_blank">{{ $tradeIn->desiredProduct->name }}</a> <span class="text-stone-500">(GH₵{{ number_format($tradeIn->desiredProduct->price, 2) }})</span></dd>@endif
                                @if($tradeIn->quoted_value)<dt class="font-bold text-stone-500">Offer</dt><dd class="font-extrabold text-stone-950">GH₵{{ number_format($tradeIn->quoted_value, 2) }} @if($tradeIn->quoted_at)<span class="font-normal text-stone-500">· sent {{ $tradeIn->quoted_at->diffForHumans() }}</span>@endif</dd>@endif
                                @if($tradeIn->admin_notes)<dt class="font-bold text-stone-500">Note to customer</dt><dd class="text-stone-800">{{ $tradeIn->admin_notes }}</dd>@endif
                            </dl>

                            {{-- Progress --}}
                            @unless($declined)
                                <ol class="mt-4 flex flex-wrap gap-1.5 text-[11px] font-bold">
                                    @foreach($steps as $key => $label)
                                        <li class="rounded-full px-2.5 py-1 {{ $rank[$key] <= $current ? 'bg-brand-tint text-brand-deep' : 'bg-stone-100 text-stone-400' }}">{{ $loop->iteration }}. {{ $label }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="mt-4 rounded-xl bg-stone-100 px-3 py-2 text-sm text-stone-600">The customer declined this offer.</p>
                            @endunless
                        </div>
                    </div>

                    {{-- Next step --}}
                    @if(in_array($tradeIn->status, ['submitted', 'under_review', 'quoted'], true))
                        <div class="border-t border-stone-100 bg-stone-50/70 px-5 py-4" x-data="{ editing: {{ $tradeIn->status === 'quoted' ? 'false' : 'true' }} }">
                            @if($tradeIn->status === 'quoted')
                                <div class="flex flex-wrap items-center justify-between gap-3" x-show="!editing">
                                    <p class="text-sm text-stone-600">Waiting for the customer to accept or decline.</p>
                                    <button type="button" class="btn-secondary h-10 px-4" x-on:click="editing = true">Change offer</button>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('superadmin.trade-ins.quote', $tradeIn) }}" class="grid gap-3 sm:grid-cols-[170px_1fr_auto] sm:items-end" x-show="editing" @if($tradeIn->status === 'quoted') x-cloak @endif>
                                @csrf @method('PATCH')
                                <div>
                                    <label for="value-{{ $tradeIn->id }}" class="mb-1 block text-xs font-bold text-stone-500">Offer (GH₵)</label>
                                    <input id="value-{{ $tradeIn->id }}" name="quoted_value" type="number" step="0.01" min="1" class="backend-field" value="{{ $tradeIn->quoted_value }}" required>
                                </div>
                                <div>
                                    <label for="notes-{{ $tradeIn->id }}" class="mb-1 block text-xs font-bold text-stone-500">Note to the customer <span class="font-normal">(optional)</span></label>
                                    <input id="notes-{{ $tradeIn->id }}" name="admin_notes" class="backend-field" value="{{ $tradeIn->admin_notes }}" placeholder="e.g. Final value after we check the battery">
                                </div>
                                <button class="backend-btn-primary">{{ $tradeIn->status === 'quoted' ? 'Update offer' : 'Send offer' }}</button>
                            </form>
                            @if($tradeIn->status === 'submitted')
                                <form method="POST" action="{{ route('superadmin.trade-ins.review', $tradeIn) }}" class="mt-2">
                                    @csrf @method('PATCH')
                                    <button class="text-xs font-bold text-stone-500 hover:text-brand-deep">Not ready to quote? Mark as under review</button>
                                </form>
                            @endif
                        </div>
                    @elseif($tradeIn->status === 'accepted')
                        <form method="POST" action="{{ route('superadmin.trade-ins.complete', $tradeIn) }}" class="grid gap-3 border-t border-emerald-100 bg-emerald-50/60 px-5 py-4 sm:grid-cols-[1fr_auto] sm:items-end" onsubmit="return confirm('Mark this trade-in as completed?')">
                            @csrf @method('PATCH')
                            <div>
                                <p class="mb-2 text-sm font-bold text-emerald-900">Customer accepted. Arrange inspection and the swap, then mark it complete.</p>
                                <input name="admin_notes" class="backend-field" placeholder="Completion note (optional), e.g. Swapped for MacBook Air on Nov 24">
                            </div>
                            <button class="backend-btn-primary">Mark completed</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white py-14 text-center">
                    <p class="font-extrabold text-stone-950">Nothing here</p>
                    <p class="mt-1 text-sm text-stone-500">{{ $tab === 'to_quote' ? 'No trade-ins are waiting for a quote.' : 'No trade-ins in this view.' }}</p>
                </div>
            @endforelse
        </div>

        <div>{{ $requests->links() }}</div>
    </div>
</x-superadmin-layout>
