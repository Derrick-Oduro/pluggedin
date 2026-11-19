<x-admin-layout>
                @php
                    $statusLabels = [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'ready_for_pickup' => 'Ready for pickup',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ];
                    $statusTimes = $order->statusHistories
                        ->groupBy('to_status')
                        ->map(fn ($events) => $events->first()->created_at);
                    $isCancelled = $order->status === 'cancelled';
                @endphp

                <x-page-header badge="Admin" :title="'Order #'.$order->id" :subtitle="'Placed '.$order->created_at->format('M d, Y · H:i')" :back-href="route('admin.orders.index')" back-label="Back to orders">
                    <x-slot:actions>
                        <x-status-badge :status="$order->status" />
                    </x-slot:actions>
                </x-page-header>

                <div class="backend-card rounded-xl p-5">
                    <h2 class="text-lg font-extrabold text-stone-950 mb-3">Order Tracking</h2>
                    @if($isCancelled)
                        <div class="mb-3 rounded-lg border border-red-500/50 bg-red-500/10 px-3 py-2 text-sm text-red-500">
                            This order was cancelled before completion.
                        </div>
                    @endif
                    <div class="space-y-2">
                        @foreach($statusLabels as $statusKey => $statusLabel)
                            @php $timestamp = $statusTimes->get($statusKey); @endphp
                            <div class="rounded-lg border border-stone-200 px-3 py-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-semibold">{{ $statusLabel }}</p>
                                    <span class="text-xs text-stone-500">{{ $timestamp ? $timestamp->format('M d, Y H:i') : 'Not reached yet' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-stone-500">Items</p>
                        <p class="text-xl font-semibold mt-1">{{ $order->items->sum('quantity') }}</p>
                    </div>
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-stone-500">Subtotal</p>
                        <p class="text-xl font-semibold mt-1">GH₵{{ number_format($order->subtotal_price ?: $order->total_price, 2) }}</p>
                    </div>
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-stone-500">Discount</p>
                        <p class="text-xl font-semibold mt-1 text-green-600">- GH₵{{ number_format($order->discount_amount ?? 0, 2) }}</p>
                    </div>
                    <div class="backend-card rounded-xl">
                        <p class="text-xs text-stone-500">Total</p>
                        <p class="text-xl font-semibold mt-1 text-brand">GH₵{{ number_format($order->total_price, 2) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="backend-card rounded-xl p-5">
                        <h2 class="text-lg font-extrabold text-stone-950 mb-3">Customer Information</h2>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-stone-500">Name</dt>
                                <dd class="font-medium text-right">{{ $order->user->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-stone-500">Email</dt>
                                <dd class="font-medium text-right break-all">{{ $order->user->email ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-stone-500">Phone</dt>
                                <dd class="font-medium text-right">{{ $order->delivery_phone ?: 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="backend-card rounded-xl p-5">
                        <h2 class="text-lg font-extrabold text-stone-950 mb-3">Delivery Information</h2>
                        <div class="text-sm space-y-2">
                            <p class="text-stone-500">Address</p>
                            <p class="font-medium whitespace-pre-line">{{ $order->delivery_address ?: 'N/A' }}</p>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="mt-4 pt-3 border-t border-stone-200 text-sm">
                                <p class="text-stone-500">Applied discount</p>
                                <p class="font-medium text-green-600 mt-1">
                                    {{ $order->applied_discount_code ?: 'Auto campaign' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="backend-card rounded-xl p-5">
                    <h2 class="text-lg font-extrabold text-stone-950 mb-3">Update Order Status</h2>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-3">
                        @csrf
                        @method('PATCH')

                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1.5">Status</label>
                            <select name="status" class="backend-field">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="ready_for_pickup" {{ $order->status === 'ready_for_pickup' ? 'selected' : '' }}>Ready for pickup</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1.5">Tracking number</label>
                            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" class="backend-field" maxlength="160">
                        </div>

                        <button type="submit" class="backend-btn-primary">Update Status</button>
                    </form>
                </div>

                <div class="backend-card rounded-xl p-5">
                    <h2 class="text-lg font-extrabold text-stone-950 mb-3">Order Items</h2>
                    <div class="backend-table-wrap">
                        <div class="overflow-x-auto">
                            <table class="backend-table">
                                <thead class="border-b border-stone-200 bg-stone-50">
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
                                                        <div class="h-12 w-12 rounded bg-stone-200"></div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium">{{ $item->product->name ?? 'Product removed' }}</p>
                                                        <p class="text-xs text-stone-500">Item ID: {{ $item->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="backend-td">{{ $item->quantity }}</td>
                                            <td class="backend-td">GH₵{{ number_format($item->price, 2) }}</td>
                                            <td class="backend-td text-brand font-semibold">GH₵{{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-stone-200 space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-stone-600">Subtotal</span>
                            <span class="font-medium">GH₵{{ number_format($order->subtotal_price ?: $order->total_price, 2) }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount ({{ $order->applied_discount_code ?: 'Auto campaign' }})</span>
                                <span>- GH₵{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-stone-200">
                            <span class="text-lg font-semibold">Final Total</span>
                            <span class="text-2xl font-bold text-brand">GH₵{{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
</x-admin-layout>
