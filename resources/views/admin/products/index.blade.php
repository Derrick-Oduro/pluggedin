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
                        <a href="{{ route('admin.products.index') }}" class="backend-tab backend-tab-active">Products</a>
                        <a href="{{ route('admin.orders.index') }}" class="backend-tab">Orders</a>
                        <a href="{{ route('admin.bookings.index') }}" class="backend-tab">Bookings</a>
                    </nav>
                </div>
            </aside>

            <div class="xl:col-span-9 space-y-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Manage Products</h1>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.pending') }}" class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Pending Reviews</a>
                        <a href="{{ route('admin.products.create') }}" class="bg-orange hover:bg-orange-light text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Add Product</a>
                    </div>
                </div>

                @if($products->count())
                    <div class="backend-table-wrap">
                        <table class="backend-table">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
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
                                <span class="text-xs px-3 py-1 rounded-full
                                    @if($product->status === 'approved') bg-green-500/20 text-green-500
                                    @elseif($product->status === 'pending') bg-yellow-500/20 text-yellow-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="backend-td text-orange font-semibold">${{ number_format($product->price, 2) }}</td>
                            <td class="backend-td">{{ $product->stock_quantity }}</td>
                            <td class="backend-td">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-500 hover:text-blue-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400">
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
            </div>
        </div>
    </div>
</x-app-layout>
