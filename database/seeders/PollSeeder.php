<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Poll;
use App\Models\PollOption;
use Carbon\Carbon;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Poll 1: Prioritas Pembangunan
        $poll1 = Poll::create([
            'title' => 'Apa prioritas pembangunan yang paling penting untuk Kabupaten Pesisir Barat?',
            'description' => 'Pilih prioritas pembangunan yang menurut Anda paling penting untuk kemajuan Kabupaten Pesisir Barat.',
            'poll_type' => 'single',
            'is_active' => true,
            'allow_anonymous' => true,
            'show_results' => true,
            'show_vote_count' => true,
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(25),
            'max_votes_per_user' => 1
        ]);

        $poll1Options = [
            ['option_text' => 'Infrastruktur Jalan dan Jembatan', 'color' => '#3B82F6', 'sort_order' => 1],
            ['option_text' => 'Pendidikan dan Pelatihan', 'color' => '#10B981', 'sort_order' => 2],
            ['option_text' => 'Kesehatan dan Fasilitas Medis', 'color' => '#F59E0B', 'sort_order' => 3],
            ['option_text' => 'Pariwisata dan Ekonomi Kreatif', 'color' => '#8B5CF6', 'sort_order' => 4],
            ['option_text' => 'Pertanian dan Perikanan', 'color' => '#EF4444', 'sort_order' => 5]
        ];

        foreach ($poll1Options as $option) {
            PollOption::create([
                'poll_id' => $poll1->id,
                'option_text' => $option['option_text'],
                'color' => $option['color'],
                'sort_order' => $option['sort_order'],
                'is_active' => true
            ]);
        }

        // Poll 2: Event Budaya Favorit
        $poll2 = Poll::create([
            'title' => 'Event budaya apa yang paling Anda sukai?',
            'description' => 'Pilih event budaya yang paling menarik untuk Anda.',
            'poll_type' => 'multiple',
            'is_active' => true,
            'allow_anonymous' => true,
            'show_results' => true,
            'show_vote_count' => true,
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(18),
            'max_votes_per_user' => 3
        ]);

        $poll2Options = [
            ['option_text' => 'Festival Musik Tradisional', 'color' => '#3B82F6', 'sort_order' => 1],
            ['option_text' => 'Pameran Seni dan Kerajinan', 'color' => '#10B981', 'sort_order' => 2],
            ['option_text' => 'Kuliner Khas Daerah', 'color' => '#F59E0B', 'sort_order' => 3],
            ['option_text' => 'Tari dan Pertunjukan Budaya', 'color' => '#8B5CF6', 'sort_order' => 4],
            ['option_text' => 'Workshop dan Pelatihan Seni', 'color' => '#EF4444', 'sort_order' => 5]
        ];

        foreach ($poll2Options as $option) {
            PollOption::create([
                'poll_id' => $poll2->id,
                'option_text' => $option['option_text'],
                'color' => $option['color'],
                'sort_order' => $option['sort_order'],
                'is_active' => true
            ]);
        }

        // Poll 3: Transportasi Umum
        $poll3 = Poll::create([
            'title' => 'Bagaimana pendapat Anda tentang transportasi umum di Pesisir Barat?',
            'description' => 'Berikan pendapat Anda tentang kondisi transportasi umum saat ini.',
            'poll_type' => 'single',
            'is_active' => true,
            'allow_anonymous' => true,
            'show_results' => true,
            'show_vote_count' => true,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(31),
            'max_votes_per_user' => 1
        ]);

        $poll3Options = [
            ['option_text' => 'Sangat Baik', 'color' => '#10B981', 'sort_order' => 1],
            ['option_text' => 'Baik', 'color' => '#3B82F6', 'sort_order' => 2],
            ['option_text' => 'Cukup', 'color' => '#F59E0B', 'sort_order' => 3],
            ['option_text' => 'Kurang Baik', 'color' => '#EF4444', 'sort_order' => 4],
            ['option_text' => 'Sangat Kurang', 'color' => '#6B7280', 'sort_order' => 5]
        ];

        foreach ($poll3Options as $option) {
            PollOption::create([
                'poll_id' => $poll3->id,
                'option_text' => $option['option_text'],
                'color' => $option['color'],
                'sort_order' => $option['sort_order'],
                'is_active' => true
            ]);
        }
    }
}