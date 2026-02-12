@section('page-title', 'Edit User')

<x-superadmin-layout>
    <div class="max-w-3xl">
        <h1 class="text-4xl font-bold mb-8">Edit User</h1>

        <form action="{{ route('superadmin.users.update', $user) }}" method="POST" class="bg-dark-secondary rounded-lg p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Password (leave blank to keep current)</label>
                    <input type="password" name="password"
                           class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Role *</label>
                    <select name="role" required
                            class="w-full bg-dark border border-gray-700 rounded px-4 py-2 focus:border-orange focus:ring-orange">
                        <option value="user" {{ old('role', $user->roles->first()?->name ?? 'user') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->roles->first()?->name ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super-admin" {{ old('role', $user->roles->first()?->name ?? 'user') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-orange hover:bg-orange-light text-white px-8 py-3 rounded-lg font-semibold transition">
                        Update User
                    </button>
                    <a href="{{ route('superadmin.users.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-superadmin-layout>
