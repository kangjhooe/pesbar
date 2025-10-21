<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if role column exists before modifying
        if (Schema::hasColumn('users', 'role')) {
            // First, modify the enum to include 'user' and change default
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['user', 'admin', 'editor', 'penulis'])->default('user')->change();
            });

            // Then update existing users with role 'penulis' to 'user' if they are not verified
            if (Schema::hasColumn('users', 'verified')) {
                DB::table('users')
                    ->where('role', 'penulis')
                    ->where('verified', false)
                    ->update(['role' => 'user']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'editor', 'penulis'])->default('penulis')->change();
        });
    }
};