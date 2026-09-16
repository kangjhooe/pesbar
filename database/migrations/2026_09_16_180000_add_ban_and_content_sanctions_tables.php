<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('banned_until')->nullable()->after('publish_restricted_until');
            $table->string('ban_appeal_status')->nullable()->after('banned_until');
            $table->text('ban_appeal_message')->nullable()->after('ban_appeal_status');
            $table->timestamp('ban_appeal_at')->nullable()->after('ban_appeal_message');
            $table->text('ban_appeal_rejection_reason')->nullable()->after('ban_appeal_at');
        });

        Schema::create('content_sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('level');
            $table->unsignedInteger('days');
            $table->timestamp('banned_until');
            $table->text('reason')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('lifted_at')->nullable();
            $table->foreignId('lifted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('lift_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_sanctions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'banned_until',
                'ban_appeal_status',
                'ban_appeal_message',
                'ban_appeal_at',
                'ban_appeal_rejection_reason',
            ]);
        });
    }
};
