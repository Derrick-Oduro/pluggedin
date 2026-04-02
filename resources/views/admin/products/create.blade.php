<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <x-page-header
            title="Add New Product"
            subtitle="Create a new catalog product and publish it immediately."
            :breadcrumbs="[
                ['label' => 'Admin Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Products', 'href' => route('admin.products.index')],
                ['label' => 'Add Product'],
            ]"
        />

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="backend-card rounded-xl p-6 mt-4">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="backend-field">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5">Category *</label>
                    <select name="category_id" required
                        class="backend-field">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5">Description *</label>
                    <textarea name="description" rows="4" required
                              class="backend-field">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Price *</label>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                               class="backend-field">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                           <label class="block text-sm font-semibold mb-1.5">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity') }}" min="0" required
                               class="backend-field">
                        @error('stock_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                      <label class="block text-sm font-semibold mb-1.5">Product Image</label>
                    <input type="file" name="image" accept="image/*"
                          class="backend-field">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sticky bottom-3 z-10 flex gap-4 rounded-lg border border-gray-200 bg-white/95 p-3 backdrop-blur dark:border-gray-700 dark:bg-dark-secondary/95">
                    <button type="submit" class="backend-btn-primary">
                        Create Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="backend-btn-muted">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
