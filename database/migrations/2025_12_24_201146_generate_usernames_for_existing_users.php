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
        // Generate username for existing users
        $users = \App\Models\User::whereNull('username')->get();
        
        foreach ($users as $user) {
            $username = \App\Models\User::generateUsername($user->name, $user->id);
            
            // If username is empty (e.g., name contains only special chars), use email prefix
            if (empty($username)) {
                $emailPrefix = strtolower(explode('@', $user->email)[0]);
                $emailPrefix = preg_replace('/[^a-z0-9_-]/', '', $emailPrefix);
                $baseUsername = $emailPrefix ?: 'user';
                $username = $baseUsername;
                $counter = 1;
                
                while (\App\Models\User::where('username', $username)->where('id', '!=', $user->id)->exists()) {
                    $username = $baseUsername . '-' . $counter;
                    $counter++;
                }
            }
            
            $user->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - usernames can stay
    }
};
