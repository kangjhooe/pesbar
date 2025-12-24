<?php
/**
 * Script untuk membuat user admin secara manual
 * 
 * Cara penggunaan:
 * 1. Upload file ini ke root folder aplikasi di hosting
 * 2. Akses via browser: https://yourdomain.com/create_admin.php
 * 3. Setelah berhasil, HAPUS file ini untuk keamanan!
 */

// Load Laravel bootstrap
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    // Cek apakah tabel sessions sudah ada
    $sessionsExists = DB::getSchemaBuilder()->hasTable('sessions');
    
    if (!$sessionsExists) {
        // Buat tabel sessions
        DB::statement("
            CREATE TABLE IF NOT EXISTS `sessions` (
              `id` varchar(255) NOT NULL,
              `user_id` bigint UNSIGNED NULL,
              `ip_address` varchar(45) NULL,
              `user_agent` text NULL,
              `payload` longtext NOT NULL,
              `last_activity` int NOT NULL,
              PRIMARY KEY (`id`),
              KEY `sessions_user_id_index` (`user_id`),
              KEY `sessions_last_activity_index` (`last_activity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Tabel sessions berhasil dibuat<br>";
    } else {
        echo "✅ Tabel sessions sudah ada<br>";
    }
    
    // Buat atau update user admin
    $admin = User::firstOrCreate(
        ['email' => 'admin@pesisirbarat.id'],
        [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'verified' => true,
            'email_verified_at' => now(),
        ]
    );
    
    // Update password jika user sudah ada (untuk memastikan password benar)
    if ($admin->wasRecentlyCreated) {
        echo "✅ User admin berhasil dibuat<br>";
    } else {
        $admin->password = bcrypt('password');
        $admin->role = 'admin';
        $admin->verified = true;
        $admin->save();
        echo "✅ User admin berhasil diupdate<br>";
    }
    
    echo "<br>";
    echo "<h2>✅ Setup Berhasil!</h2>";
    echo "<p><strong>Data Login:</strong></p>";
    echo "<ul>";
    echo "<li>Email: <strong>admin@pesisirbarat.id</strong></li>";
    echo "<li>Password: <strong>password</strong></li>";
    echo "</ul>";
    echo "<p><a href='/login'>Klik di sini untuk login</a></p>";
    echo "<br>";
    echo "<p style='color: red;'><strong>⚠️ PENTING: Hapus file ini setelah selesai untuk keamanan!</strong></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

