@section('page-title', 'Create New User')

<x-superadmin-layout>
    <div class="max-w-2xl space-y-4">
        <x-page-header badge="Super admin" title="Create user" :back-href="route('superadmin.users.index')" back-label="Back to team members" />

        <form action="{{ route('superadmin.users.store') }}" method="POST" class="backend-card rounded-xl space-y-4">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="backend-field">
                    @error('name')
                        <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="backend-field">
                    @error('email')
                        <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Password *</label>
                    <input type="password" name="password" required
                           class="backend-field">
                    @error('password')
                        <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="backend-field">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-stone-800">Role *</label>
                    <select name="role" required
                            class="backend-field">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super-admin" {{ old('role') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sticky bottom-3 z-10 flex gap-4 rounded-lg border border-stone-200 bg-white/95 p-3 backdrop-blur">
                    <button type="submit" class="backend-btn-primary">
                        Create User
                    </button>
                    <a href="{{ route('superadmin.users.index') }}" class="backend-btn-muted">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-superadmin-layout>
