@section('page-title', 'Category Management')

<x-superadmin-layout>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">Category Management</h1>
            <a href="{{ route('superadmin.categories.create') }}" class="backend-btn-primary">
                + Add New Category
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg px-3 py-2 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-500 rounded-lg px-3 py-2 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($categories->count())
            <x-table-shell>
                <table class="backend-table">
                    <thead class="border-b border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-dark">
                        <tr>
                            <th class="backend-th">ID</th>
                            <th class="backend-th">Name</th>
                            <th class="backend-th">Slug</th>
                            <th class="backend-th">Products</th>
                            <th class="backend-th">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr class="backend-row">
                            <td class="backend-td">{{ $category->id }}</td>
                            <td class="backend-td font-medium">{{ $category->name }}</td>
                            <td class="backend-td text-gray-600 dark:text-text-secondary">{{ $category->slug }}</td>
                            <td class="backend-td">
                                <span class="text-orange">{{ $category->products_count }} products</span>
                            </td>
                            <td class="backend-td">
                                <div class="flex gap-2">
                                    <a href="{{ route('superadmin.categories.edit', $category) }}" class="text-sm text-blue-500 hover:text-blue-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('superadmin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-400">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table-shell>
        @else
            <x-empty-state
                title="No categories found"
                message="Add categories to organize products for better browsing and filtering."
                action-label="Add New Category"
                :action-href="route('superadmin.categories.create')"
            />
        @endif

        <div class="pt-1">
            {{ $categories->links() }}
        </div>
    </div>
</x-superadmin-layout>
