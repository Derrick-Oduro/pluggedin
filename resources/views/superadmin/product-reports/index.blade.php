<x-superadmin-layout title="Listing reports">
    <div class="space-y-5">
        <x-page-header badge="To do" title="Listing reports" subtitle="Members flag listings that look wrong. Check the listing, then keep it up or take it down. Taking it down tells the seller why and closes every report on it." />

        <x-admin-tabs route="superadmin.product-reports.index" param="status" :current="$status" :counts="$tabCounts"
            :tabs="['open' => 'Open', 'resolved' => 'Resolved', 'dismissed' => 'Dismissed', 'all' => 'All']" />

        <div class="space-y-4">
            @forelse($reports as $report)
                @php
                    $product = $report->product;
                    $others = ($openPerProduct[$report->product_id] ?? 1) - 1;
                    $isLive = $product && $product->status === 'approved';
                @endphp
                <article class="overflow-hidden rounded-2xl border bg-white {{ $report->status === 'open' ? 'border-l-4 border-stone-200 border-l-red-500' : 'border-stone-200' }}" x-data="{ takedown: false }">
                    <div class="flex flex-col gap-4 p-5 sm:flex-row">
                        <a href="{{ $product ? route('products.show', $product) : '#' }}" target="_blank" class="block h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-stone-100">
                            @if($product?->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="" class="h-full w-full object-cover" loading="lazy">@endif
                        </a>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0">
                                    @if($product)
                                        <a href="{{ route('products.show', $product) }}" target="_blank" class="font-extrabold text-stone-950 hover:text-brand-deep">{{ $product->name }} <span class="text-xs text-stone-400">&#8599;</span></a>
                                        <p class="text-sm text-stone-600">
                                            GH₵{{ number_format($product->price, 2) }} · {{ $product->category?->name }}
                                            · seller {{ $product->uploader?->name ?? 'PluggedIn' }}
                                            · <span class="{{ $isLive ? 'text-emerald-700' : 'text-stone-500' }} font-bold">{{ $isLive ? 'live' : 'not live ('.$product->status.')' }}</span>
                                        </p>
                                    @else
                                        <p class="font-extrabold text-stone-400">Listing deleted</p>
                                    @endif
                                </div>
                                <x-status-badge :status="$report->status" />
                            </div>

                            <div class="mt-3 rounded-xl bg-stone-50 px-3 py-2.5 text-sm">
                                <p class="font-bold text-stone-950">{{ $reasons[$report->reason] ?? Str::headline($report->reason) }}</p>
                                @if($report->details)<p class="mt-1 whitespace-pre-line text-stone-700">“{{ $report->details }}”</p>@endif
                                <p class="mt-1 text-xs text-stone-500">Reported by {{ $report->reporter?->name ?? 'a deleted user' }} · {{ $report->created_at->diffForHumans() }}</p>
                            </div>
                            @if($report->status === 'open' && $others > 0)
                                <p class="mt-2 text-xs font-extrabold text-red-600">+ {{ $others }} other open {{ Str::plural('report', $others) }} on this listing</p>
                            @endif
                        </div>
                    </div>

                    @if($report->status === 'open')
                        <div class="flex flex-wrap items-center gap-2 border-t border-stone-100 bg-stone-50/70 px-5 py-3">
                            @if($isLive)
                                <button type="button" class="inline-flex h-10 items-center rounded-xl bg-red-600 px-4 text-sm font-bold text-white hover:bg-red-700" x-on:click="takedown = !takedown">Take listing down…</button>
                            @endif
                            <form method="POST" action="{{ route('superadmin.product-reports.update', $report) }}">
                                @csrf @method('PATCH')
                                <button name="action" value="resolve" class="btn-secondary h-10 px-4" title="You dealt with it, e.g. the seller fixed the listing">Resolved, keep it up</button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.product-reports.update', $report) }}">
                                @csrf @method('PATCH')
                                <button name="action" value="dismiss" class="h-10 px-3 text-sm font-bold text-stone-500 hover:text-stone-950" title="Nothing wrong with the listing">Dismiss</button>
                            </form>
                            @if($product)
                                <a href="{{ route('admin.products.edit', $product) }}" class="ml-auto text-sm font-bold text-brand-deep hover:underline">Edit listing</a>
                            @endif
                        </div>
                        @if($isLive)
                            <form method="POST" action="{{ route('superadmin.product-reports.update', $report) }}" x-cloak x-show="takedown" class="grid gap-3 border-t border-red-100 bg-red-50/60 px-5 py-4 sm:grid-cols-[1fr_auto] sm:items-end">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="takedown">
                                <div>
                                    <label for="note-{{ $report->id }}" class="mb-1 block text-xs font-bold text-red-900">Reason the seller will see</label>
                                    <input id="note-{{ $report->id }}" name="note" class="backend-field" maxlength="500" placeholder="Removed after a report: {{ $reasons[$report->reason] ?? $report->reason }}.">
                                </div>
                                <button class="inline-flex h-11 items-center rounded-xl bg-red-600 px-4 text-sm font-bold text-white hover:bg-red-700">Take down and notify seller</button>
                            </form>
                        @endif
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white py-14 text-center">
                    <p class="font-extrabold text-stone-950">{{ $status === 'open' ? 'No open reports' : 'Nothing here' }}</p>
                    <p class="mt-1 text-sm text-stone-500">{{ $status === 'open' ? 'The marketplace looks clean.' : 'No reports in this view.' }}</p>
                </div>
            @endforelse
        </div>

        <div>{{ $reports->links() }}</div>
    </div>
</x-superadmin-layout>
