<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-12 text-center">Upgrade Services</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            @foreach($services as $service)
                <div class="bg-white dark:bg-dark-secondary rounded-lg p-8">
                    <h3 class="text-2xl font-bold mb-4">{{ $service->name }}</h3>
                    <p class="text-gray-600 dark:text-text-secondary mb-6 leading-relaxed">{{ $service->description }}</p>
                    <p class="text-4xl font-bold text-orange mb-6">${{ number_format($service->price, 2) }}</p>

                    @auth
                        <a href="{{ route('bookings.create', $service) }}" class="block text-center bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                            Book Now
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block text-center bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                            Login to Book
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>

        <!-- What to Expect -->
        <div class="bg-white dark:bg-dark-secondary rounded-lg p-12">
            <h2 class="text-3xl font-bold mb-8 text-center">What to Expect</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-4xl mb-4">📅</div>
                    <h3 class="text-xl font-semibold mb-2">1. Book Online</h3>
                    <p class="text-gray-600 dark:text-text-secondary">Choose your service and preferred date</p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-4">✅</div>
                    <h3 class="text-xl font-semibold mb-2">2. Confirmation</h3>
                    <p class="text-gray-600 dark:text-text-secondary">We'll review and confirm your booking</p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-4">🔧</div>
                    <h3 class="text-xl font-semibold mb-2">3. Professional Service</h3>
                    <p class="text-gray-600 dark:text-text-secondary">Expert installation and testing</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
