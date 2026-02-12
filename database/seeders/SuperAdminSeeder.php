<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@pluggedin.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@pluggedin.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_super_admin' => true,
            ]
        );
    }
}

