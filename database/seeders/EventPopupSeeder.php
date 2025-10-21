<?php

namespace Database\Seeders;

use App\Models\EventPopup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventPopupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventPopups = [
            [
                'title' => 'Selamat Datang di Portal Berita Pesisir Barat',
                'message' => 'Selamat datang di portal berita resmi Kabupaten Pesisir Barat. Dapatkan informasi terkini seputar kabupaten kami, mulai dari berita pemerintahan, pembangunan, hingga kegiatan masyarakat.',
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(30),
                'status' => true,
            ],
            [
                'title' => 'Pembaruan Sistem Website',
                'message' => 'Website portal berita telah diperbarui dengan fitur-fitur terbaru untuk memberikan pengalaman browsing yang lebih baik. Nikmati navigasi yang lebih mudah dan tampilan yang lebih menarik.',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(15),
                'status' => false,
            ],
            [
                'title' => 'Layanan Publik Online',
                'message' => 'Pemerintah Kabupaten Pesisir Barat kini menyediakan berbagai layanan publik secara online. Kunjungi halaman layanan untuk informasi lebih lanjut.',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'status' => false,
            ],
        ];

        foreach ($eventPopups as $popup) {
            EventPopup::create($popup);
        }
    }
}
