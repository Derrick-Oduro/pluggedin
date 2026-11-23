<x-superadmin-layout title="Dashboard">
    @php
        $queueCards = [
            ['Listings to approve', $queues['listing_approvals'], route('admin.products.pending'), 'New uploads from sellers'],
            ['Reports to review', $queues['reports'], route('superadmin.product-reports.index'), 'Flagged by members'],
            ['Trade-ins to quote', $queues['trade_ins_to_quote'], route('superadmin.trade-ins.index', ['tab' => 'to_quote']), 'Customers waiting for an offer'],
            ['Trade-ins to complete', $queues['trade_ins_to_complete'], route('superadmin.trade-ins.index', ['tab' => 'accepted']), 'Offer accepted, arrange the swap'],
            ['Shop applications', $queues['shops'], route('superadmin.shops.index'), 'Sellers asking for a shop'],
            ['Unanswered messages', $queues['messages'], route('superadmin.contact-messages.index'), 'From the contact form'],
            ['Cash to collect', $queues['cash_to_collect'], route('admin.orders.index'), 'Pay-on-delivery orders not yet paid'],
        ];
        $waiting = collect($queueCards)->filter(fn ($card) => $card[1] > 0);
        $clear = collect($queueCards)->filter(fn ($card) => $card[1] === 0);
        $money = fn ($v) => 'GH₵'.number_format($v, 2);
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="brand-badge">Super admin</p>
                <h1 class="display-heading mt-3 text-4xl text-stone-950">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Str::before(auth()->user()->name, ' ') }}.</h1>
                <p class="mt-1 text-sm text-stone-600">
                    @if($waiting->isNotEmpty())
                        {{ $waiting->sum(fn ($c) => $c[1]) }} {{ Str::plural('thing', $waiting->sum(fn ($c) => $c[1])) }} need you today.
                    @else
                        You're all caught up.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="rounded-xl border border-stone-200 bg-white px-3 py-2"><b>{{ number_format($totals['members']) }}</b> <span class="text-stone-500">members</span></span>
                <span class="rounded-xl border border-stone-200 bg-white px-3 py-2"><b>{{ number_format($totals['live_listings']) }}</b> <span class="text-stone-500">live listings</span></span>
                <span class="rounded-xl border border-stone-200 bg-white px-3 py-2"><b>{{ number_format($totals['open_orders']) }}</b> <span class="text-stone-500">open orders</span></span>
            </div>
        </div>

        {{-- Work queue --}}
        <section id="queue" class="scroll-mt-20">
            <h2 class="mb-3 text-xs font-extrabold uppercase tracking-[0.16em] text-stone-500">Waiting for you</h2>
            @if($waiting->isNotEmpty())
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($waiting as [$label, $count, $href, $hint])
                        <a href="{{ $href }}" class="group flex items-center justify-between gap-3 rounded-2xl border border-l-4 border-stone-200 border-l-brand bg-white px-4 py-4 transition hover:-translate-y-0.5 hover:shadow-[0_14px_30px_-22px_rgba(41,30,23,0.6)]">
                            <span class="min-w-0">
                                <span class="block font-extrabold text-stone-950">{{ $label }}</span>
                                <span class="block truncate text-xs text-stone-500">{{ $hint }}</span>
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="text-3xl font-extrabold tracking-tight text-stone-950">{{ $count }}</span>
                                <span class="text-brand-deep transition group-hover:translate-x-0.5">&rarr;</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">Nothing waiting. Every queue is clear.</div>
            @endif
            @if($clear->isNotEmpty() && $waiting->isNotEmpty())
                <p class="mt-3 text-xs text-stone-500">Nothing waiting in: {{ Str::lower($clear->pluck(0)->implode(', ')) }}.</p>
            @endif
        </section>

        {{-- This month --}}
        <section>
            <h2 class="mb-3 text-xs font-extrabold uppercase tracking-[0.16em] text-stone-500">{{ now()->format('F') }} so far <span class="font-semibold normal-case tracking-normal text-stone-400">vs the same days last month</span></h2>
            <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                @foreach($kpis as $kpi)
                    @php
                        $change = $kpi['before'] > 0 ? round(($kpi['now'] - $kpi['before']) / $kpi['before'] * 100) : null;
                    @endphp
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-4">
                        <p class="text-xs font-bold text-stone-500">{{ $kpi['label'] }}</p>
                        <p class="mt-1 text-2xl font-extrabold tracking-tight text-stone-950 sm:text-3xl">{{ ! empty($kpi['money']) ? $money($kpi['now']) : number_format($kpi['now']) }}</p>
                        <p class="mt-1 text-xs font-bold {{ $change === null ? 'text-stone-400' : ($change >= 0 ? 'text-emerald-700' : 'text-red-600') }}">
                            @if($change === null)
                                {{ $kpi['now'] > 0 ? 'New this month' : 'No activity yet' }}
                            @else
                                {{ $change >= 0 ? '▲' : '▼' }} {{ abs($change) }}% <span class="font-semibold text-stone-400">from {{ ! empty($kpi['money']) ? $money($kpi['before']) : number_format($kpi['before']) }}</span>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Charts --}}
        <div class="grid gap-4 xl:grid-cols-12">
            <section class="backend-card xl:col-span-8">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-stone-950">Orders and money in</h2>
                    <span class="text-xs text-stone-500">Last 6 months</span>
                </div>
                <div class="h-64"><canvas id="trendChart" aria-label="Orders and money in over the last six months" role="img"></canvas></div>
            </section>
            <section class="backend-card xl:col-span-4">
                <h2 class="mb-3 text-lg font-extrabold text-stone-950">Orders by status</h2>
                <div class="h-64"><canvas id="statusChart" aria-label="Orders by status" role="img"></canvas></div>
            </section>
        </div>

        <div class="grid gap-4 xl:grid-cols-12">
            {{-- Latest orders --}}
            <section class="backend-card p-0 xl:col-span-7">
                <div class="flex items-center justify-between px-5 pb-2 pt-5">
                    <h2 class="text-lg font-extrabold text-stone-950">Latest orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-extrabold text-brand-deep hover:text-stone-950">All orders &rarr;</a>
                </div>
                @forelse($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between gap-3 border-t border-stone-100 px-5 py-3 hover:bg-stone-50">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold text-stone-950">#{{ $order->id }} · {{ $order->user?->name ?? 'Deleted user' }}</span>
                            <span class="block text-xs text-stone-500">{{ $order->created_at->diffForHumans() }} · {{ $order->paymentLabel() }}</span>
                        </span>
                        <span class="flex shrink-0 items-center gap-3">
                            <span class="text-sm font-extrabold text-stone-950">{{ $money($order->total_price) }}</span>
                            <x-status-badge :status="$order->status" />
                        </span>
                    </a>
                @empty
                    <p class="border-t border-stone-100 px-5 py-6 text-sm text-stone-500">No orders yet.</p>
                @endforelse
            </section>

            {{-- Recent admin activity --}}
            <section class="backend-card p-0 xl:col-span-5">
                <div class="flex items-center justify-between px-5 pb-2 pt-5">
                    <h2 class="text-lg font-extrabold text-stone-950">Recent admin activity</h2>
                    <a href="{{ route('superadmin.audit-logs.index') }}" class="text-sm font-extrabold text-brand-deep hover:text-stone-950">Audit log &rarr;</a>
                </div>
                @forelse($recentAudits as $log)
                    <div class="border-t border-stone-100 px-5 py-3">
                        <p class="text-sm text-stone-800"><b>{{ $log->user?->name ?? 'System' }}</b> {{ \App\Support\AuditDescriber::sentence($log) }}</p>
                        <p class="text-xs text-stone-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="border-t border-stone-100 px-5 py-6 text-sm text-stone-500">No admin activity yet.</p>
                @endforelse
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const data = @json($chartData);
            if (!window.Chart) return;
            Chart.defaults.font.family = 'Manrope, sans-serif';
            Chart.defaults.color = '#78716c';

            new Chart(document.getElementById('trendChart'), {
                data: {
                    labels: data.labels,
                    datasets: [
                        { type: 'bar', label: 'Money in (GH₵)', data: data.money, backgroundColor: '#f7941d', borderRadius: 6, yAxisID: 'money' },
                        { type: 'line', label: 'Orders', data: data.orders, borderColor: '#1c1917', backgroundColor: '#1c1917', tension: 0.3, yAxisID: 'orders' },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
                    scales: {
                        money: { position: 'left', beginAtZero: true, grid: { color: 'rgba(120,113,108,.15)' } },
                        orders: { position: 'right', beginAtZero: true, grid: { display: false }, ticks: { precision: 0 } },
                        x: { grid: { display: false } },
                    },
                },
            });

            const palette = { pending: '#f59e0b', confirmed: '#0ea5e9', processing: '#6366f1', ready_for_pickup: '#14b8a6', shipped: '#3b82f6', completed: '#10b981', cancelled: '#ef4444', refunded: '#a8a29e' };
            const statuses = Object.keys(data.status);
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: statuses.map(s => s.replace(/_/g, ' ').replace(/^./, c => c.toUpperCase())),
                    datasets: [{ data: statuses.map(s => data.status[s]), backgroundColor: statuses.map(s => palette[s] || '#d6d3d1'), borderWidth: 0 }],
                },
                options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } } },
            });
        })();
    </script>
</x-superadmin-layout>
