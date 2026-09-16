<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('content_warning_count')->default(0)->after('verification_rejection_reason');
            $table->timestamp('publish_restricted_until')->nullable()->after('content_warning_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['content_warning_count', 'publish_restricted_until']);
        });
    }
};
