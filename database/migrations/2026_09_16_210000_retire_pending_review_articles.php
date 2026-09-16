<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tidak ada antrean review: sisa pending_review → draft.
     * Status enum tetap mempertahankan nilai lama agar rollback aman.
     */
    public function up(): void
    {
        DB::table('articles')
            ->where('status', 'pending_review')
            ->update([
                'status' => 'draft',
                'rejection_reason' => null,
                'published_at' => null,
            ]);
    }

    public function down(): void
    {
        // Data tidak dikembalikan ke pending_review.
    }
};
