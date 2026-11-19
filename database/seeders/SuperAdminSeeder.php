<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo super admin for local development ONLY.
 *
 * Never runs in production: real admins are created with
 * `php artisan app:create-admin` and their own password.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Skipping demo super admin outside local/testing. Use php artisan app:create-admin.');

            return;
        }

        $user = User::firstOrCreate(
            ['email' => 'superadmin@pluggedin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_super_admin' => true,
            ]
        );

        $user->syncRoles(['super-admin']);
    }
}
