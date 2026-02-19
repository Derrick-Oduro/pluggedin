@section('page-title', 'Category Management')

<x-superadmin-layout>
    <div>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Category Management</h1>
            <a href="{{ route('superadmin.categories.create') }}" class="bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add New Category
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-500 rounded-lg p-4 mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-dark-secondary rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="text-left p-4">ID</th>
                        <th class="text-left p-4">Name</th>
                        <th class="text-left p-4">Slug</th>
                        <th class="text-left p-4">Products</th>
                        <th class="text-left p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="border-b border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="p-4">{{ $category->id }}</td>
                            <td class="p-4 font-semibold">{{ $category->name }}</td>
                            <td class="p-4 text-gray-600 dark:text-text-secondary">{{ $category->slug }}</td>
                            <td class="p-4">
                                <span class="text-orange">{{ $category->products_count }} products</span>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('superadmin.categories.edit', $category) }}" class="text-blue-500 hover:text-blue-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('superadmin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="5" class="p-8 text-center text-gray-600 dark:text-text-secondary">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    </div>
</x-superadmin-layout>
