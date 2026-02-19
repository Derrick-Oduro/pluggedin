@section('page-title', 'Create New User')

<x-superadmin-layout>
    <div class="max-w-3xl">
        <h1 class="text-4xl font-bold mb-8">Create New User</h1>

        <form action="{{ route('superadmin.users.store') }}" method="POST" class="bg-white dark:bg-dark-secondary rounded-lg p-8">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Password *</label>
                    <input type="password" name="password" required
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Role *</label>
                    <select name="role" required
                            class="w-full bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super-admin" {{ old('role') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Create User
                    </button>
                    <a href="{{ route('superadmin.users.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-superadmin-layout>
