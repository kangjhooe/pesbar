<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('suspension_reason')->nullable()->after('rejection_reason');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
            $table->foreignId('suspended_by')->nullable()->after('suspended_at')->constrained('users')->nullOnDelete();
        });

        // Widen status enum (MySQL) — keep all existing values + suspended
        DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('draft','pending_review','published','archived','rejected','suspended') NOT NULL DEFAULT 'draft'");

        Schema::create('article_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('reason', 100);
            $table->text('details')->nullable();
            $table->string('status', 20)->default('open'); // open | resolved | dismissed
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['article_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_reports');

        // Move any suspended rows back to archived before shrinking enum
        DB::table('articles')->where('status', 'suspended')->update(['status' => 'archived']);

        DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('draft','pending_review','published','archived','rejected') NOT NULL DEFAULT 'draft'");

        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('suspended_by');
            $table->dropColumn(['suspension_reason', 'suspended_at']);
        });
    }
};
