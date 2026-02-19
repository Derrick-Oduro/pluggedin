<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-5xl font-bold mb-8 text-center">{{ $service->name }}</h1>

        <div class="bg-white dark:bg-dark-secondary rounded-lg p-12 mb-8">
            <p class="text-gray-600 dark:text-text-secondary text-lg leading-relaxed mb-8">
                {{ $service->description }}
            </p>

            <div class="flex justify-between items-center mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <span class="text-2xl font-bold">Service Price:</span>
                <span class="text-4xl font-bold text-orange">${{ number_format($service->price, 2) }}</span>
            </div>

            @auth
                <a href="{{ route('bookings.create', $service) }}" class="block text-center bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold transition text-xl">
                    Book This Service
                </a>
            @else
                <a href="{{ route('login') }}" class="block text-center bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold transition text-xl">
                    Login to Book
                </a>
            @endauth
        </div>

        <div class="text-center">
            <a href="{{ route('services.index') }}" class="text-orange hover:text-orange-light">
                ← Back to All Services
            </a>
        </div>
    </div>
</x-app-layout>
