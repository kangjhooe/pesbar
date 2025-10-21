<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            'site_name' => 'Portal Berita Pesisir Barat',
            'site_description' => 'Portal berita resmi Kabupaten Pesisir Barat yang menyajikan informasi terkini dan terpercaya',
            'site_keywords' => 'berita, pesisir barat, lampung, informasi, news',
            'site_author' => 'Portal Berita Pesisir Barat',
            'contact_email' => 'info@pesisirbarat.com',
            'contact_phone' => '+62 812-3456-7890',
            'contact_address' => 'Jl. Raya Pesisir Barat, Lampung',
            
            // Social Media
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            
            // About Page
            'about_title' => 'Tentang Portal Berita Pesisir Barat',
            'about_content' => 'Portal Berita Pesisir Barat adalah media online resmi yang menyajikan informasi terkini, akurat, dan terpercaya tentang berbagai peristiwa dan perkembangan di Kabupaten Pesisir Barat, Lampung. Kami berkomitmen untuk memberikan layanan informasi yang berkualitas kepada masyarakat.',
            'mission_title' => 'Misi Kami',
            'mission_content' => 'Menyediakan informasi yang akurat, cepat, dan terpercaya untuk mendukung transparansi dan partisipasi masyarakat dalam pembangunan daerah.',
            'vision_title' => 'Visi Kami',
            'vision_content' => 'Menjadi media informasi terdepan dan terpercaya di Kabupaten Pesisir Barat yang mendukung terwujudnya masyarakat yang informatif dan partisipatif.',
            
            // SEO Settings
            'meta_title' => 'Portal Berita Pesisir Barat - Informasi Terkini',
            'meta_description' => 'Portal berita resmi Kabupaten Pesisir Barat yang menyajikan informasi terkini dan terpercaya tentang berbagai peristiwa dan perkembangan di daerah.',
            'meta_keywords' => 'berita pesisir barat, lampung, informasi terkini, news',
            'google_analytics' => '',
            'google_search_console' => '',
            'facebook_pixel' => '',
            
            // Logo Settings
            'site_logo' => 'settings/logo-default.png',
            'site_favicon' => 'settings/favicon-default.png',
            
            // System Settings
            'articles_per_page' => '10',
            'comments_per_page' => '10',
            'auto_approve_comments' => '0',
            'require_comment_approval' => '1',
            'enable_registration' => '1',
            'enable_newsletter' => '1',
            'maintenance_mode' => '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }
    }
}
