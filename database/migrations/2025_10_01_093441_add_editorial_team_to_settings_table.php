<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tidak perlu menambah kolom baru karena settings menggunakan key-value pair
        // Kita akan menambah data default untuk tim redaksi
        DB::table('settings')->insert([
            [
                'setting_key' => 'editorial_team_title',
                'setting_value' => 'Tim Redaksi',
                'description' => 'Judul untuk bagian tim redaksi'
            ],
            [
                'setting_key' => 'editorial_team_content',
                'setting_value' => json_encode([
                    [
                        'name' => 'Dr. Ahmad Wijaya, S.Sos., M.Si.',
                        'position' => 'Pemimpin Redaksi',
                        'description' => 'Pengalaman 15 tahun di bidang jurnalistik'
                    ],
                    [
                        'name' => 'Siti Nurhaliza, S.Ikom.',
                        'position' => 'Wakil Pemimpin Redaksi',
                        'description' => 'Spesialisasi media digital dan komunikasi'
                    ],
                    [
                        'name' => 'Budi Santoso, S.Hum.',
                        'position' => 'Editor Senior',
                        'description' => 'Ahli dalam bidang politik dan pemerintahan'
                    ],
                    [
                        'name' => 'Maya Sari, S.Ikom.',
                        'position' => 'Reporter Senior',
                        'description' => 'Spesialisasi berita sosial dan budaya'
                    ]
                ]),
                'description' => 'Data tim redaksi dalam format JSON'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('setting_key', [
            'editorial_team_title',
            'editorial_team_content'
        ])->delete();
    }
};
