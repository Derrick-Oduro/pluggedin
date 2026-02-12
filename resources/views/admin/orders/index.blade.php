<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">Manage Orders</h1>

        <div class="bg-dark-secondary rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-gray-700">
                    <tr>
                        <th class="text-left p-4">Order ID</th>
                        <th class="text-left p-4">Customer</th>
                        <th class="text-left p-4">Total</th>
                        <th class="text-left p-4">Status</th>
                        <th class="text-left p-4">Date</th>
                        <th class="text-left p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-b border-gray-700 hover:bg-gray-800">
                            <td class="p-4 font-semibold">#{{ $order->id }}</td>
                            <td class="p-4">{{ $order->user->name }}</td>
                            <td class="p-4 text-orange font-semibold">${{ number_format($order->total_price, 2) }}</td>
                            <td class="p-4">
                                <span class="text-xs px-3 py-1 rounded-full
                                    @if($order->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @elseif($order->status === 'confirmed') bg-blue-500/20 text-blue-500
                                    @elseif($order->status === 'completed') bg-green-500/20 text-green-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-text-secondary">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="p-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-orange hover:text-orange-light">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-text-secondary">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
