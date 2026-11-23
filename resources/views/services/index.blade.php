<x-app-layout>
    @php
        $bookHref = fn ($service) => auth()->check() ? route('bookings.create', $service) : route('login');
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10 space-y-12">
            <x-page-header badge="Repairs & upgrades" title="Fixed on campus. Checked before it’s handed back." subtitle="Cracked screen, tired battery or a laptop that’s just slow: book it in, drop it off or have us collect it, and we confirm the price before any work starts." />

            {{-- Not sure what's wrong? --}}
            @if($diagnosis)
                <div class="flyer-dark relative overflow-hidden rounded-3xl px-6 py-7 sm:px-10 sm:py-9">
                    <div class="flyer-stripes pointer-events-none absolute -right-16 -top-16 h-52 w-52 rounded-full opacity-80"></div>
                    <div class="pointer-events-none absolute -right-6 -top-6 h-32 w-32 rounded-full bg-stone-950"></div>
                    <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div class="max-w-xl">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-brand">Not sure what’s wrong?</p>
                            <h2 class="display-heading mt-2 text-3xl text-white sm:text-4xl">{{ $diagnosis->name }}</h2>
                            <p class="mt-2 text-sm leading-6 text-stone-300">{{ $diagnosis->description }}</p>
                            <p class="mt-3 text-sm font-bold text-white">{{ $diagnosis->priceLabel() }}@if($diagnosis->turnaround) <span class="font-normal text-stone-400">· {{ $diagnosis->turnaround }}</span>@endif</p>
                        </div>
                        <a href="{{ $bookHref($diagnosis) }}" class="btn-primary h-12 shrink-0 px-6 text-base hover:bg-white hover:text-stone-950">{{ auth()->check() ? 'Book a check-up' : 'Log in to book' }}</a>
                    </div>
                </div>
            @endif

            @foreach(['Repairs' => [$repairs, 'Something broken? Pick the closest match. If it’s not listed, book a check-up.'], 'Upgrades' => [$upgrades, 'Make a working device faster. Buy the part here or bring your own, and we fit it.']] as $heading => [$list, $lead])
                @continue($list->isEmpty())
                <section>
                    <div class="mb-5">
                        <h2 class="section-title">{{ $heading }}</h2>
                        <p class="section-subtitle mt-1 max-w-2xl">{{ $lead }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($list as $service)
                            <article class="flex flex-col rounded-2xl border border-stone-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-brand hover:shadow-[0_16px_35px_-24px_rgba(168,85,0,0.5)]">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-lg font-extrabold leading-snug text-stone-950">{{ $service->name }}</h3>
                                    <p class="shrink-0 text-right">
                                        @if($service->price_is_from)<span class="block text-[10px] font-bold uppercase tracking-wide text-stone-400">from</span>@endif
                                        <span class="text-xl font-extrabold text-brand-deep">{{ (float) $service->price > 0 ? 'GH₵'.number_format($service->price, 0) : 'Free' }}</span>
                                    </p>
                                </div>
                                <p class="mt-2 flex-1 text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                                <p class="mt-4 flex flex-wrap gap-2 text-[11px] font-bold">
                                    @if($service->turnaround)<span class="rounded-full bg-stone-100 px-2.5 py-1 text-stone-700">&#9201; {{ $service->turnaround }}</span>@endif
                                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-stone-700">{{ $service->devicesLabel() ?? 'Any device' }}</span>
                                </p>
                                <div class="mt-5 flex gap-2">
                                    <a href="{{ $bookHref($service) }}" class="btn-primary h-10 flex-1 px-3 text-sm">{{ auth()->check() ? 'Book' : 'Log in to book' }}</a>
                                    @if($service->partsCategory)
                                        <a href="{{ route('products.index', ['category' => $service->partsCategory->slug]) }}" class="btn-secondary h-10 px-3 text-sm">Shop parts</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach

            @if(! $diagnosis && $repairs->isEmpty() && $upgrades->isEmpty())
                <x-empty-state title="Bookings are closed for now" message="Our workshop menu is being updated. Check back soon or contact us." action-label="Contact us" :action-href="route('contact')" />
            @endif

            {{-- How it works --}}
            <section class="rounded-3xl border border-stone-200 bg-white p-6 sm:p-10">
                <h2 class="section-title text-center">How it works</h2>
                <ol class="mt-8 grid gap-6 md:grid-cols-4">
                    @foreach([
                        ['Book online', 'Pick a service and a day. Tell us the model and what’s wrong.'],
                        ['Hand it over', 'Drop it at the PluggedIn point on campus, or we come and collect it.'],
                        ['We confirm the price', 'We check the device and agree the final price with you before any work starts.'],
                        ['Get it back working', 'Fixed and tested, then ready to collect or brought back to you.'],
                    ] as $i => [$title, $text])
                        <li class="relative rounded-2xl bg-brand-tint/70 p-5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-stone-950 text-sm font-extrabold text-brand">{{ $i + 1 }}</span>
                            <h3 class="mt-3 font-extrabold text-stone-950">{{ $title }}</h3>
                            <p class="mt-1 text-sm leading-6 text-stone-600">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
    </section>
</x-app-layout>
