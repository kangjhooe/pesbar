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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('organizer')->nullable();
            $table->enum('event_type', ['pemerintah', 'masyarakat', 'budaya', 'olahraga', 'pendidikan', 'kesehatan', 'lainnya'])->default('pemerintah');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->text('contact_info')->nullable();
            $table->json('metadata')->nullable(); // Untuk data tambahan
            $table->timestamps();
            
            $table->index(['event_date', 'is_active']);
            $table->index(['event_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};