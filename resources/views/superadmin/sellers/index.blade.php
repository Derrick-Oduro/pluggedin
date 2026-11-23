<x-superadmin-layout title="Seller verification">
    <div class="space-y-5">
        <x-page-header badge="People" title="Seller verification" subtitle="Give trusted sellers a verified badge. Look for a good number of live listings, few rejections and no open reports." />

        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-admin-tabs route="superadmin.sellers.index" :current="$tab" :counts="$tabCounts" :keep="array_filter(['q' => $search])"
                :tabs="['all' => 'All sellers', 'unverified' => 'Not verified', 'verified' => 'Verified']" />
            <form method="GET" class="flex w-full gap-2 sm:w-auto" role="search">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <label for="seller-search" class="sr-only">Search sellers</label>
                <input id="seller-search" type="search" name="q" value="{{ $search }}" placeholder="Name or email" class="backend-field sm:w-64">
                <button class="btn-secondary px-4">Search</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
            <div class="overflow-x-auto">
                <table class="backend-table min-w-[660px]">
                    <thead>
                        <tr>
                            <th class="backend-th">Seller</th>
                            <th class="backend-th">Live</th>
                            <th class="backend-th">Rejected</th>
                            <th class="backend-th">Open reports</th>
                            <th class="backend-th">Member since</th>
                            <th class="backend-th text-right">Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellers as $seller)
                            @php($reports = (int) ($openReports[$seller->id] ?? 0))
                            <tr class="backend-row">
                                <td class="backend-td">
                                    <p class="flex items-center gap-1.5 font-bold text-stone-950">
                                        {{ $seller->name }}
                                        @if($seller->seller_verified_at)
                                            <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="currentColor" aria-label="Verified"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                                        @endif
                                    </p>
                                    <a href="mailto:{{ $seller->email }}" class="text-xs text-stone-500 hover:text-brand-deep">{{ $seller->email }}</a>
                                </td>
                                <td class="backend-td"><b class="text-stone-950">{{ $seller->live_products_count }}</b> <span class="text-xs text-stone-400">of {{ $seller->uploaded_products_count }}</span></td>
                                <td class="backend-td {{ $seller->rejected_products_count ? 'font-bold text-amber-700' : 'text-stone-400' }}">{{ $seller->rejected_products_count }}</td>
                                <td class="backend-td">
                                    @if($reports)
                                        <a href="{{ route('superadmin.product-reports.index') }}" class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-extrabold text-red-700 hover:bg-red-100">{{ $reports }} open</a>
                                    @else
                                        <span class="text-stone-400">0</span>
                                    @endif
                                </td>
                                <td class="backend-td text-stone-500">{{ $seller->created_at->format('M Y') }}</td>
                                <td class="backend-td text-right">
                                    <form method="POST" action="{{ route('superadmin.sellers.verification', $seller) }}"
                                        @if($seller->seller_verified_at) onsubmit="return confirm('Remove the verified badge from {{ addslashes($seller->name) }}?')" @endif>
                                        @csrf @method('PATCH')
                                        @if($seller->seller_verified_at)
                                            <span class="mr-2 text-xs text-stone-500">since {{ $seller->seller_verified_at->format('M j, Y') }}</span>
                                            <button class="text-sm font-bold text-stone-500 hover:text-red-600">Remove</button>
                                        @else
                                            <button class="inline-flex h-9 items-center rounded-xl bg-stone-950 px-3 text-xs font-bold text-white hover:bg-brand hover:text-stone-950 disabled:cursor-not-allowed disabled:opacity-40" @disabled($reports > 0) @if($reports) title="Resolve the open reports first" @endif>Verify</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="backend-td py-12 text-center text-stone-500" colspan="6">{{ $search !== '' ? "No sellers match “{$search}”." : 'No sellers in this view.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $sellers->links() }}</div>
    </div>
</x-superadmin-layout>
