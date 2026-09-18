<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductionAdminSeeder extends Seeder
{
    /**
     * Seed production admin accounts with unique strong passwords.
     * Run only in production setup:
     * php artisan db:seed --class=ProductionAdminSeeder --force
     */
    public function run(): void
    {
        $admins = [
            [
                'email' => 'admin1@pesbar.id',
                'name' => 'Admin 1',
                'username' => 'admin1',
            ],
            [
                'email' => 'admin2@pesbar.id',
                'name' => 'Admin 2',
                'username' => 'admin2',
            ],
        ];

        $this->command?->info('Production admin accounts:');
        $this->command?->newLine();

        foreach ($admins as $admin) {
            $password = Str::password(24);

            $user = User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'username' => $admin['username'],
                    'password' => $password,
                    'role' => 'admin',
                    'verified' => true,
                    'email_verified_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->command?->warn("{$admin['email']} → {$password}");
            } else {
                $this->command?->line("{$admin['email']} already exists (password unchanged)");
            }
        }

        $this->command?->newLine();
        $this->command?->warn('Save these passwords now. They will not be shown again.');
    }
}
