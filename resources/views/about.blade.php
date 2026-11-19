<x-app-layout>
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="About PluggedIn" title="We help devices last longer" subtitle="PluggedIn is built around one idea: thoughtful upgrades can outperform unnecessary replacement. We focus on practical improvements that boost speed, reliability, and everyday productivity." />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-14">
                <div class="lg:col-span-2 backend-card sm:p-8">
                    <h2 class="text-3xl font-bold mb-6">Our Philosophy</h2>
                    <p class="text-stone-600 text-lg leading-relaxed mb-6">
                        At PluggedIn, we believe upgrading is better than replacing. Your devices should not become e-waste just because they slow down. With the right parts and installation, you can extend lifespan and get years of added performance.
                    </p>
                    <p class="text-stone-600 text-lg leading-relaxed mb-7">
                        We pair honest advice with quality components and careful service, so you get improvements that are meaningful, reliable, and worth your investment.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl bg-brand-tint px-4 py-3 border border-brand/20 text-sm font-medium">Compatibility-first recommendations</div>
                        <div class="rounded-xl bg-brand-tint px-4 py-3 border border-brand/20 text-sm font-medium">Transparent pricing and options</div>
                        <div class="rounded-xl bg-brand-tint px-4 py-3 border border-brand/20 text-sm font-medium">Long-term performance support</div>
                    </div>
                </div>

                <div class="bg-gradient-to-b from-brand to-brand-deep text-white rounded-2xl p-8 shadow-lg shadow-brand/20 flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-3">Why It Matters</h3>
                        <p class="text-white/90 leading-relaxed">Smarter upgrades mean less waste, lower costs, and devices that keep up with your work longer.</p>
                    </div>

                    <a href="{{ route('services.index') }}" class="mt-8 inline-flex items-center gap-2 bg-white text-brand-deep px-5 py-3 rounded-lg font-semibold hover:bg-dark hover:text-white transition w-fit">
                        Explore Services
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="backend-card">
                    <div class="w-12 h-12 rounded-xl bg-brand/15 text-brand-deep flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25C6.615 2.25 2.25 6.615 2.25 12S6.615 21.75 12 21.75 21.75 17.385 21.75 12 17.385 2.25 12 2.25Zm0 0c2.583 2.306 4.125 5.625 4.125 9.75S14.583 19.444 12 21.75m0-19.5C9.417 4.556 7.875 7.875 7.875 12S9.417 19.444 12 21.75m-9.75-9.75h19.5" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Sustainable</h3>
                    <p class="text-stone-600">Reduce e-waste by extending the life of hardware you already own.</p>
                </div>

                <div class="backend-card">
                    <div class="w-12 h-12 rounded-xl bg-brand/15 text-brand-deep flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12c0-1.232.24-2.407.676-3.482a1.5 1.5 0 0 1 1.767-.89l2.295.573a1.5 1.5 0 0 1 1.068 1.926l-.466 1.397a8.966 8.966 0 0 0 4.886 4.886l1.397-.466a1.5 1.5 0 0 1 1.926 1.068l.573 2.295a1.5 1.5 0 0 1-.89 1.767A9.75 9.75 0 1 1 2.25 12Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Cost-Effective</h3>
                    <p class="text-stone-600">Save significantly compared to replacing your entire setup.</p>
                </div>

                <div class="backend-card">
                    <div class="w-12 h-12 rounded-xl bg-brand/15 text-brand-deep flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Performance</h3>
                    <p class="text-stone-600">Unlock smoother workflows and faster response with targeted upgrades.</p>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
