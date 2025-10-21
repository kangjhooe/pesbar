<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Pemerintahan',
            'Pembangunan',
            'Pendidikan',
            'Kesehatan',
            'Pariwisata',
            'Ekonomi',
            'Sosial',
            'Budaya',
            'Olahraga',
            'Teknologi',
            'Lingkungan',
            'Infrastruktur',
            'Pertanian',
            'Perikanan',
            'Keamanan',
            'Hukum',
            'Bencana',
            'Kemiskinan',
            'Pemuda',
            'Perempuan'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }
    }
}
