<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get admin user
        $adminUser = User::where('email', 'admin@pesisirbarat.go.id')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Create tags
        $tags = [
            'Pembangunan', 'Pendidikan', 'Kesehatan', 'Infrastruktur', 'Ekonomi',
            'Pariwisata', 'Pertanian', 'Teknologi', 'Lingkungan', 'Sosial',
            'Budaya', 'Olahraga', 'Politik', 'Keamanan', 'Transportasi'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        $allTags = Tag::all();

        // Create 20 berita articles
        $this->createBeritaArticles($categories, $adminUser, $allTags);
        
        // Create 15 artikel articles
        $this->createArtikelArticles($categories, $adminUser, $allTags);

        $this->command->info('Dummy data created successfully!');
    }

    private function createBeritaArticles($categories, $adminUser, $allTags)
    {
        $beritaTitles = [
            'Pembangunan Jembatan Penghubung Antar Desa di Pesisir Barat Dimulai',
            'Festival Budaya Pesisir Barat 2024 Sukses Digelar',
            'Program Beasiswa Pendidikan untuk Anak Pesisir Barat',
            'Pembangunan Puskesmas Baru di Desa Terpencil',
            'Peningkatan Produksi Padi di Pesisir Barat',
            'Wisata Pantai Pesisir Barat Dikunjungi 10.000 Wisatawan',
            'Pelatihan Kewirausahaan untuk Pemuda Pesisir Barat',
            'Penanaman 1000 Pohon Mangrove di Pesisir Barat',
            'Pembangunan Jalan Lingkar Kota Pesisir Barat',
            'Program Digitalisasi Layanan Publik Pesisir Barat',
            'Festival Kuliner Tradisional Pesisir Barat',
            'Pembangunan Taman Edukasi untuk Anak-anak',
            'Program Pemberdayaan Perempuan di Pesisir Barat',
            'Peningkatan Kualitas Air Bersih di Pesisir Barat',
            'Pengembangan Wisata Bahari di Pesisir Barat',
            'Pembangunan Terminal Bus Modern di Pesisir Barat',
            'Program Pemberantasan Buta Aksara di Pesisir Barat',
            'Festival Musik Tradisional Pesisir Barat',
            'Pembangunan Pasar Rakyat Terpadu',
            'Program Konservasi Hutan Mangrove'
        ];

        $this->command->info('Creating 20 berita articles...');
        
        foreach ($beritaTitles as $index => $title) {
            $article = Article::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => 'Ini adalah excerpt untuk artikel: ' . $title . '. Artikel ini membahas tentang berbagai aspek pembangunan dan kemajuan di Pesisir Barat.',
                'content' => $this->generateContent($title),
                'category_id' => $categories->random()->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => $index < 5, // First 5 are featured
                'is_breaking' => $index < 2, // First 2 are breaking
                'views' => rand(50, 1000),
                'published_at' => Carbon::now()->subDays($index + 1),
            ]);

            // Attach random tags
            $randomTags = $allTags->random(rand(2, 4));
            $article->tags()->attach($randomTags->pluck('id'));
        }
    }

    private function createArtikelArticles($categories, $adminUser, $allTags)
    {
        $artikelTitles = [
            'Sejarah dan Budaya Pesisir Barat: Warisan yang Harus Dilestarikan',
            'Potensi Ekonomi Kreatif di Pesisir Barat',
            'Strategi Pengembangan Pariwisata Berkelanjutan di Pesisir Barat',
            'Inovasi Teknologi dalam Pertanian Modern di Pesisir Barat',
            'Peran Generasi Muda dalam Pembangunan Pesisir Barat',
            'Konservasi Lingkungan Pesisir: Tantangan dan Solusi',
            'Digitalisasi Layanan Publik: Transformasi Menuju Pemerintahan Modern',
            'Kuliner Tradisional Pesisir Barat: Warisan Rasa yang Menggugah Selera',
            'Pendidikan Inklusif: Mewujudkan Kesetaraan di Pesisir Barat',
            'Ekonomi Biru: Potensi Kelautan untuk Pembangunan Berkelanjutan',
            'Pemberdayaan Masyarakat Pesisir melalui Koperasi',
            'Teknologi Informasi untuk Peningkatan Layanan Publik',
            'Budaya Lokal sebagai Daya Tarik Wisata',
            'Pembangunan Berkelanjutan di Era Digital',
            'Inovasi dalam Pengelolaan Sampah di Pesisir Barat'
        ];

        $this->command->info('Creating 15 artikel articles...');
        
        foreach ($artikelTitles as $index => $title) {
            $article = Article::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => 'Artikel ini membahas secara mendalam tentang: ' . $title . '. Sebuah analisis komprehensif tentang berbagai aspek pembangunan di Pesisir Barat.',
                'content' => $this->generateContent($title),
                'category_id' => $categories->random()->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => $index < 3, // First 3 are featured
                'is_breaking' => false,
                'views' => rand(100, 800),
                'published_at' => Carbon::now()->subDays($index + 2),
            ]);

            // Attach random tags
            $randomTags = $allTags->random(rand(2, 4));
            $article->tags()->attach($randomTags->pluck('id'));
        }
    }

    private function generateContent($title)
    {
        return "
        <h2>Pengantar</h2>
        <p>Artikel ini membahas tentang " . $title . " yang menjadi topik penting dalam pembangunan Pesisir Barat. Pembahasan ini akan mencakup berbagai aspek yang relevan dengan topik tersebut.</p>
        
        <h3>Latar Belakang</h3>
        <p>Pesisir Barat sebagai salah satu kabupaten di Provinsi Lampung memiliki potensi yang besar dalam berbagai bidang. Perkembangan yang terjadi di wilayah ini perlu mendapat perhatian serius dari berbagai pihak terkait.</p>
        
        <h3>Pembahasan</h3>
        <p>Dalam konteks " . $title . ", terdapat beberapa hal penting yang perlu diperhatikan. Pertama, aspek perencanaan yang matang diperlukan untuk memastikan keberhasilan program atau kegiatan yang dilaksanakan. Kedua, partisipasi masyarakat menjadi kunci utama dalam implementasi berbagai program pembangunan.</p>
        
        <p>Selain itu, dukungan dari pemerintah daerah dan berbagai stakeholder lainnya juga sangat diperlukan. Kolaborasi yang baik antara berbagai pihak akan memastikan tercapainya tujuan yang diharapkan.</p>
        
        <h3>Kesimpulan</h3>
        <p>Dari pembahasan di atas, dapat disimpulkan bahwa " . $title . " merupakan hal yang penting untuk diperhatikan dalam konteks pembangunan Pesisir Barat. Dengan perencanaan yang baik dan partisipasi aktif dari berbagai pihak, diharapkan dapat memberikan dampak positif bagi masyarakat dan wilayah Pesisir Barat secara keseluruhan.</p>
        ";
    }
}