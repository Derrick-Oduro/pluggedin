<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold mb-8">Edit Product</h1>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-dark-secondary rounded-lg p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Category *</label>
                    <select name="category_id" required
                            class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Description *</label>
                    <textarea name="description" rows="4" required
                              class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Price *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                               class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                               class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        @error('stock_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if($product->image_path)
                    <div>
                        <label class="block text-sm font-semibold mb-2">Current Image</label>
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded">
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold mb-2">Update Product Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
