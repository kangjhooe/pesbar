<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Konten dipisah via kategori, bukan field type berita/artikel.
     */
    public function up(): void
    {
        if (Schema::hasColumn('articles', 'type')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('articles', 'type')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->enum('type', ['berita', 'artikel'])->default('berita')->after('status');
            });
        }
    }
};
