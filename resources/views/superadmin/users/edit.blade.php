@section('page-title', 'Edit User')

<x-superadmin-layout>
    <div class="max-w-2xl space-y-4">
        <h1 class="text-2xl font-bold">Edit User</h1>

        <form action="{{ route('superadmin.users.update', $user) }}" method="POST" class="backend-card rounded-xl space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="backend-field">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="backend-field">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5">Password (leave blank to keep current)</label>
                    <input type="password" name="password"
                           class="backend-field">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="backend-field">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5">Role *</label>
                    <select name="role" required
                            class="backend-field">
                        <option value="user" {{ old('role', $user->roles->first()?->name ?? 'user') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->roles->first()?->name ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super-admin" {{ old('role', $user->roles->first()?->name ?? 'user') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="backend-btn-primary">
                        Update User
                    </button>
                    <a href="{{ route('superadmin.users.index') }}" class="backend-btn-muted">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-superadmin-layout>
