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
                'email' => 'ahmad.fauzi@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@pesisirbarat.id',
                'password' => bcrypt('password'),
                'role' => 'penulis',
                'verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rizki Pratama',
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
                    'bio' => 'Penulis di Portal Berita Pesisir Barat',
                    'avatar' => null,
                    'website' => null,
                    'location' => 'Kabupaten Pesisir Barat, Lampung',
                    'social_links' => json_encode([
                        'facebook' => null,
                        'twitter' => null,
                        'instagram' => null,
                        'linkedin' => null,
                    ]),
                ]);
            }

            $this->command->info("Created penulis user: {$user->name}");
        }

        $this->command->info('Successfully created 5 penulis users!');
    }
}