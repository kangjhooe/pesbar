<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use ZipArchive;

class BackupService
{
    /**
     * Create database backup (PHP dump — works on XAMPP/Windows without mysqldump PATH).
     */
    public function createDatabaseBackup(): ?string
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = 'database_' . date('Y-m-d_His') . '.sql';
            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            $sql = $this->generateSqlDump();
            if ($sql === null || $sql === '') {
                Log::error('Database backup failed: empty SQL dump');
                return null;
            }

            if (file_put_contents($filepath, $sql) === false) {
                Log::error('Database backup failed: could not write file', ['path' => $filepath]);
                return null;
            }

            $compressedPath = $this->compressFile($filepath);
            if ($compressedPath) {
                @unlink($filepath);
                return $compressedPath;
            }

            return $filepath;
        } catch (\Exception $e) {
            Log::error('Database backup exception: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Generate SQL dump via Laravel DB (portable fallback for local/XAMPP).
     */
    protected function generateSqlDump(): ?string
    {
        try {
            $database = DB::getDatabaseName();
            $tables = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . $database;

            $lines = [
                '-- Pesbar database backup',
                '-- Generated at: ' . now()->toDateTimeString(),
                '-- Database: ' . $database,
                'SET FOREIGN_KEY_CHECKS=0;',
                '',
            ];

            foreach ($tables as $tableRow) {
                $table = $tableRow->$key ?? null;
                if (!$table) {
                    continue;
                }

                $create = DB::select("SHOW CREATE TABLE `{$table}`");
                $createSql = $create[0]->{'Create Table'} ?? null;
                if (!$createSql) {
                    continue;
                }

                $lines[] = "DROP TABLE IF EXISTS `{$table}`;";
                $lines[] = $createSql . ';';
                $lines[] = '';

                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_keys($rowArray);
                    $values = [];
                    foreach ($rowArray as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif (is_bool($value)) {
                            $values[] = $value ? '1' : '0';
                        } elseif (is_int($value) || is_float($value)) {
                            $values[] = (string) $value;
                        } else {
                            $values[] = "'" . str_replace(
                                ["\\", "'", "\n", "\r", "\x00"],
                                ["\\\\", "\\'", "\\n", "\\r", "\\0"],
                                (string) $value
                            ) . "'";
                        }
                    }
                    $lines[] = 'INSERT INTO `' . $table . '` (`' . implode('`, `', $columns) . '`) VALUES (' . implode(', ', $values) . ');';
                }

                $lines[] = '';
            }

            $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

            return implode("\n", $lines);
        } catch (\Exception $e) {
            Log::error('SQL dump generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create files backup (storage/app/public)
     */
    public function createFilesBackup(): ?string
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = 'files_' . date('Y-m-d_His') . '.zip';
            $filepath = $backupDir . '/' . $filename;

            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                Log::error('Failed to create zip archive for files backup');
                return null;
            }

            // Add public storage files
            $publicPath = storage_path('app/public');
            if (is_dir($publicPath)) {
                $this->addDirectoryToZip($zip, $publicPath, 'storage');
            }

            $zip->close();

            if (!file_exists($filepath)) {
                return null;
            }

            return $filepath;
        } catch (\Exception $e) {
            Log::error('Files backup exception: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Create full backup (database + files)
     */
    public function createFullBackup(): array
    {
        $results = [
            'database' => null,
            'files' => null,
            'success' => false,
            'created_at' => now()->toDateTimeString(),
        ];

        try {
            // Backup database
            $results['database'] = $this->createDatabaseBackup();

            // Backup files
            $results['files'] = $this->createFilesBackup();

            $results['success'] = !empty($results['database']) || !empty($results['files']);

            Log::info('Full backup completed', $results);

            return $results;
        } catch (\Exception $e) {
            Log::error('Full backup failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return $results;
        }
    }

    /**
     * Compress file using gzip
     */
    protected function compressFile(string $filepath): ?string
    {
        try {
            $compressedPath = $filepath . '.gz';
            $fp_in = fopen($filepath, 'rb');
            $fp_out = gzopen($compressedPath, 'wb9');

            if (!$fp_in || !$fp_out) {
                return null;
            }

            while (!feof($fp_in)) {
                gzwrite($fp_out, fread($fp_in, 8192));
            }

            fclose($fp_in);
            gzclose($fp_out);

            return $compressedPath;
        } catch (\Exception $e) {
            Log::warning('File compression failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add directory to zip recursively
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipPath = ''): void
    {
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir . '/' . $file;
            $zipFilePath = $zipPath ? $zipPath . '/' . $file : $file;

            if (is_dir($filePath)) {
                $zip->addEmptyDir($zipFilePath);
                $this->addDirectoryToZip($zip, $filePath, $zipFilePath);
            } else {
                $zip->addFile($filePath, $zipFilePath);
            }
        }
    }

    /**
     * Get list of backups
     */
    public function getBackups(): array
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            return [];
        }

        $backups = [];
        $files = scandir($backupDir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filepath = $backupDir . '/' . $file;
            if (is_file($filepath)) {
                $backups[] = [
                    'filename' => $file,
                    'path' => $filepath,
                    'size' => filesize($filepath),
                    'size_human' => $this->formatBytes(filesize($filepath)),
                    'created_at' => Carbon::createFromTimestamp(filemtime($filepath)),
                    'type' => strpos($file, 'database_') === 0 ? 'database' : 'files',
                ];
            }
        }

        // Sort by created_at descending
        usort($backups, function ($a, $b) {
            return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
        });

        return $backups;
    }

    /**
     * Delete old backups (keep only last N backups)
     */
    public function cleanupOldBackups(int $keepCount = 10): int
    {
        $backups = $this->getBackups();
        $deleted = 0;

        if (count($backups) <= $keepCount) {
            return 0;
        }

        // Group by type
        $databaseBackups = array_filter($backups, fn($b) => $b['type'] === 'database');
        $fileBackups = array_filter($backups, fn($b) => $b['type'] === 'files');

        // Keep only last N of each type
        $toDelete = array_merge(
            array_slice($databaseBackups, $keepCount),
            array_slice($fileBackups, $keepCount)
        );

        foreach ($toDelete as $backup) {
            if (@unlink($backup['path'])) {
                $deleted++;
                Log::info("Deleted old backup: {$backup['filename']}");
            }
        }

        return $deleted;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get backup storage size
     */
    public function getBackupStorageSize(): array
    {
        $backups = $this->getBackups();
        $totalSize = array_sum(array_column($backups, 'size'));

        return [
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'backup_count' => count($backups),
        ];
    }
}

