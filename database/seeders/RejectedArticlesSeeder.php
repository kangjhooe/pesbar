<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RejectedArticlesSeeder extends Seeder
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

        // Create 3 rejected articles
        $rejectedArticles = [
            [
                'title' => 'Kontroversi Pembangunan Hotel di Kawasan Konservasi',
                'excerpt' => 'Artikel ini ditolak karena mengandung informasi yang belum diverifikasi dan dapat menimbulkan konflik di masyarakat.',
                'content' => 'Artikel ini ditolak karena mengandung informasi yang belum diverifikasi dan dapat menimbulkan konflik di masyarakat. Penulis diminta untuk melakukan verifikasi ulang terhadap sumber informasi dan memperbaiki konten sebelum mengajukan kembali.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Lingkungan')->first()->id ?? $categories->first()->id,
                'rejection_reason' => 'Informasi belum diverifikasi dan dapat menimbulkan konflik di masyarakat',
            ],
            [
                'title' => 'Analisis Politik Lokal yang Tidak Objektif',
                'excerpt' => 'Artikel ini ditolak karena mengandung analisis yang tidak objektif dan cenderung memihak salah satu pihak.',
                'content' => 'Artikel ini ditolak karena mengandung analisis yang tidak objektif dan cenderung memihak salah satu pihak. Penulis diminta untuk melakukan analisis yang lebih netral dan berdasarkan fakta yang dapat diverifikasi.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Politik')->first()->id ?? $categories->first()->id,
                'rejection_reason' => 'Analisis tidak objektif dan cenderung memihak salah satu pihak',
            ],
            [
                'title' => 'Berita Hoax tentang Bencana Alam',
                'excerpt' => 'Artikel ini ditolak karena mengandung informasi hoax tentang bencana alam yang dapat menimbulkan kepanikan di masyarakat.',
                'content' => 'Artikel ini ditolak karena mengandung informasi hoax tentang bencana alam yang dapat menimbulkan kepanikan di masyarakat. Penulis diminta untuk selalu memverifikasi informasi dari sumber yang kredibel sebelum menulis artikel.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Lingkungan')->first()->id ?? $categories->first()->id,
                'rejection_reason' => 'Mengandung informasi hoax yang dapat menimbulkan kepanikan di masyarakat',
            ]
        ];

        foreach ($rejectedArticles as $index => $articleData) {
            // Get random penulis user
            $author = $penulisUsers->random();
            
            // Get random category if specific category not found
            if (!isset($articleData['category_id'])) {
                $articleData['category_id'] = $categories->random()->id;
            }

            // Create article with rejected status
            $article = Article::create([
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'excerpt' => $articleData['excerpt'],
                'content' => $articleData['content'],
                'category_id' => $articleData['category_id'],
                'author_id' => $author->id,
                'status' => 'rejected',
                'type' => $articleData['type'],
                'is_featured' => false,
                'is_breaking' => false,
                'views' => 0,
                'rejection_reason' => $articleData['rejection_reason'],
                'created_at' => Carbon::now()->subDays(rand(1, 21)), // Random date within last 3 weeks
                'updated_at' => Carbon::now()->subDays(rand(1, 21)),
            ]);

            // Attach random tags
            $randomTags = $tags->random(rand(2, 4));
            $article->tags()->attach($randomTags->pluck('id'));

            $this->command->info("Created rejected article: {$article->title} by {$author->name}");
        }

        $this->command->info('Successfully created 3 rejected articles!');
    }
}