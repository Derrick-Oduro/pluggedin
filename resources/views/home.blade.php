<x-app-layout>
    <!-- Hero Section -->
    <div class="relative overflow-hidden min-h-[78vh] flex items-center px-4 sm:px-6 lg:px-8"
         x-data="heroCarousel()"
         x-init="init()"
         @mouseenter="pause()"
         @mouseleave="resume()">
        <!-- Carousel Background -->
        <div class="absolute inset-0 z-0">
            <template x-for="(slide, index) in slides" :key="slide.src">
                <img :src="slide.src"
                     :alt="slide.alt"
                     class="absolute inset-0 w-full h-full object-cover transition-all duration-1000 ease-out"
                     :class="currentIndex === index ? 'opacity-100 scale-100' : 'opacity-0 scale-110'"
                     :loading="index === 0 ? 'eager' : 'lazy'">
            </template>
            <div class="absolute inset-0 bg-gradient-to-b from-black/65 via-black/45 to-black/70"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(249,115,22,.25),transparent_40%),radial-gradient(circle_at_10%_80%,rgba(255,255,255,.12),transparent_45%)]"></div>
            <div class="hero-glow absolute -top-24 -right-24 h-72 w-72 rounded-full bg-orange/25 blur-3xl"></div>
            <div class="hero-glow absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-orange/20 blur-3xl" style="animation-delay: 1.3s;"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto w-full">
            <div class="max-w-3xl text-white">
                <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/25 bg-white/10 backdrop-blur text-sm font-medium tracking-wide mb-6">
                    <span class="h-2 w-2 rounded-full bg-orange"></span>
                    Trusted Local Upgrade Experts
                </p>

                <h1 class="text-5xl sm:text-6xl md:text-7xl font-bold leading-tight drop-shadow-lg mb-5">
                    Upgrade. Don't Replace.
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 drop-shadow mb-9 max-w-2xl">
                    Practical upgrades, careful installs, and support that keeps your devices running longer.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold shadow-xl shadow-black/20 transition">
                        View Upgrades
                    </a>
                    <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center bg-white/90 dark:bg-dark border-2 border-orange text-orange dark:text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-light hover:text-white transition">
                        Book Service
                    </a>
                </div>

                <div class="mt-8 flex flex-wrap gap-3 text-sm sm:text-base">
                    <div class="rounded-full bg-white/10 border border-white/20 backdrop-blur px-4 py-2">Fast diagnostics</div>
                    <div class="rounded-full bg-white/10 border border-white/20 backdrop-blur px-4 py-2">Quality components</div>
                    <div class="rounded-full bg-white/10 border border-white/20 backdrop-blur px-4 py-2">Warranty-backed service</div>
                </div>
            </div>

            @if($activeCampaign)
                <div class="absolute right-0 top-0 hidden md:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-orange/85 text-white text-sm font-semibold shadow-lg">
                    <span>{{ $activeCampaign->discount_percent }}% Off</span>
                    <span class="opacity-90">{{ $activeCampaign->name }}</span>
                    @if($activeCampaign->code)
                        <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">Code: {{ $activeCampaign->code }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Controls -->
        <button type="button"
                x-show="slides.length > 1"
                @click="prev()"
                class="absolute left-4 sm:left-8 top-1/2 -translate-y-1/2 z-20 h-11 w-11 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur transition"
                aria-label="Previous slide">
            &#10094;
        </button>
        <button type="button"
                x-show="slides.length > 1"
                @click="next()"
                class="absolute right-4 sm:right-8 top-1/2 -translate-y-1/2 z-20 h-11 w-11 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur transition"
                aria-label="Next slide">
            &#10095;
        </button>

        <div x-show="slides.length > 1" class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
            <template x-for="(slide, index) in slides" :key="slide.alt">
                <button type="button"
                        class="h-2.5 rounded-full transition-all"
                        :class="currentIndex === index ? 'w-8 bg-orange' : 'w-2.5 bg-white/70 hover:bg-white'"
                        @click="goTo(index)"
                        :aria-label="`Go to slide ${index + 1}`"></button>
            </template>
        </div>
    </div>

    <style>
        @keyframes hero-float {
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-14px);
            }
        }

        .hero-glow {
            animation: hero-float 7s ease-in-out infinite;
        }
    </style>

    <script>
        function heroCarousel() {
            return {
                slides: @json($heroSlides),
                currentIndex: 0,
                timer: null,
                interval: 7000,
                init() {
                    if (!this.slides.length) {
                        return;
                    }
                    this.resume();
                },
                pause() {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                },
                resume() {
                    this.pause();
                    this.timer = setInterval(() => this.next(), this.interval);
                },
                next() {
                    if (!this.slides.length) {
                        return;
                    }
                    this.currentIndex = (this.currentIndex + 1) % this.slides.length;
                },
                prev() {
                    if (!this.slides.length) {
                        return;
                    }
                    this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
                },
                goTo(index) {
                    if (!this.slides.length) {
                        return;
                    }
                    this.currentIndex = index;
                    this.resume();
                }
            };
        }
    </script>

    <!-- Featured Products -->
    <section class="relative py-20 bg-gradient-to-b from-white via-orange-50/30 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Featured Picks</p>
                    <h2 class="text-4xl sm:text-5xl font-bold">Performance Upgrades Worth It</h2>
                    <p class="text-gray-600 dark:text-text-secondary mt-4 max-w-2xl">Handpicked products and services focused on speed, reliability, and long-term value.</p>
                </div>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-orange hover:text-orange-light font-semibold">
                    Browse all products
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProducts as $product)
                    <a href="{{ route('products.show', $product) }}" class="group bg-white/90 dark:bg-dark-secondary rounded-2xl overflow-hidden border border-gray-200/70 dark:border-gray-800 hover:-translate-y-1 hover:shadow-2xl hover:shadow-orange/10 transition duration-300">
                        <div class="relative overflow-hidden">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-56 object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-56 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4 bg-black/65 text-white text-xs px-3 py-1 rounded-full backdrop-blur">
                                {{ $product->category->name }}
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 group-hover:text-orange transition">{{ $product->name }}</h3>
                            <p class="text-gray-600 dark:text-text-secondary mb-5 line-clamp-2">{{ $product->description }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-2xl font-bold text-orange">GH₵{{ number_format($product->price, 2) }}</p>
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-orange transition">
                                    Details
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Why PluggedIn Section (connected steps) -->
    <section class="relative py-20 bg-white dark:bg-dark-secondary overflow-hidden">
        <div class="absolute inset-0 opacity-60 pointer-events-none">
            <div class="absolute top-0 left-1/3 h-48 w-48 rounded-full bg-orange/10 blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 h-56 w-56 rounded-full bg-orange/15 blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Why Choose Us</p>
                <h2 class="text-4xl sm:text-5xl font-bold mb-4">Built Around Long-Term Value</h2>
                <p class="text-gray-600 dark:text-text-secondary">Practical recommendations, careful installs, and support that keeps devices running smoothly.</p>
            </div>

            <div class="relative mt-12">
                <!-- horizontal connector for md+ -->
                <div class="hidden md:block absolute left-0 right-0 top-10">
                    <div class="mx-auto w-full max-w-4xl h-0.5 bg-gray-200 dark:bg-gray-800"></div>
                </div>

                <div class="relative max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                    <div class="flex flex-col items-center text-center md:items-center md:text-center">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-orange text-white flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v6m0 8v6m8-8h-6M4 12H2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mt-4">Assessment</h3>
                        <p class="text-gray-600 dark:text-text-secondary mt-2">Fast, no-nonsense diagnostics to identify upgrade opportunities and cost-effective fixes.</p>
                    </div>

                    <div class="flex flex-col items-center text-center md:items-center md:text-center">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-orange text-white flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M6 11h12M10 15h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mt-4">Install & Test</h3>
                        <p class="text-gray-600 dark:text-text-secondary mt-2">Careful installs, thorough testing, and cleanup so the upgrade works reliably from day one.</p>
                    </div>

                    <div class="flex flex-col items-center text-center md:items-center md:text-center">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-orange text-white flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2 2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mt-4">Support</h3>
                        <p class="text-gray-600 dark:text-text-secondary mt-2">Warranty-backed service, follow-up guidance, and options for future upgrades.</p>
                    </div>
                </div>

                <!-- vertical connectors for small screens -->
                <div class="md:hidden mt-8 max-w-md mx-auto">
                    <ul class="flex flex-col">
                        <li class="flex items-start gap-4 py-4">
                            <div class="flex items-center"><span class="w-4 h-4 rounded-full bg-orange mt-1"></span></div>
                            <div>
                                <p class="font-semibold">Assessment</p>
                                <p class="text-gray-600 dark:text-text-secondary text-sm mt-1">Fast, no-nonsense diagnostics to identify upgrade opportunities and cost-effective fixes.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 py-4">
                            <div class="flex items-center"><span class="w-4 h-4 rounded-full bg-orange mt-1"></span></div>
                            <div>
                                <p class="font-semibold">Install & Test</p>
                                <p class="text-gray-600 dark:text-text-secondary text-sm mt-1">Careful installs, thorough testing, and cleanup so the upgrade works reliably from day one.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 py-4">
                            <div class="flex items-center"><span class="w-4 h-4 rounded-full bg-orange mt-1"></span></div>
                            <div>
                                <p class="font-semibold">Support</p>
                                <p class="text-gray-600 dark:text-text-secondary text-sm mt-1">Warranty-backed service, follow-up guidance, and options for future upgrades.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-20 bg-gradient-to-b from-white to-orange-50/40 dark:from-dark dark:to-dark-secondary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Service Menu</p>
                    <h2 class="text-4xl sm:text-5xl font-bold">Professional Service, Start to Finish</h2>
                </div>
                <p class="text-gray-600 dark:text-text-secondary max-w-xl">Transparent pricing, careful handling, and guidance that fits your device goals.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="group bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:border-orange/40 hover:shadow-xl hover:shadow-orange/10 transition duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-11 h-11 rounded-xl bg-orange/15 text-orange flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9m-9 6h9m-9 6h9M4.5 6h.008v.008H4.5V6Zm0 6h.008v.008H4.5V12Zm0 6h.008v.008H4.5V18Z" />
                                </svg>
                            </div>
                            <p class="text-3xl font-bold text-orange">GH₵{{ number_format($service->price, 2) }}</p>
                        </div>

                        <h3 class="text-2xl font-bold mb-3 group-hover:text-orange transition">{{ $service->name }}</h3>
                        <p class="text-gray-600 dark:text-text-secondary mb-7">{{ $service->description }}</p>

                        <a href="{{ route('services.show', $service) }}" class="inline-flex items-center gap-2 bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                            Learn More
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto rounded-3xl bg-gradient-to-r from-orange via-orange-light to-amber-400 text-white px-8 sm:px-12 py-12 sm:py-14 shadow-2xl shadow-orange/20 relative overflow-hidden">
            <div class="absolute -top-12 -right-8 w-52 h-52 bg-white/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-16 -left-10 w-56 h-56 bg-black/15 rounded-full blur-2xl"></div>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/90 mb-3">Ready to Start?</p>
                    <h2 class="text-4xl sm:text-5xl font-bold mb-4">Check Your Upgrade Options</h2>
                    <p class="text-lg text-white/90">Not sure what your device needs? We’ll map out the best path based on your budget and goals.</p>
                </div>

                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 bg-white text-orange px-8 py-4 rounded-lg font-semibold hover:bg-dark hover:text-white transition">
                    Contact Us
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
