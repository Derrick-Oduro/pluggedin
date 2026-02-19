<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-gray-50 dark:bg-dark-secondary py-32 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-6xl md:text-7xl font-bold mb-6 text-gray-900 dark:text-white">
                Upgrade. Don't Replace.
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 dark:text-text-secondary mb-8 max-w-3xl mx-auto">
                Extend your device's life with professional upgrade services and quality components
            </p>
            <div class="flex gap-4 justify-center">
                <a href="{{ route('products.index') }}" class="bg-orange hover:bg-orange-light text-white px-8 py-4 rounded-lg font-semibold transition">
                    View Upgrades
                </a>
                <a href="{{ route('services.index') }}" class="bg-white dark:bg-dark border-2 border-orange text-orange dark:text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-light transition">
                    Book Service
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-4xl font-bold mb-12">Featured Products</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProducts as $product)
                <a href="{{ route('products.show', $product) }}" class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden hover:ring-2 hover:ring-orange transition border border-gray-200 dark:border-gray-800">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500 dark:text-text-secondary">No Image</span>
                        </div>
                    @endif

                    <div class="p-6">
                        <p class="text-orange text-sm mb-2">{{ $product->category->name }}</p>
                        <h3 class="text-xl font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 dark:text-text-secondary mb-4 line-clamp-2">{{ $product->description }}</p>
                        <p class="text-2xl font-bold text-orange">${{ number_format($product->price, 2) }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="text-orange hover:text-orange-light font-semibold">
                View All Products →
            </a>
        </div>
    </div>

    <!-- Why PluggedIn Section -->
    <div class="bg-gray-50 dark:bg-dark-secondary py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold mb-12 text-center">Why PluggedIn?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-4xl mb-4">🔧</div>
                    <h3 class="text-xl font-semibold mb-2">Upgrade-Ready Systems</h3>
                    <p class="text-gray-600 dark:text-text-secondary">Expert advice on compatible upgrades for your specific device</p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-4">💚</div>
                    <h3 class="text-xl font-semibold mb-2">Honest Advice</h3>
                    <p class="text-gray-600 dark:text-text-secondary">We'll tell you if an upgrade makes sense, or if it's time for something new</p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-4">🛠️</div>
                    <h3 class="text-xl font-semibold mb-2">Long-Term Support</h3>
                    <p class="text-gray-600 dark:text-text-secondary">Professional installation and ongoing support for all upgrades</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-4xl font-bold mb-12">Our Services</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($services as $service)
                <div class="bg-white dark:bg-dark-secondary rounded-lg p-8 border border-gray-200 dark:border-gray-800">
                    <h3 class="text-2xl font-bold mb-4">{{ $service->name }}</h3>
                    <p class="text-gray-600 dark:text-text-secondary mb-6">{{ $service->description }}</p>
                    <p class="text-3xl font-bold text-orange mb-6">${{ number_format($service->price, 2) }}</p>
                    <a href="{{ route('services.show', $service) }}" class="block text-center bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                        Learn More
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-orange py-16">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-4xl font-bold mb-4 text-white">Check Your Upgrade Options</h2>
            <p class="text-xl mb-8 text-white/90">Not sure what your device needs? Let us help you find the perfect upgrade.</p>
            <a href="{{ route('contact') }}" class="inline-block bg-dark text-white px-8 py-4 rounded-lg font-semibold hover:bg-dark-secondary transition">
                Contact Us
            </a>
        </div>
    </div>
</x-app-layout>
