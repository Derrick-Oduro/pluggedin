<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="bg-white dark:bg-dark-secondary border border-gray-200 dark:border-gray-800 rounded-xl p-4 sticky top-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Dashboard</a>
                        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-lg text-sm bg-orange/15 text-orange">Orders</a>
                        <a href="{{ route('admin.reviews.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Review Moderation</a>
                        <a href="{{ route('admin.bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Bookings</a>
                        <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Products</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange hover:text-orange-light">Back to Orders</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Info -->
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-3">Customer Information</h2>
                <div class="space-y-2 text-gray-600 dark:text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Name:</span> {{ $order->user->name }}</p>
                    <p><span class="font-semibold text-text-primary">Email:</span> {{ $order->user->email }}</p>
                    <p><span class="font-semibold text-text-primary">Phone:</span> {{ $order->delivery_phone }}</p>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-3">Delivery Information</h2>
                <div class="space-y-2 text-gray-600 dark:text-text-secondary">
                    <p><span class="font-semibold text-text-primary">Address:</span></p>
                    <p>{{ $order->delivery_address }}</p>
                    <p><span class="font-semibold text-text-primary">Order Date:</span> {{ $order->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
            <h2 class="text-lg font-semibold mb-3">Update Order Status</h2>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex gap-4 items-end">
                @csrf
                @method('PATCH')

                <div class="flex-1">
                    <label class="block text-sm mb-2">Status</label>
                    <select name="status" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <button type="submit" class="bg-orange hover:bg-orange-light text-white px-6 py-2 rounded-lg font-semibold transition">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Order Items -->
        <div class="bg-white dark:bg-dark-secondary rounded-lg p-4 border border-gray-200 dark:border-gray-800">
            <h2 class="text-lg font-semibold mb-3">Order Items</h2>

            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-700 pb-4">
                        <div class="flex gap-4">
                            @if($item->product->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-800 rounded"></div>
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
                @endforeach
            </div>

            @if($order->discount_amount > 0)
                <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-700 text-sm space-y-1">
                    <div class="flex justify-between"><span>Subtotal</span><span>${{ number_format($order->subtotal_price ?: $order->total_price, 2) }}</span></div>
                    <div class="flex justify-between text-green-600 dark:text-green-400"><span>Discount ({{ $order->applied_discount_code ?: 'Auto campaign' }})</span><span>- ${{ number_format($order->discount_amount, 2) }}</span></div>
                </div>
            @endif

            <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-300 dark:border-gray-700">
                <span class="text-xl font-bold">Total:</span>
                <span class="text-2xl font-bold text-orange">${{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>
            </div>
        </div>
    </div>
</x-app-layout>
