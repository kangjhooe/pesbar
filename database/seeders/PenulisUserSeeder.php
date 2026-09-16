<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class PenulisUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penulisUsers = [
            [
                'name' => 'Ahmad Fauzi',
                'username' => 'ahmad-fauzi',
                'email' => 'ahmad.fauzi@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'siti-nurhaliza',
                'email' => 'siti.nurhaliza@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi-santoso',
                'email' => 'budi.santoso@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Maya Sari',
                'username' => 'maya-sari',
                'email' => 'maya.sari@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rizki Pratama',
                'username' => 'rizki-pratama',
                'email' => 'rizki.pratama@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ]
        ];

        foreach ($penulisUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Create user profile if not exists
            if (!$user->profile) {
                UserProfile::create([
                    'user_id' => $user->id,
                    'bio' => 'Penulis di Pesisir Barat Hub',
                    'avatar' => null,
                    'website' => null,
                    'location' => 'Kabupaten Pesisir Barat, Lampung',
                    'social_links' => [
                        'facebook' => null,
                        'twitter' => null,
                        'instagram' => null,
                        'linkedin' => null,
                    ],
                ]);
            }

            $this->command->info("Created penulis user: {$user->name}");
        }

        $this->command->info('Successfully created 5 penulis users!');
    }
}