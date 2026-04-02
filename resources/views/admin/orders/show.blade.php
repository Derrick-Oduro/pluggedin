<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3">
                <div class="backend-sidebar sticky top-24">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="backend-tab">Dashboard</a>
                        <a href="{{ route('admin.products.pending') }}" class="backend-tab">Moderation Queue</a>
                        <a href="{{ route('admin.reviews.index') }}" class="backend-tab">Review Moderation</a>
                        <a href="{{ route('admin.products.index') }}" class="backend-tab">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="backend-tab backend-tab-active">Orders</a>
                        <a href="{{ route('admin.bookings.index') }}" class="backend-tab">Bookings</a>
                        <a href="{{ route('admin.referrals.index') }}" class="backend-tab">Referrals</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
                @php
                    $statusLabels = [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ];
                    $statusTimes = $order->statusHistories
                        ->groupBy('to_status')
                        ->map(fn ($events) => $events->first()->created_at);
                    $isCancelled = $order->status === 'cancelled';
                @endphp

                <div class="backend-card rounded-xl p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary">Order Details</p>
                            <h1 class="text-2xl font-bold mt-1">Order #{{ $order->id }}</h1>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-3 py-1 rounded-full
                                @if($order->status === 'pending') bg-yellow-500/20 text-yellow-500
                                @elseif($order->status === 'confirmed') bg-blue-500/20 text-blue-500
                                @elseif($order->status === 'completed') bg-green-500/20 text-green-500
                                @else bg-red-500/20 text-red-500
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            <a href="{{ route('admin.orders.index') }}" class="backend-btn-muted">Back to Orders</a>
                        </div>
                    </div>
                </div>

                <div class="backend-card rounded-xl p-5">
                    <h2 class="text-lg font-semibold mb-3">Order Tracking</h2>
                    @if($isCancelled)
                        <div class="mb-3 rounded-lg border border-red-500/50 bg-red-500/10 px-3 py-2 text-sm text-red-500">
                            This order was cancelled before completion.
                        </div>
                    @endif
                    <div class="space-y-2">
                        @foreach($statusLabels as $statusKey => $statusLabel)
                            @php($timestamp = $statusTimes->get($statusKey))
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-semibold">{{ $statusLabel }}</p>
                                    <span class="text-xs text-gray-500 dark:text-text-secondary">{{ $timestamp ? $timestamp->format('M d, Y H:i') : 'Not reached yet' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Items</p>
                        <p class="text-xl font-semibold mt-1">{{ $order->items->sum('quantity') }}</p>
                    </div>
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Subtotal</p>
                        <p class="text-xl font-semibold mt-1">${{ number_format($order->subtotal_price ?: $order->total_price, 2) }}</p>
                    </div>
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Discount</p>
                        <p class="text-xl font-semibold mt-1 text-green-600 dark:text-green-400">- ${{ number_format($order->discount_amount ?? 0, 2) }}</p>
                    </div>
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-gray-500 dark:text-text-secondary">Total</p>
                        <p class="text-xl font-semibold mt-1 text-orange">${{ number_format($order->total_price, 2) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="backend-card rounded-xl p-5">
                        <h2 class="text-lg font-semibold mb-3">Customer Information</h2>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500 dark:text-text-secondary">Name</dt>
                                <dd class="font-medium text-right">{{ $order->user->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500 dark:text-text-secondary">Email</dt>
                                <dd class="font-medium text-right break-all">{{ $order->user->email ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500 dark:text-text-secondary">Phone</dt>
                                <dd class="font-medium text-right">{{ $order->delivery_phone ?: 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="backend-card rounded-xl p-5">
                        <h2 class="text-lg font-semibold mb-3">Delivery Information</h2>
                        <div class="text-sm space-y-2">
                            <p class="text-gray-500 dark:text-text-secondary">Address</p>
                            <p class="font-medium whitespace-pre-line">{{ $order->delivery_address ?: 'N/A' }}</p>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-sm">
                                <p class="text-gray-500 dark:text-text-secondary">Applied discount</p>
                                <p class="font-medium text-green-600 dark:text-green-400 mt-1">
                                    {{ $order->applied_discount_code ?: 'Auto campaign' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="backend-card rounded-xl p-5">
                    <h2 class="text-lg font-semibold mb-3">Update Order Status</h2>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-3">
                        @csrf
                        @method('PATCH')

                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1.5">Status</label>
                            <select name="status" class="backend-field">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="backend-btn-primary">Update Status</button>
                    </form>
                </div>

                <div class="backend-card rounded-xl p-5">
                    <h2 class="text-lg font-semibold mb-3">Order Items</h2>
                    <div class="backend-table-wrap">
                        <div class="overflow-x-auto">
                            <table class="backend-table">
                                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-dark">
                                    <tr>
                                        <th class="backend-th">Product</th>
                                        <th class="backend-th">Qty</th>
                                        <th class="backend-th">Unit</th>
                                        <th class="backend-th">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr class="backend-row">
                                            <td class="backend-td">
                                                <div class="flex items-center gap-3">
                                                    @if(optional($item->product)->image_path)
                                                        <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-12 w-12 rounded object-cover">
                                                    @else
                                                        <div class="h-12 w-12 rounded bg-gray-200 dark:bg-gray-800"></div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium">{{ $item->product->name ?? 'Product removed' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-text-secondary">Item ID: {{ $item->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="backend-td">{{ $item->quantity }}</td>
                                            <td class="backend-td">${{ number_format($item->price, 2) }}</td>
                                            <td class="backend-td text-orange font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-text-secondary">Subtotal</span>
                            <span class="font-medium">${{ number_format($order->subtotal_price ?: $order->total_price, 2) }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600 dark:text-green-400">
                                <span>Discount ({{ $order->applied_discount_code ?: 'Auto campaign' }})</span>
                                <span>- ${{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-lg font-semibold">Final Total</span>
                            <span class="text-2xl font-bold text-orange">${{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
