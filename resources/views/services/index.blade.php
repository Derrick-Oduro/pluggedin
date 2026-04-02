<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/45 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="absolute inset-0 pointer-events-none opacity-70">
            <div class="absolute -top-20 -left-12 h-72 w-72 rounded-full bg-orange/15 blur-3xl"></div>
            <div class="absolute -bottom-20 right-0 h-80 w-80 rounded-full bg-orange/15 blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-12">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-4">Upgrade Services</p>
                <h1 class="text-5xl sm:text-6xl font-bold leading-tight mb-5">Expert Service, Tangible Results</h1>
                <p class="text-lg text-gray-600 dark:text-text-secondary">From diagnostics to installation, every service is built to improve speed, reliability, and the lifespan of your device.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-16">
                @foreach($services as $service)
                    <div class="group bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200/70 dark:border-gray-800 hover:-translate-y-1 hover:shadow-2xl hover:shadow-orange/10 transition duration-300">
                        <div class="flex items-start justify-between gap-3 mb-5">
                            <div class="w-11 h-11 rounded-xl bg-orange/15 text-orange flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5.1 14.4a2.25 2.25 0 0 0-.659 1.591v3.905a1.5 1.5 0 0 0 2.56 1.06l3.683-3.683a2.25 2.25 0 0 1 1.591-.659h5.714a2.25 2.25 0 0 0 1.591-.659l1.316-1.316a2.25 2.25 0 0 0 0-3.182L12.93 3.763a2.25 2.25 0 0 0-3.18 0Z" />
                                </svg>
                            </div>
                            <p class="text-3xl font-bold text-orange">${{ number_format($service->price, 2) }}</p>
                        </div>

                        <h3 class="text-2xl font-bold mb-3 group-hover:text-orange transition">{{ $service->name }}</h3>
                        <p class="text-gray-600 dark:text-text-secondary mb-7 leading-relaxed">{{ $service->description }}</p>

                        @auth
                            <a href="{{ route('bookings.create', $service) }}" class="inline-flex items-center justify-center gap-2 w-full bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                                Book Now
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                                Login to Book
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                                </svg>
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>

            <!-- What to Expect -->
            <div class="bg-white/95 dark:bg-dark-secondary rounded-2xl p-8 sm:p-12 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Process</p>
                    <h2 class="text-3xl sm:text-4xl font-bold mb-4">What to Expect</h2>
                    <p class="text-gray-600 dark:text-text-secondary">A simple, transparent flow from booking to completed service.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center rounded-xl bg-orange-50/70 dark:bg-dark p-6 border border-orange/20">
                        <div class="mx-auto w-12 h-12 rounded-xl bg-orange text-white flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25m10.5-2.25v2.25M3 18.75V8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75Zm0-7.5h18" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">1. Book Online</h3>
                        <p class="text-gray-600 dark:text-text-secondary">Choose your service and preferred date.</p>
                    </div>

                    <div class="text-center rounded-xl bg-orange-50/70 dark:bg-dark p-6 border border-orange/20">
                        <div class="mx-auto w-12 h-12 rounded-xl bg-orange text-white flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">2. Confirmation</h3>
                        <p class="text-gray-600 dark:text-text-secondary">We review details and confirm your booking.</p>
                    </div>

                    <div class="text-center rounded-xl bg-orange-50/70 dark:bg-dark p-6 border border-orange/20">
                        <div class="mx-auto w-12 h-12 rounded-xl bg-orange text-white flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5 7.5 8.25m0 0L4.5 11.25m3-3 5.25 5.25m0 0 3-3m-3 3-3.75 3.75M7.5 8.25 12 3.75a2.121 2.121 0 0 1 3 3l-4.5 4.5m0 0L9 12.75" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">3. Professional Service</h3>
                        <p class="text-gray-600 dark:text-text-secondary">Expert installation, testing, and handoff.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
