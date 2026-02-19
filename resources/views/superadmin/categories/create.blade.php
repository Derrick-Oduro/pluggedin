@section('page-title', 'Create New Category')

<x-superadmin-layout>
    <div class="max-w-3xl">
        <h1 class="text-4xl font-bold mb-8">Create New Category</h1>

        <form action="{{ route('superadmin.categories.store') }}" method="POST" class="bg-white dark:bg-dark-secondary rounded-lg p-8">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Category Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g., Storage, Memory, Accessories"
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 dark:text-text-secondary text-sm mt-1">The slug will be automatically generated from the name</p>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Create Category
                    </button>
                    <a href="{{ route('superadmin.categories.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-superadmin-layout>
