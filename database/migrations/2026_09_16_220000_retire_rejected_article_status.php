<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Status rejected artikel tidak dipakai lagi (moderasi = tangguhkan).
     */
    public function up(): void
    {
        DB::table('articles')
            ->where('status', 'rejected')
            ->update([
                'status' => 'draft',
                'rejection_reason' => null,
                'published_at' => null,
            ]);
    }

    public function down(): void
    {
        // Data tidak dikembalikan ke rejected.
    }
};
