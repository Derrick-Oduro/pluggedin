<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Orders</p>
                <h1 class="text-5xl sm:text-6xl font-bold">My Orders</h1>
            </div>

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white dark:bg-dark-secondary rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold mb-1">Order #{{ $order->id }}</h3>
                                    <p class="text-gray-600 dark:text-text-secondary text-sm">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full
                                    @if($order->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($order->status === 'confirmed') bg-blue-500/20 text-blue-500
                                    @elseif($order->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                                @foreach($order->items as $item)
                                    <div class="flex justify-between py-2 text-sm">
                                        <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                        <span class="text-orange font-medium">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold">Total: <span class="text-orange">${{ number_format($order->total_price, 2) }}</span></span>
                                <a href="{{ route('orders.show', $order) }}" class="text-orange hover:text-orange-light font-semibold">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 bg-white dark:bg-dark-secondary rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 text-center py-14 px-6">
                    <p class="text-gray-600 dark:text-text-secondary text-xl mb-6">You haven't placed any orders yet</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
