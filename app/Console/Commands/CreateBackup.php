<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {--type=full : Backup type (database, files, full)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database and/or files backup';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        $type = $this->option('type');

        $this->info("Creating {$type} backup...");
        $this->newLine();

        try {
            switch ($type) {
                case 'database':
                    $result = $backupService->createDatabaseBackup();
                    if ($result) {
                        $this->info("✅ Database backup created: " . basename($result));
                    } else {
                        $this->error("❌ Database backup failed!");
                        return 1;
                    }
                    break;

                case 'files':
                    $result = $backupService->createFilesBackup();
                    if ($result) {
                        $this->info("✅ Files backup created: " . basename($result));
                    } else {
                        $this->error("❌ Files backup failed!");
                        return 1;
                    }
                    break;

                case 'full':
                default:
                    $results = $backupService->createFullBackup();
                    if ($results['success']) {
                        $this->info("✅ Full backup completed!");
                        if ($results['database']) {
                            $this->line("  - Database: " . basename($results['database']));
                        }
                        if ($results['files']) {
                            $this->line("  - Files: " . basename($results['files']));
                        }
                    } else {
                        $this->error("❌ Full backup failed!");
                        return 1;
                    }
                    break;
            }

            // Cleanup old backups
            $deleted = $backupService->cleanupOldBackups(10);
            if ($deleted > 0) {
                $this->info("🧹 Cleaned up {$deleted} old backup(s)");
            }

            // Show storage info
            $storage = $backupService->getBackupStorageSize();
            $this->newLine();
            $this->info("Backup Storage:");
            $this->line("  - Total backups: {$storage['backup_count']}");
            $this->line("  - Total size: {$storage['total_size_human']}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return 1;
        }
    }
}

