<x-app-layout>
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold">Edit Upload</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-2">Update details and resubmit this product for moderation.</p>
            </div>
            <a href="{{ route('products.upload.index') }}" class="text-orange hover:text-orange-light font-semibold">Back to My Uploads</a>
        </div>

        <form action="{{ route('products.upload.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-dark-secondary rounded-2xl p-8 border border-gray-200 dark:border-gray-800 space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-semibold mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Description *</label>
                <textarea name="description" rows="5" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">{{ old('description', $product->description) }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Price *</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                    @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Stock *</label>
                    <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                    @error('stock_quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Category *</label>
                    <select name="category_id" required class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Replace Images (optional, up to 5)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 focus:border-orange focus:ring-orange">
                @error('images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="inline-flex items-center justify-center bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                Save and Resubmit
            </button>
        </form>
    </section>
</x-app-layout>
