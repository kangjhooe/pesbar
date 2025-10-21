<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Politik',
                'slug' => 'politik',
                'description' => 'Berita seputar politik dan pemerintahan',
                'icon' => 'fas fa-landmark',
                'color' => '#e74c3c',
            ],
            [
                'name' => 'Ekonomi',
                'slug' => 'ekonomi',
                'description' => 'Berita ekonomi dan bisnis',
                'icon' => 'fas fa-chart-line',
                'color' => '#f39c12',
            ],
            [
                'name' => 'Sosial',
                'slug' => 'sosial',
                'description' => 'Berita sosial dan kemasyarakatan',
                'icon' => 'fas fa-users',
                'color' => '#27ae60',
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Berita olahraga',
                'icon' => 'fas fa-futbol',
                'color' => '#3498db',
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Berita teknologi dan digital',
                'icon' => 'fas fa-laptop',
                'color' => '#9b59b6',
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'description' => 'Berita kesehatan dan medis',
                'icon' => 'fas fa-heartbeat',
                'color' => '#e67e22',
            ],
            [
                'name' => 'Pendidikan',
                'slug' => 'pendidikan',
                'description' => 'Berita pendidikan dan akademik',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#1abc9c',
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
