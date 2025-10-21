<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'site_title',
                'setting_value' => 'Portal Berita Kabupaten Pesisir Barat',
                'description' => 'Judul website',
            ],
            [
                'setting_key' => 'site_description',
                'setting_value' => 'Portal berita resmi Kabupaten Pesisir Barat yang menyajikan informasi terkini, akurat, dan terpercaya untuk masyarakat.',
                'description' => 'Deskripsi website',
            ],
            [
                'setting_key' => 'site_keywords',
                'setting_value' => 'berita pesisir barat, kabupaten pesisir barat, lampung, berita lokal',
                'description' => 'Keywords SEO',
            ],
            [
                'setting_key' => 'contact_email',
                'setting_value' => 'info@pesisirbaratnews.id',
                'description' => 'Email kontak',
            ],
            [
                'setting_key' => 'contact_phone',
                'setting_value' => '+62 721 123456',
                'description' => 'Nomor telepon',
            ],
            [
                'setting_key' => 'contact_address',
                'setting_value' => 'Jl. Raya Pesisir Barat No. 1, Kec. Pesisir Barat, Kab. Pesisir Barat, Lampung',
                'description' => 'Alamat',
            ],
            [
                'setting_key' => 'facebook_url',
                'setting_value' => '#',
                'description' => 'URL Facebook',
            ],
            [
                'setting_key' => 'twitter_url',
                'setting_value' => '#',
                'description' => 'URL Twitter',
            ],
            [
                'setting_key' => 'instagram_url',
                'setting_value' => '#',
                'description' => 'URL Instagram',
            ],
            [
                'setting_key' => 'youtube_url',
                'setting_value' => '#',
                'description' => 'URL YouTube',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::firstOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
