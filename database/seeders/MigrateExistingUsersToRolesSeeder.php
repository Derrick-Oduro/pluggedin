<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MigrateExistingUsersToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Skip if user already has a role
            if ($user->roles->isNotEmpty()) {
                continue;
            }

            // Assign role based on old column values
            if (isset($user->is_super_admin) && $user->is_super_admin) {
                $user->assignRole('super-admin');
            } elseif (isset($user->role) && $user->role === 'admin') {
                $user->assignRole('admin');
            } else {
                $user->assignRole('user');
            }
        }

        $this->command->info('Migrated ' . $users->count() . ' users to Spatie roles.');
    }
}

