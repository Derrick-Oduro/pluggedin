<x-superadmin-layout title="Audit log">
    <div class="space-y-5">
        <x-page-header badge="Records" title="Audit log" subtitle="Every change admins make to users, listings, shops, trade-ins, categories and homepage content." />

        <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
            <div>
                <label for="area" class="mb-1 block text-xs font-bold text-stone-500">What changed</label>
                <select id="area" name="area" class="backend-field w-48" onchange="this.form.submit()">
                    <option value="">Everything</option>
                    @foreach($areas as $option)
                        <option value="{{ $option }}" @selected($area === $option)>{{ Str::headline($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="actor" class="mb-1 block text-xs font-bold text-stone-500">Who</label>
                <select id="actor" name="actor" class="backend-field w-48" onchange="this.form.submit()">
                    <option value="">Anyone</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" @selected($actorId === $actor->id)>{{ $actor->name }}</option>
                    @endforeach
                </select>
            </div>
            <noscript><button class="backend-btn-primary">Filter</button></noscript>
            @if($area !== '' || $actorId)
                <a href="{{ route('superadmin.audit-logs.index') }}" class="btn-link mb-3">Clear filters</a>
            @endif
            <p class="ml-auto mb-3 text-xs text-stone-500">{{ number_format($logs->total()) }} {{ Str::plural('entry', $logs->total()) }}</p>
        </form>

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
            @forelse($logs as $log)
                @php($details = \App\Support\AuditDescriber::details($log))
                <div class="flex flex-col gap-1 border-b border-stone-100 px-5 py-3.5 last:border-0 sm:flex-row sm:items-start sm:gap-6" x-data="{ open: false }">
                    <div class="w-36 shrink-0 text-xs text-stone-500">
                        <span class="block font-bold text-stone-700">{{ $log->created_at->format('M j, Y') }}</span>
                        {{ $log->created_at->format('H:i') }} · {{ $log->created_at->diffForHumans(short: true) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-stone-800"><b class="text-stone-950">{{ $log->user?->name ?? 'System' }}</b> {{ \App\Support\AuditDescriber::sentence($log) }}</p>
                        @if($details)
                            <button type="button" class="mt-1 text-xs font-bold text-stone-500 hover:text-brand-deep" x-on:click="open = !open" x-text="open ? 'Hide details' : 'Show details'">Show details</button>
                            <dl x-cloak x-show="open" class="mt-2 grid gap-x-4 gap-y-1 rounded-xl bg-stone-50 p-3 text-xs sm:grid-cols-[auto_1fr]">
                                @foreach($details as $key => $value)
                                    <dt class="font-bold text-stone-500">{{ $key }}</dt>
                                    <dd class="break-all text-stone-800">{{ $value }}</dd>
                                @endforeach
                                @if($log->ip_address ?? false)
                                    <dt class="font-bold text-stone-500">IP</dt><dd class="text-stone-800">{{ $log->ip_address }}</dd>
                                @endif
                            </dl>
                        @endif
                    </div>
                    <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-bold text-stone-600 sm:self-center">{{ Str::headline(\App\Support\AuditDescriber::area($log)) }}</span>
                </div>
            @empty
                <p class="px-5 py-12 text-center text-sm text-stone-500">No audit entries match.</p>
            @endforelse
        </div>

        <div>{{ $logs->links() }}</div>
    </div>
</x-superadmin-layout>
