<x-app-layout>
    <section class="flyer-dark torn-bottom z-10 -mb-[22px] pb-[22px]">
        {{-- Flyer decorations --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 hidden xl:block">
            <span class="absolute right-[6%] top-[12%] h-16 w-16 rounded-full border-[3px] border-brand"></span>
            <span class="absolute right-[3%] top-[8%] text-4xl font-light text-brand">+</span>
            <span class="absolute left-[2%] top-[8%] hidden h-10 w-10 rounded-full border-[3px] border-brand 2xl:block"></span>
            <span class="absolute bottom-[10%] left-[50%] text-3xl font-light text-brand">+</span>
            <span class="absolute right-[2.5%] top-[30%] flex flex-col text-xl font-black leading-[0.7] text-brand">@for($i = 0; $i < 6; $i++)<span>&#8964;</span>@endfor</span>
            <span class="absolute bottom-[14%] left-[1.5%] hidden grid-cols-2 gap-2 2xl:grid">@for($i = 0; $i < 10; $i++)<span class="h-2 w-2 rounded-full bg-brand"></span>@endfor</span>
        </div>

        <div class="content-wrap relative grid min-h-[600px] items-center gap-12 py-14 lg:grid-cols-[1.05fr_.95fr] lg:py-20">
            <div class="relative z-10 max-w-2xl">
                <p class="text-lg font-black tracking-[-0.1em] text-white" aria-hidden="true">&raquo;&raquo;&raquo;&raquo;&raquo;&raquo;&raquo;&raquo;&raquo;</p>
                <h1 class="display-heading mt-3 text-6xl leading-[0.95] text-white sm:text-7xl xl:text-8xl">Buy<span class="text-brand">&middot;</span>Swap<span class="hidden text-brand sm:inline">&middot;</span><br class="sm:hidden">Upgrade</h1>
                <p class="tagline mt-4 text-4xl sm:text-5xl">Your One-Stop Student Hub</p>
                <p class="mt-6 max-w-xl text-lg leading-8 text-white/75">Laptops, phones, parts and accessories, plus repairs and trade-ins. We've got all your accessories and more, delivered free on campus.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('products.index') }}" class="btn-primary h-12 px-6 hover:bg-white hover:text-stone-950">Shop the marketplace</a>
                    <a href="{{ auth()->check() ? route('products.upload.create') : route('register') }}" class="inline-flex h-12 items-center justify-center rounded-xl border-2 border-white/30 px-6 text-sm font-extrabold text-white transition hover:-translate-y-0.5 hover:border-brand hover:text-brand">Sell or swap tech</a>
                </div>
                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4 text-white">
                    <p class="text-xl font-semibold uppercase tracking-[0.04em]"><span class="orange-underline">Free</span> campus delivery</p>
                    @if($site['contact_phone'] ?? null)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $site['contact_phone']) }}" class="flex items-center gap-2 text-lg font-bold hover:text-brand">
                            <svg class="h-5 w-5 text-brand" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1L6.6 10.8Z"/></svg>
                            {{ $site['contact_phone'] }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-xl">
                {{-- Orange striped circle behind the device shot, as on the flyer --}}
                <div aria-hidden="true" class="flyer-stripes absolute left-1/2 top-1/2 aspect-square w-[105%] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-95"></div>
                <div class="relative overflow-hidden rounded-[2rem] border-4 border-stone-950 bg-stone-900 shadow-[0_30px_70px_-30px_rgba(0,0,0,0.9)]">
                    @if($heroSlide)
                        <img src="{{ $heroSlide->image_url }}" alt="{{ $heroSlide->alt_text ?: $heroSlide->title ?: 'PluggedIn campus tech' }}" class="h-[340px] w-full object-cover sm:h-[440px]">
                        @if($heroSlide->caption)
                            <div class="absolute inset-x-0 bottom-0 bg-stone-950/80 px-5 py-4 text-white">
                                <p class="text-sm font-semibold">{{ $heroSlide->caption }}</p>
                            </div>
                        @endif
                    @else
                        <div class="relative h-[340px] overflow-hidden bg-stone-900 sm:h-[440px]"><div class="absolute left-[13%] top-[14%] h-[60%] w-[74%] rounded-[1.4rem] border-[10px] border-stone-700 bg-stone-800 p-3 shadow-2xl"><div class="h-full rounded-lg bg-stone-950 p-5"><div class="flex justify-between"><span class="h-2.5 w-2.5 rounded-full bg-brand"></span><span class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-white/60">System refresh</span></div><p class="display-heading mt-10 text-4xl text-white">Built to<br><span class="text-brand">last.</span></p><div class="mt-6 grid grid-cols-2 gap-2"><span class="rounded-lg bg-brand p-2 text-[10px] font-bold text-stone-950">FASTER BOOT</span><span class="rounded-lg bg-brand p-2 text-[10px] font-bold text-stone-950">MORE STORAGE</span></div></div></div><div class="absolute bottom-[11%] left-[7%] h-4 w-[86%] rounded-full bg-stone-700 shadow-xl"></div></div>
                    @endif
                </div>
            </div>
        </div>
        @if($activeCampaign)<div class="absolute right-8 top-8 z-20 hidden rounded-full bg-brand px-4 py-2 text-sm font-extrabold text-stone-950 shadow-lg xl:block">{{ $activeCampaign->discount_percent }}% off · {{ $activeCampaign->name }}{{ $activeCampaign->code ? ' · '.$activeCampaign->code : '' }}</div>@endif
    </section>

    <section class="bg-brand pt-[22px] text-stone-950"><div class="content-wrap grid gap-5 py-7 text-center sm:grid-cols-3"><div><p class="display-heading text-2xl">Buy &amp; discover</p><p class="mt-1 text-sm font-semibold text-stone-900/80">Laptops, phones, parts and daily tech.</p></div><div class="border-stone-950/20 sm:border-x"><p class="display-heading text-2xl">Sell &amp; swap</p><p class="mt-1 text-sm font-semibold text-stone-900/80">Turn unused tech into value.</p></div><div><p class="display-heading text-2xl">Repair &amp; upgrade</p><p class="mt-1 text-sm font-semibold text-stone-900/80">Keep the devices you depend on going.</p></div></div></section>

    <section class="page-shell"><div class="content-wrap relative z-10"><div class="mb-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-end"><div class="max-w-xl"><span class="brand-badge">Marketplace picks</span><h2 class="section-title mt-4">Tech worth taking to campus.</h2><p class="section-subtitle mt-3">Find laptops, parts, and accessories that help you work, learn, and create.</p></div><a href="{{ route('products.index') }}" class="text-sm font-extrabold text-brand-deep hover:text-brand-deep">Browse the marketplace →</a></div><div class="grid gap-5 md:grid-cols-3">@forelse($featuredProducts->take(3) as $product)<a href="{{ route('products.show', $product) }}" class="group overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:-translate-y-1 hover:shadow-[0_22px_45px_-30px_rgba(41,30,23,0.45)]"><div class="h-48 overflow-hidden bg-stone-100">@if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@else<div class="flex h-full items-center justify-center text-sm font-bold text-stone-400">{{ $product->category->name }}</div>@endif</div><div class="p-5"><p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-deep">{{ $product->category->name }}</p><h3 class="mt-2 text-lg font-extrabold text-stone-950">{{ $product->name }}</h3><div class="mt-5 flex items-center justify-between"><span class="text-lg font-extrabold text-stone-950">GH₵{{ number_format($product->price, 2) }}</span><span class="text-sm font-bold text-brand-deep">View →</span></div></div></a>@empty<div class="rounded-2xl border border-dashed border-stone-300 p-8 text-stone-500 md:col-span-3">Products will appear here once they are available.</div>@endforelse</div></div></section>

    <section class="bg-stone-950 py-16 text-white sm:py-20"><div class="content-wrap"><div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr]"><div><span class="rounded-full border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-white/70">The PluggedIn approach</span><h2 class="display-heading mt-5 text-4xl leading-tight sm:text-5xl">A better route than replacing everything.</h2><p class="mt-5 max-w-md text-base leading-7 text-white/70">We assess what is holding your device back, recommend only what helps, then install and test it carefully.</p></div><div class="grid gap-4 sm:grid-cols-3"><div class="rounded-2xl border border-white/10 bg-white/5 p-5"><span class="text-sm font-bold text-brand">01</span><h3 class="mt-8 text-lg font-extrabold">Assess</h3><p class="mt-2 text-sm leading-6 text-white/65">Find the best practical next step for your device and budget.</p></div><div class="rounded-2xl border border-white/10 bg-white/5 p-5"><span class="text-sm font-bold text-brand">02</span><h3 class="mt-8 text-lg font-extrabold">Improve</h3><p class="mt-2 text-sm leading-6 text-white/65">Install quality components with careful, clean workmanship.</p></div><div class="rounded-2xl border border-white/10 bg-white/5 p-5"><span class="text-sm font-bold text-brand">03</span><h3 class="mt-8 text-lg font-extrabold">Support</h3><p class="mt-2 text-sm leading-6 text-white/65">Leave with a tested device and guidance for what comes next.</p></div></div></div></div></section>

    <section class="page-shell bg-white"><div class="content-wrap relative z-10"><div class="mb-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-end"><div class="max-w-xl"><span class="brand-badge">Services</span><h2 class="section-title mt-4">Bring us the device. We’ll handle the rest.</h2></div><a href="{{ route('services.index') }}" class="text-sm font-extrabold text-brand-deep hover:text-brand-deep">View all services →</a></div><div class="grid gap-5 md:grid-cols-3">@foreach($services->take(3) as $service)<article class="rounded-2xl border border-stone-200 p-6"><div class="flex items-start justify-between gap-4"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-tint text-sm font-extrabold text-brand-deep">0{{ $loop->iteration }}</span><span class="text-lg font-extrabold text-brand-deep">GH₵{{ number_format($service->price, 2) }}</span></div><h3 class="mt-8 text-xl font-extrabold tracking-[-0.03em] text-stone-950">{{ $service->name }}</h3><p class="mt-3 min-h-16 text-sm leading-6 text-stone-600">{{ $service->description }}</p><a href="{{ route('services.show', $service) }}" class="mt-6 inline-block text-sm font-extrabold text-stone-900 hover:text-brand-deep">Learn more →</a></article>@endforeach</div></div></section>

    <section class="px-4 py-12 sm:px-6 sm:py-16"><div class="mx-auto max-w-6xl overflow-hidden rounded-[2rem] bg-stone-950 px-8 py-12 text-white shadow-[0_26px_50px_-32px_rgba(27,22,19,0.7)] sm:px-12"><div class="flex flex-col justify-between gap-7 md:flex-row md:items-center"><div class="max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.16em] text-brand">Not sure where to start?</p><h2 class="display-heading mt-3 text-3xl sm:text-4xl">Tell us what you want your device to do better.</h2><p class="mt-3 text-white/70">We’ll help you choose a sensible upgrade path.</p></div><a href="{{ route('contact') }}" class="inline-flex h-12 shrink-0 items-center justify-center rounded-xl bg-brand px-6 text-sm font-extrabold text-stone-950 transition hover:bg-white">Talk to us →</a></div></div></section>
</x-app-layout>
