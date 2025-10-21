<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactImportant;

class ContactImportantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Polres Pesisir Barat',
                'type' => 'polisi',
                'phone' => '0721-123456',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Kantor Polisi Resor Pesisir Barat untuk laporan kejahatan dan darurat',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'RSUD Pesisir Barat',
                'type' => 'rumah_sakit',
                'phone' => '0721-234567',
                'address' => 'Jl. Raya Krui KM 5, Pesisir Barat, Lampung',
                'description' => 'Rumah Sakit Umum Daerah Pesisir Barat untuk layanan kesehatan darurat',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Damkar Pesisir Barat',
                'type' => 'pemadam_kebakaran',
                'phone' => '0721-345678',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Pemadam Kebakaran Pesisir Barat untuk penanganan kebakaran',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Ambulans Darurat',
                'type' => 'ambulans',
                'phone' => '0721-456789',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Layanan ambulans darurat 24 jam',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Posko Bencana Pesisir Barat',
                'type' => 'posko_bencana',
                'phone' => '0721-567890',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Posko penanganan bencana alam dan darurat',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Kantor Camat Pesisir Barat',
                'type' => 'kantor_camat',
                'phone' => '0721-678901',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Kantor Kecamatan Pesisir Barat untuk layanan administrasi',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Puskesmas Krui',
                'type' => 'puskesmas',
                'phone' => '0721-789012',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Pusat Kesehatan Masyarakat untuk layanan kesehatan dasar',
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'SAR Pesisir Barat',
                'type' => 'lainnya',
                'phone' => '0721-890123',
                'address' => 'Jl. Raya Krui, Pesisir Barat, Lampung',
                'description' => 'Tim Search and Rescue untuk penanganan kecelakaan dan pencarian',
                'is_active' => true,
                'sort_order' => 8
            ]
        ];

        foreach ($contacts as $contact) {
            ContactImportant::create($contact);
        }
    }
}
