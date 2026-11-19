<x-admin-layout>
        <x-page-header badge="Admin" title="Referrals" subtitle="Referral usage, conversions and points awarded." />

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Total Links</p>
                <p class="text-2xl font-bold text-brand">{{ $summary['links'] }}</p>
            </div>
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Total Clicks</p>
                <p class="text-2xl font-bold">{{ $summary['clicks'] }}</p>
            </div>
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Total Conversions</p>
                <p class="text-2xl font-bold">{{ $summary['conversions'] }}</p>
            </div>
            <div class="backend-card">
                <p class="text-sm text-stone-600 mb-2">Points Awarded</p>
                <p class="text-2xl font-bold text-brand">{{ number_format($summary['points_awarded']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white border border-stone-200 rounded-xl p-6">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Top Referral Links</h2>
                <div class="space-y-3">
                    @forelse($topLinks as $link)
                        <div class="border border-stone-200 rounded-lg p-3">
                            <p class="font-semibold">{{ $link->product->name }}</p>
                            <p class="text-sm text-stone-600">Owner: {{ $link->user->name }}</p>
                            <p class="text-sm text-stone-600">Clicks: {{ $link->clicks }} | Conversions: {{ $link->conversions }}</p>
                        </div>
                    @empty
                        <p class="text-stone-600">No referral links found.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-xl p-6">
                <h2 class="text-lg font-extrabold text-stone-950 mb-4">Top Referrers</h2>
                <div class="space-y-3">
                    @forelse($topReferrers as $referrer)
                        <div class="border border-stone-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $referrer->name }}</p>
                                <p class="text-sm text-stone-600">Points Balance: {{ number_format($referrer->points_balance) }}</p>
                            </div>
                            <span class="text-sm font-semibold text-brand">{{ $referrer->referral_links_sum_conversions ?? 0 }} conversions</span>
                        </div>
                    @empty
                        <p class="text-stone-600">No referrer activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
</x-admin-layout>
