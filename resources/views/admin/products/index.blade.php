<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Manage Products</h1>
            <a href="{{ route('admin.products.create') }}" class="bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add New Product
            </a>
        </div>

        <div class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="text-left p-4">ID</th>
                        <th class="text-left p-4">Name</th>
                        <th class="text-left p-4">Category</th>
                        <th class="text-left p-4">Price</th>
                        <th class="text-left p-4">Stock</th>
                        <th class="text-left p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="p-4">{{ $product->id }}</td>
                            <td class="p-4">{{ $product->name }}</td>
                            <td class="p-4">{{ $product->category->name }}</td>
                            <td class="p-4 text-orange font-semibold">${{ number_format($product->price, 2) }}</td>
                            <td class="p-4">{{ $product->stock_quantity }}</td>
                            <td class="p-4">
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
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-600 dark:text-text-secondary">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
