<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('poll_type', ['single', 'multiple'])->default('single');
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_anonymous')->default(true);
            $table->boolean('show_results')->default(true);
            $table->boolean('show_vote_count')->default(true);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->integer('max_votes_per_user')->default(1);
            $table->json('settings')->nullable(); // Untuk pengaturan tambahan
            $table->timestamps();
            
            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};