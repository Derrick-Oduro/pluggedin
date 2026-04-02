@section('page-title', 'Edit Category')

<x-superadmin-layout>
    <div class="max-w-2xl space-y-4">
        <h1 class="text-2xl font-bold">Edit Category</h1>

        <form action="{{ route('superadmin.categories.update', $category) }}" method="POST" class="backend-card rounded-xl space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Category Name *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                           class="backend-field">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 dark:text-text-secondary text-sm mt-1">Current slug: {{ $category->slug }}</p>
                </div>

                <div class="sticky bottom-3 z-10 flex gap-4 rounded-lg border border-gray-200 bg-white/95 p-3 backdrop-blur dark:border-gray-700 dark:bg-dark-secondary/95">
                    <button type="submit" class="backend-btn-primary">
                        Update Category
                    </button>
                    <a href="{{ route('superadmin.categories.index') }}" class="backend-btn-muted">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-superadmin-layout>
