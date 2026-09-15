<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@pesbar.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'verified' => true,
        ]);

        // Create editor user
        User::create([
            'name' => 'Editor',
            'username' => 'editor',
            'email' => 'editor@pesbar.com',
            'password' => Hash::make('password'),
            'role' => 'editor',
            'verified' => true,
        ]);

        // Create sample penulis
        User::create([
            'name' => 'Penulis Terverifikasi',
            'username' => 'penulis-terverifikasi',
            'email' => 'penulis@pesbar.com',
            'password' => Hash::make('password'),
            'role' => 'penulis',
            'verified' => true,
        ]);

        // Create sample penulis unverified
        User::create([
            'name' => 'Penulis Belum Terverifikasi',
            'username' => 'penulis-belum-terverifikasi',
            'email' => 'penulis2@pesbar.com',
            'password' => Hash::make('password'),
            'role' => 'penulis',
            'verified' => false,
        ]);

        // Create sample user
        User::create([
            'name' => 'User Biasa',
            'username' => 'user-biasa',
            'email' => 'user@pesbar.com',
            'password' => Hash::make('password'),
            'role' => 'penulis',
            'verified' => false,
        ]);
    }
}
