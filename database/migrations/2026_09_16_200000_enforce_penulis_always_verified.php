<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Invariant: role=penulis selalu verified.
     * Bersihkan data lama: demote penulis unverified → user; artikel pending_review → draft.
     * Pengajuan verifikasi pending tetap dipertahankan sebagai upgrade request (role user).
     */
    public function up(): void
    {
        $unverified = User::query()
            ->where('role', 'penulis')
            ->where(function ($q) {
                $q->where('verified', false)->orWhereNull('verified');
            })
            ->get();

        foreach ($unverified as $user) {
            $pendingArticles = Article::query()
                ->where('author_id', $user->id)
                ->where('status', 'pending_review')
                ->update([
                    'status' => 'draft',
                    'rejection_reason' => null,
                ]);

            $keepUpgradePending = $user->verification_request_status === 'pending';

            DB::table('users')->where('id', $user->id)->update([
                'role' => 'user',
                'verified' => false,
                'is_internal' => false,
                'verification_request_status' => $keepUpgradePending ? 'pending' : null,
                'verification_rejection_reason' => $keepUpgradePending
                    ? null
                    : $user->verification_rejection_reason,
                'publish_restricted_until' => null,
                'updated_at' => now(),
            ]);

            Log::info('Demoted unverified penulis to user', [
                'user_id' => $user->id,
                'pending_articles_to_draft' => $pendingArticles,
                'kept_upgrade_pending' => $keepUpgradePending,
            ]);
        }
    }

    public function down(): void
    {
        // Irreversible data cleanup — no restore of unverified penulis roles.
    }
};
