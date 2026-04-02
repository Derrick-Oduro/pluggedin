<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
                @if(session('referral_link'))
                    <div class="mt-2 text-sm break-all">
                        Referral link: {{ session('referral_link') }}
                    </div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-500 rounded-lg p-4 mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Product Image -->
            <div>
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                @else
                    <div class="w-full h-96 bg-gray-100 dark:bg-dark-secondary rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-800">
                        <span class="text-gray-500 dark:text-text-secondary text-xl">No Image</span>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <p class="text-orange text-sm mb-2">{{ $product->category->name }}</p>
                <h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>
                <p class="text-3xl font-bold text-orange mb-6">${{ number_format($product->price, 2) }}</p>

                <div class="mb-6">
                    <p class="text-gray-600 dark:text-text-secondary mb-2">Stock: {{ $product->stock_quantity }} available</p>
                </div>

                <p class="text-gray-600 dark:text-text-secondary mb-8 leading-relaxed">{{ $product->description }}</p>

                @auth
                    @if($product->status === 'approved')
                        <form action="{{ route('products.referrals.store', $product) }}" method="POST" class="mb-6">
                            @csrf
                            <button type="submit" class="w-full border border-orange text-orange hover:bg-orange hover:text-white px-6 py-3 rounded-lg font-semibold transition">
                                {{ $referralLink ? 'Refresh Referral Link' : 'Generate Referral Link' }}
                            </button>
                        </form>

                        @if($referralLink)
                            <p class="text-sm text-gray-600 dark:text-text-secondary break-all mb-6">
                                Your referral link: {{ route('products.referrals.track', [$product, $referralLink->code]) }}
                            </p>
                        @endif
                    @endif
                @endauth

                @auth
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm mb-2">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                   class="w-full bg-white dark:bg-dark-secondary border border-gray-300 dark:border-gray-700 rounded px-4 py-2">
                        </div>

                        <button type="submit" class="w-full bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                            Add to Cart
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                        Login to Purchase
                    </a>
                @endauth
            </div>
        </div>

        <div class="mt-16 bg-white dark:bg-dark-secondary rounded-lg p-6 border border-gray-200 dark:border-gray-800">
            <h2 class="text-2xl font-bold mb-6">Reviews</h2>

            @auth
                @if($canReview)
                    <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="space-y-4 mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                        @csrf
                        <div>
                            <label class="block text-sm mb-2">Rating</label>
                            <select name="rating" class="w-full bg-white dark:bg-dark-secondary border border-gray-300 dark:border-gray-700 rounded px-4 py-2" required>
                                <option value="">Select rating</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-2">Comment (optional)</label>
                            <textarea name="comment" rows="3" class="w-full bg-white dark:bg-dark-secondary border border-gray-300 dark:border-gray-700 rounded px-4 py-2"></textarea>
                        </div>
                        <button type="submit" class="bg-orange hover:bg-orange-light text-white px-6 py-2 rounded-lg font-semibold transition">Submit Review</button>
                    </form>
                @elseif($userReview)
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-6">
                        <p class="font-semibold">Your review: {{ $userReview->rating }}/5</p>
                        @if($userReview->comment)
                            <p class="text-gray-600 dark:text-text-secondary mt-2">{{ $userReview->comment }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-600 dark:text-text-secondary mb-6">Only verified buyers can leave a review.</p>
                @endif
            @endauth

            <div class="space-y-5">
                @forelse($reviews as $review)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-semibold">{{ $review->user->name }}</p>
                            <p class="text-sm text-orange font-semibold">{{ $review->rating }}/5</p>
                        </div>
                        @if($review->comment)
                            <p class="text-gray-600 dark:text-text-secondary">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-text-secondary">No reviews yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold mb-6">Related Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('products.show', $related) }}" class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden hover:ring-2 hover:ring-orange transition border border-gray-200 dark:border-gray-800">
                            @if($related->image_path)
                                <img src="{{ asset('storage/' . $related->image_path) }}" alt="{{ $related->name }}" class="w-full h-32 object-cover">
                            @else
                                <div class="w-full h-32 bg-gray-200 dark:bg-gray-800"></div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold mb-2">{{ $related->name }}</h3>
                                <p class="text-orange font-bold">${{ number_format($related->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
