<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Order Detail</p>
                <h1 class="text-5xl sm:text-6xl font-bold">Order #{{ $order->id }}</h1>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Order Status</h2>
                    <span class="text-xs px-3 py-1 rounded-full
                        @if($order->status === 'pending') bg-yellow-500/20 text-yellow-500
                        @elseif($order->status === 'confirmed') bg-blue-500/20 text-blue-500
                        @elseif($order->status === 'completed') bg-green-500/20 text-green-500
                        @else bg-red-500/20 text-red-500
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-text-secondary">Order Date: {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-800">
                <h2 class="text-xl font-bold mb-4">Delivery Information</h2>
                <p class="text-gray-600 dark:text-text-secondary mb-2"><span class="font-semibold text-gray-900 dark:text-text-primary">Address:</span></p>
                <p class="text-gray-600 dark:text-text-secondary mb-4">{{ $order->delivery_address }}</p>
                <p class="text-gray-600 dark:text-text-secondary"><span class="font-semibold text-gray-900 dark:text-text-primary">Phone:</span> {{ $order->delivery_phone }}</p>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                <h2 class="text-xl font-bold mb-4">Order Items</h2>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div id="review-product-{{ $item->product->id }}" class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                            <div class="flex justify-between items-center">
                            <div class="flex gap-4">
                                @if($item->product->image_path)
                                    <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-800 rounded-lg"></div>
                                @endif

                                <div>
                                    <h3 class="font-semibold">{{ $item->product->name }}</h3>
                                    <p class="text-gray-600 dark:text-text-secondary text-sm">Quantity: {{ $item->quantity }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-gray-600 dark:text-text-secondary text-sm">${{ number_format($item->price, 2) }} each</p>
                                <p class="text-orange font-bold">${{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>
                            </div>

                            @if($canReviewOrder)
                                @php($itemReview = $userReviews->get($item->product_id))
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    @if($itemReview)
                                        <p class="font-semibold">Your review: {{ $itemReview->rating }}/5</p>
                                        @if($itemReview->comment)
                                            <p class="text-gray-600 dark:text-text-secondary mt-2">{{ $itemReview->comment }}</p>
                                        @endif
                                    @else
                                        <form action="{{ route('products.reviews.store', $item->product) }}" method="POST" class="space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-sm mb-2">Rate this item</label>
                                                <select name="rating" class="w-full sm:w-56 bg-white dark:bg-dark-secondary border border-gray-300 dark:border-gray-700 rounded px-3 py-2" required>
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
                                                <textarea name="comment" rows="2" class="w-full bg-white dark:bg-dark-secondary border border-gray-300 dark:border-gray-700 rounded px-3 py-2"></textarea>
                                            </div>
                                            <button type="submit" class="inline-flex bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg font-semibold transition">Submit Review</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-300 dark:border-gray-700">
                    <span class="text-2xl font-bold">Total:</span>
                    <span class="text-3xl font-bold text-orange">${{ number_format($order->total_price, 2) }}</span>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
