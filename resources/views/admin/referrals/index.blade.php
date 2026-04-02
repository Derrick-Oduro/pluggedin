<x-app-layout>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold">Referral Analytics</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-1">Monitor referral usage, conversions, and reward distribution.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-orange hover:text-orange-light font-semibold">Back to Dashboard</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Total Links</p>
                <p class="text-2xl font-bold text-orange">{{ $summary['links'] }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Total Clicks</p>
                <p class="text-2xl font-bold">{{ $summary['clicks'] }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Total Conversions</p>
                <p class="text-2xl font-bold">{{ $summary['conversions'] }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                <p class="text-sm text-gray-600 dark:text-text-secondary mb-2">Points Awarded</p>
                <p class="text-2xl font-bold text-orange">{{ number_format($summary['points_awarded']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Top Referral Links</h2>
                <div class="space-y-3">
                    @forelse($topLinks as $link)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <p class="font-semibold">{{ $link->product->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Owner: {{ $link->user->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary">Clicks: {{ $link->clicks }} | Conversions: {{ $link->conversions }}</p>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No referral links found.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Top Referrers</h2>
                <div class="space-y-3">
                    @forelse($topReferrers as $referrer)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $referrer->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-text-secondary">Points Balance: {{ number_format($referrer->points_balance) }}</p>
                            </div>
                            <span class="text-sm font-semibold text-orange">{{ $referrer->referral_links_sum_conversions ?? 0 }} conversions</span>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-text-secondary">No referrer activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
