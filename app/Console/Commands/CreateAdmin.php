<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Create an admin (or super admin), or reset an existing account's password and role.
 *
 *   php artisan app:create-admin you@example.com --super
 *
 * The password is asked for interactively, so it never ends up in shell history or logs.
 */
class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {email : Email address for the account}
                            {--name= : Display name (defaults to the part before @)}
                            {--super : Give the account the super-admin role}';

    protected $description = 'Create an admin account, or reset an existing account\'s password and role';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $role = $this->option('super') ? 'super-admin' : 'admin';

        if (Validator::make(['email' => $email], ['email' => 'required|email'])->fails()) {
            $this->error('That is not a valid email address.');

            return self::FAILURE;
        }

        $password = $this->secret('Password (min 12 characters, input hidden)');
        $confirm = $this->secret('Confirm password');

        if ($password !== $confirm) {
            $this->error('Passwords do not match.');

            return self::FAILURE;
        }

        $check = Validator::make(['password' => $password], ['password' => ['required', Password::min(12)->letters()->numbers()]]);
        if ($check->fails()) {
            $this->error($check->errors()->first('password'));

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $existed = $user->exists;

        $user->forceFill([
            'name' => $user->name ?: ($this->option('name') ?: ucfirst(strstr($email, '@', true))),
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_super_admin' => $role === 'super-admin',
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $user->syncRoles([$role]);

        $this->info(($existed ? 'Updated' : 'Created')." {$role} account for {$email}.");

        return self::SUCCESS;
    }
}
