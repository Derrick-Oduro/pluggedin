<x-app-layout>
    <section class="page-shell"><div class="content-wrap relative z-10">
        <div class="flex items-end justify-between gap-4"><div><span class="brand-badge">Trade-ins</span><h1 class="section-title mt-4">My trade-in requests</h1><p class="section-subtitle mt-2">Track your device review, quote, and exchange progress.</p></div><a href="{{ route('trade-ins.create') }}" class="backend-btn-primary">Request a quote</a></div>
        <div class="mt-8 space-y-4">
            @forelse($requests as $tradeIn)
                <article class="backend-card"><div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><p class="text-lg font-extrabold text-stone-950">{{ $tradeIn->brand }} {{ $tradeIn->model }}</p><p class="mt-1 text-sm text-stone-600">{{ $tradeIn->device_type }} · {{ ucfirst($tradeIn->condition) }}@if($tradeIn->desiredProduct) · Toward {{ $tradeIn->desiredProduct->name }}@endif</p></div><div class="sm:text-right"><span class="rounded-full bg-brand-tint px-3 py-1 text-xs font-extrabold text-brand-deep">{{ str_replace('_', ' ', ucfirst($tradeIn->status)) }}</span>@if($tradeIn->quoted_value)<p class="mt-2 text-lg font-extrabold text-stone-950">Quote: GH₵{{ number_format($tradeIn->quoted_value, 2) }}</p>@endif</div></div>
                @if($tradeIn->status === 'quoted')
                    <div class="mt-5 rounded-xl border border-brand-soft bg-brand-tint p-4"><p class="text-sm font-bold text-stone-950">PluggedIn has offered GH₵{{ number_format($tradeIn->quoted_value, 2) }} for this device.</p>@if($tradeIn->admin_notes)<p class="mt-1 text-sm text-stone-600">{{ $tradeIn->admin_notes }}</p>@endif<div class="mt-4 flex flex-wrap gap-3"><form method="POST" action="{{ route('trade-ins.respond', $tradeIn) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="accepted"><button class="backend-btn-primary">Accept quote</button></form><form method="POST" action="{{ route('trade-ins.respond', $tradeIn) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="declined"><button class="btn-secondary">Decline</button></form></div></div>
                @elseif($tradeIn->status === 'accepted')
                    <p class="mt-4 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">Quote accepted. PluggedIn will contact you to arrange device inspection and the exchange.</p>
                @elseif($tradeIn->status === 'completed')
                    <p class="mt-4 rounded-xl bg-stone-100 p-4 text-sm text-stone-700">This trade-in has been completed.</p>
                @endif
            </article>
            @empty
                <div class="backend-card py-14 text-center"><p class="font-extrabold text-stone-950">No trade-in requests yet.</p><a href="{{ route('trade-ins.create') }}" class="mt-4 inline-flex text-sm font-bold text-brand-deep">Start a request →</a></div>
            @endforelse
        </div><div class="mt-8">{{ $requests->links() }}</div>
    </div></section>
</x-app-layout>
