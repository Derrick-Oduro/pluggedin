@section('page-title', 'User Management')

<x-superadmin-layout>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">User Management</h1>
            <a href="{{ route('superadmin.users.create') }}" class="backend-btn-primary">
                + Add New User
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

        <div class="backend-table-wrap rounded-xl">
            <table class="backend-table">
                <thead class="border-b border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-dark">
                    <tr>
                        <th class="backend-th">ID</th>
                        <th class="backend-th">Name</th>
                        <th class="backend-th">Email</th>
                        <th class="backend-th">Role</th>
                        <th class="backend-th">Joined</th>
                        <th class="backend-th">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="backend-row">
                            <td class="backend-td">{{ $user->id }}</td>
                            <td class="backend-td font-medium">{{ $user->name }}</td>
                            <td class="backend-td text-gray-600 dark:text-text-secondary">{{ $user->email }}</td>
                            <td class="backend-td">
                                @if($user->hasRole('super-admin'))
                                    <span class="text-xs px-3 py-1 rounded-full bg-red-500/20 text-red-500">Super Admin</span>
                                @elseif($user->hasRole('admin'))
                                    <span class="text-xs px-3 py-1 rounded-full bg-orange/20 text-orange">Admin</span>
                                @else
                                    <span class="text-xs px-3 py-1 rounded-full bg-blue-500/20 text-blue-500">User</span>
                                @endif
                            </td>
                            <td class="backend-td text-gray-600 dark:text-text-secondary">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="backend-td">
                                <div class="flex gap-2">
                                    <a href="{{ route('superadmin.users.edit', $user) }}" class="text-sm text-blue-500 hover:text-blue-400">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-500 hover:text-red-400">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-sm text-gray-600 dark:text-text-secondary">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-1">
            {{ $users->links() }}
        </div>
    </div>
</x-superadmin-layout>
