<x-admin-layout>
                <x-page-header badge="Admin" title="Orders" subtitle="Confirm, fulfil and track customer orders." />

                @if($orders->count())
                    <x-table-shell>
                        <table class="backend-table">
                            <thead class="border-b border-stone-200">
                                <tr>
                                    <th class="backend-th">Order ID</th>
                                    <th class="backend-th">Customer</th>
                                    <th class="backend-th">Total</th>
                                    <th class="backend-th">Status</th>
                                    <th class="backend-th">Date</th>
                                    <th class="backend-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                        <tr class="backend-row">
                            <td class="backend-td font-semibold">#{{ $order->id }}</td>
                            <td class="backend-td">{{ $order->user->name }}</td>
                            <td class="backend-td text-brand-deep font-semibold">GH₵{{ number_format($order->total_price, 2) }}</td>
                            <td class="backend-td">
                                <x-status-badge :status="$order->status" />
                                @if($order->awaitingCash())
                                    <span class="mt-1 block text-[11px] font-bold text-amber-700">Collect cash</span>
                                @elseif($order->isCashOnDelivery() && $order->paid_at)
                                    <span class="mt-1 block text-[11px] font-bold text-emerald-700">Paid on delivery</span>
                                @endif
                            </td>
                            <td class="backend-td text-stone-600">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="backend-td">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-deep hover:text-brand-deep">
                                    View Details
                                </a>
                            </td>
                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-table-shell>
                @else
                    <x-empty-state
                        title="No orders found"
                        message="New customer purchases will appear here once checkout is completed."
                    />
                @endif

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
</x-admin-layout>
