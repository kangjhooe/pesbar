<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('correction_notice')->nullable()->after('rejection_reason');
            $table->timestamp('corrected_at')->nullable()->after('correction_notice');
            $table->timestamp('fact_checked_at')->nullable()->after('corrected_at');
            $table->foreignId('fact_checked_by')->nullable()->after('fact_checked_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fact_checked_by');
            $table->dropColumn(['correction_notice', 'corrected_at', 'fact_checked_at']);
        });
    }
};
