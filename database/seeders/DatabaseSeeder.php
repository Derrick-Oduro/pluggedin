<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Reference data (roles, categories, services, slides) is safe to seed anywhere
     * and safe to re-run. Demo accounts are only created locally.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SuperAdminSeeder::class,
            MigrateExistingUsersToRolesSeeder::class,
            CategorySeeder::class,
            ServiceSeeder::class,
            HeroSlideSeeder::class,
        ]);

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        // Demo accounts with the password "password". firstOrCreate never resets an existing password.
        foreach ([
            ['admin@pluggedin.com', 'Admin', 'admin'],
            ['test@example.com', 'Test User', 'user'],
        ] as [$email, $name, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password'), 'role' => $role]
            );

            if ($user->roles->isEmpty()) {
                $user->assignRole($role);
            }
        }
    }
}
