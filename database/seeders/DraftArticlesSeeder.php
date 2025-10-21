<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DraftArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get penulis users
        $penulisUsers = User::where('role', 'penulis')->get();
        
        if ($penulisUsers->isEmpty()) {
            $this->command->info('No penulis users found. Please create penulis users first.');
            return;
        }

        // Get categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get tags
        $tags = Tag::all();
        if ($tags->isEmpty()) {
            $this->command->info('No tags found. Please run TagSeeder first.');
            return;
        }

        // Create 3 draft articles
        $draftArticles = [
            [
                'title' => 'Strategi Pengembangan Ekonomi Kreatif di Pesisir Barat',
                'excerpt' => 'Pemerintah Kabupaten Pesisir Barat mengembangkan strategi untuk meningkatkan ekonomi kreatif melalui pemberdayaan UMKM dan industri kreatif lokal.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Dinas Koperasi dan UKM mengembangkan strategi komprehensif untuk meningkatkan ekonomi kreatif di daerah. Program ini bertujuan untuk memberdayakan UMKM dan industri kreatif lokal agar dapat bersaing di pasar nasional dan internasional.

Strategi pengembangan ekonomi kreatif meliputi pelatihan kewirausahaan, akses permodalan, dan pengembangan produk yang inovatif. UMKM dilatih untuk mengembangkan produk yang memiliki nilai tambah tinggi dan dapat bersaing di pasar global. Program ini juga meliputi pelatihan digital marketing dan e-commerce untuk memperluas jangkauan pasar.

"Ekonomi kreatif memiliki potensi besar untuk meningkatkan perekonomian daerah dan menciptakan lapangan kerja baru," ujar Kepala Dinas Koperasi dan UKM Pesisir Barat.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Ekonomi')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Rehabilitasi Hutan Mangrove untuk Mitigasi Perubahan Iklim',
                'excerpt' => 'Program rehabilitasi hutan mangrove di Pesisir Barat bertujuan untuk mitigasi perubahan iklim dan melindungi ekosistem pesisir.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Dinas Lingkungan Hidup meluncurkan program rehabilitasi hutan mangrove yang bertujuan untuk mitigasi perubahan iklim dan melindungi ekosistem pesisir. Program ini meliputi penanaman mangrove di area yang telah rusak dan pengembangan sistem monitoring yang terintegrasi.

Rehabilitasi hutan mangrove dilakukan dengan melibatkan masyarakat lokal, kelompok tani, dan organisasi lingkungan. Masyarakat dilatih untuk melakukan penanaman mangrove yang benar dan merawat tanaman hingga tumbuh dengan baik. Program ini juga meliputi pembentukan kelompok pengawas mangrove yang bertugas untuk menjaga kelestarian hutan mangrove.

"Hutan mangrove memiliki peran penting dalam mitigasi perubahan iklim dan melindungi ekosistem pesisir dari abrasi," kata Kepala Dinas Lingkungan Hidup Pesisir Barat.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Lingkungan')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Pengembangan Smart City di Pesisir Barat',
                'excerpt' => 'Pemerintah Kabupaten Pesisir Barat mengembangkan konsep smart city untuk meningkatkan efisiensi layanan publik dan kualitas hidup masyarakat.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat mengembangkan konsep smart city yang bertujuan untuk meningkatkan efisiensi layanan publik dan kualitas hidup masyarakat. Program smart city ini meliputi implementasi teknologi informasi dan komunikasi dalam berbagai aspek pemerintahan dan pelayanan publik.

Konsep smart city di Pesisir Barat meliputi pengembangan sistem transportasi cerdas, manajemen sampah yang terintegrasi, dan sistem monitoring keamanan yang real-time. Sistem transportasi cerdas akan menggunakan teknologi GPS dan aplikasi mobile untuk memberikan informasi real-time tentang kondisi lalu lintas dan jadwal transportasi publik.

"Smart city adalah masa depan pembangunan perkotaan yang berkelanjutan dan efisien," ujar Sekretaris Daerah Pesisir Barat.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Teknologi')->first()->id ?? $categories->first()->id,
            ]
        ];

        foreach ($draftArticles as $index => $articleData) {
            // Get random penulis user
            $author = $penulisUsers->random();
            
            // Get random category if specific category not found
            if (!isset($articleData['category_id'])) {
                $articleData['category_id'] = $categories->random()->id;
            }

            // Create article with draft status
            $article = Article::create([
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'excerpt' => $articleData['excerpt'],
                'content' => $articleData['content'],
                'category_id' => $articleData['category_id'],
                'author_id' => $author->id,
                'status' => 'draft',
                'type' => $articleData['type'],
                'is_featured' => false,
                'is_breaking' => false,
                'views' => 0,
                'created_at' => Carbon::now()->subDays(rand(1, 14)), // Random date within last 2 weeks
                'updated_at' => Carbon::now()->subDays(rand(1, 14)),
            ]);

            // Attach random tags
            $randomTags = $tags->random(rand(2, 4));
            $article->tags()->attach($randomTags->pluck('id'));

            $this->command->info("Created draft article: {$article->title} by {$author->name}");
        }

        $this->command->info('Successfully created 3 draft articles!');
    }
}