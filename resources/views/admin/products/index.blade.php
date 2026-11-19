<x-admin-layout>
                <x-page-header badge="Admin" title="Products" subtitle="Every catalog and community listing in the marketplace.">
                    <x-slot:actions>
                        <a href="{{ route('admin.products.pending') }}" class="btn-secondary">Moderation queue</a>
                        <a href="{{ route('admin.products.create') }}" class="btn-primary">Add product</a>
                    </x-slot:actions>
                </x-page-header>

                @if($products->count())
                    <div class="backend-table-wrap">
                        <table class="backend-table">
                            <thead class="border-b border-stone-200">
                                <tr>
                                    <th class="backend-th">ID</th>
                                    <th class="backend-th">Name</th>
                                    <th class="backend-th">Category</th>
                                    <th class="backend-th">Status</th>
                                    <th class="backend-th">Price</th>
                                    <th class="backend-th">Stock</th>
                                    <th class="backend-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                        <tr class="backend-row">
                            <td class="backend-td">{{ $product->id }}</td>
                            <td class="backend-td">{{ $product->name }}</td>
                            <td class="backend-td">{{ $product->category->name }}</td>
                            <td class="backend-td">
                                <x-status-badge :status="$product->status" />
                            </td>
                            <td class="backend-td text-brand font-semibold">GH₵{{ number_format($product->price, 2) }}</td>
                            <td class="backend-td">{{ $product->stock_quantity }}</td>
                            <td class="backend-td">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-brand hover:text-brand-deep">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-600 hover:text-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-empty-state
                        title="No products found"
                        message="Create your first product to start selling in the catalog."
                        action-label="Add Product"
                        :action-href="route('admin.products.create')"
                    />
                @endif

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
</x-admin-layout>
