<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Rapat Koordinasi Pemerintah Kabupaten Pesisir Barat',
                'description' => 'Rapat koordinasi bulanan untuk membahas program pembangunan dan kebijakan daerah.',
                'event_date' => Carbon::now()->addDays(2),
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'location' => 'Aula Kantor Bupati Pesisir Barat',
                'organizer' => 'Sekretariat Daerah',
                'event_type' => 'pemerintah',
                'priority' => 'high',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Telp: 0721-123456'
            ],
            [
                'title' => 'Festival Budaya Pesisir Barat 2024',
                'description' => 'Festival budaya tahunan menampilkan kesenian dan kuliner khas Pesisir Barat.',
                'event_date' => Carbon::now()->addDays(7),
                'start_time' => '18:00:00',
                'end_time' => '22:00:00',
                'location' => 'Lapangan Merdeka Pesisir Barat',
                'organizer' => 'Dinas Pariwisata dan Kebudayaan',
                'event_type' => 'budaya',
                'priority' => 'high',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Email: festival@pesisirbarat.go.id'
            ],
            [
                'title' => 'Turnamen Sepak Bola Antar Desa',
                'description' => 'Kompetisi sepak bola antar desa se-Kabupaten Pesisir Barat.',
                'event_date' => Carbon::now()->addDays(10),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'location' => 'Stadion Pesisir Barat',
                'organizer' => 'Dinas Pemuda dan Olahraga',
                'event_type' => 'olahraga',
                'priority' => 'medium',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Telp: 0721-234567'
            ],
            [
                'title' => 'Seminar Kesehatan Masyarakat',
                'description' => 'Seminar tentang pencegahan penyakit dan pola hidup sehat.',
                'event_date' => Carbon::now()->addDays(14),
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'location' => 'Puskesmas Pesisir Barat',
                'organizer' => 'Dinas Kesehatan',
                'event_type' => 'kesehatan',
                'priority' => 'medium',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Telp: 0721-345678'
            ],
            [
                'title' => 'Workshop Digital Marketing untuk UMKM',
                'description' => 'Pelatihan digital marketing untuk pengusaha kecil dan menengah.',
                'event_date' => Carbon::now()->addDays(21),
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'location' => 'Balai Latihan Kerja Pesisir Barat',
                'organizer' => 'Dinas Koperasi dan UKM',
                'event_type' => 'pendidikan',
                'priority' => 'medium',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Email: umkm@pesisirbarat.go.id'
            ],
            [
                'title' => 'Gotong Royong Bersih Desa',
                'description' => 'Kegiatan gotong royong membersihkan lingkungan desa.',
                'event_date' => Carbon::now()->addDays(3),
                'start_time' => '07:00:00',
                'end_time' => '11:00:00',
                'location' => 'Desa Pesisir Utara',
                'organizer' => 'Pemerintah Desa Pesisir Utara',
                'event_type' => 'masyarakat',
                'priority' => 'low',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Telp: 0812-3456789'
            ],
            [
                'title' => 'Pameran Produk Unggulan Daerah',
                'description' => 'Pameran produk unggulan dan hasil pertanian Kabupaten Pesisir Barat.',
                'event_date' => Carbon::now()->addDays(28),
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'location' => 'Gedung Serbaguna Pesisir Barat',
                'organizer' => 'Dinas Perdagangan dan Perindustrian',
                'event_type' => 'pemerintah',
                'priority' => 'medium',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Telp: 0721-456789'
            ],
            [
                'title' => 'Konser Amal untuk Korban Bencana',
                'description' => 'Konser amal untuk mengumpulkan dana bantuan korban bencana alam.',
                'event_date' => Carbon::now()->addDays(35),
                'start_time' => '19:00:00',
                'end_time' => '23:00:00',
                'location' => 'Taman Kota Pesisir Barat',
                'organizer' => 'Komunitas Musik Pesisir Barat',
                'event_type' => 'masyarakat',
                'priority' => 'high',
                'is_public' => true,
                'is_active' => true,
                'contact_info' => 'Email: konser@pesisirbarat.com'
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}