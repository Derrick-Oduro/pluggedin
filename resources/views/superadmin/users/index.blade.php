@section('page-title', 'User Management')

<x-superadmin-layout>
    <div>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">User Management</h1>
            <a href="{{ route('superadmin.users.create') }}" class="bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add New User
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
                        <th class="text-left p-4">Email</th>
                        <th class="text-left p-4">Role</th>
                        <th class="text-left p-4">Joined</th>
                        <th class="text-left p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="p-4">{{ $user->id }}</td>
                            <td class="p-4">{{ $user->name }}</td>
                            <td class="p-4 text-gray-600 dark:text-text-secondary">{{ $user->email }}</td>
                            <td class="p-4">
                                @if($user->hasRole('super-admin'))
                                    <span class="text-xs px-3 py-1 rounded-full bg-red-500/20 text-red-500">Super Admin</span>
                                @elseif($user->hasRole('admin'))
                                    <span class="text-xs px-3 py-1 rounded-full bg-orange/20 text-orange">Admin</span>
                                @else
                                    <span class="text-xs px-3 py-1 rounded-full bg-blue-500/20 text-blue-500">User</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-600 dark:text-text-secondary">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="p-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('superadmin.users.edit', $user) }}" class="text-blue-500 hover:text-blue-400">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-600 dark:text-text-secondary">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</x-superadmin-layout>
