<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@pesisirbarat.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Run seeders
        $this->call([
            PenulisUserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            SettingSeeder::class,
            DefaultSettingsSeeder::class,
            ArticleSeeder::class,
            PendingArticlesSeeder::class,
        ]);
    }
}
