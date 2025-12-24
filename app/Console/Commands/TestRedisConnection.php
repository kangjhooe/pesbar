<?php

namespace App\Console\Commands;

use App\Helpers\RedisHelper;
use Illuminate\Console\Command;

class TestRedisConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis connection and display statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Redis connection...');
        $this->newLine();

        // Test connection
        if (!RedisHelper::isAvailable()) {
            $this->error('❌ Redis is not available!');
            $this->warn('Please check your Redis configuration in .env file.');
            $this->warn('Make sure Redis server is running.');
            return 1;
        }

        $this->info('✅ Redis connection successful!');
        $this->newLine();

        // Display info
        $info = RedisHelper::getInfo();
        $this->info('Redis Information:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Status', $info['status']],
                ['Connected Clients', $info['connected_clients'] ?? 'N/A'],
                ['Used Memory', $info['used_memory_human'] ?? 'N/A'],
                ['Peak Memory', $info['used_memory_peak_human'] ?? 'N/A'],
                ['Uptime', $this->formatUptime($info['uptime_in_seconds'] ?? 0)],
            ]
        );
        $this->newLine();

        // Display cache stats
        $stats = RedisHelper::getCacheStats();
        if ($stats['available']) {
            $this->info('Cache Statistics:');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Cache Hits', number_format($stats['hits'] ?? 0)],
                    ['Cache Misses', number_format($stats['misses'] ?? 0)],
                    ['Hit Rate', ($stats['hit_rate'] ?? 0) . '%'],
                ]
            );
            $this->newLine();
        }

        // Display memory usage
        $memory = RedisHelper::getMemoryUsage();
        if ($memory['available']) {
            $this->info('Memory Usage:');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Used Memory', $memory['used_memory_human'] ?? 'N/A'],
                    ['Peak Memory', $memory['used_memory_peak_human'] ?? 'N/A'],
                    ['RSS Memory', $memory['used_memory_rss_human'] ?? 'N/A'],
                ]
            );
        }

        $this->newLine();
        $this->info('Redis is ready to use! 🚀');

        return 0;
    }

    /**
     * Format uptime seconds to human readable
     */
    private function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $parts = [];
        if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
        if ($hours > 0) $parts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        if ($minutes > 0) $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        
        return implode(', ', $parts) ?: 'Less than a minute';
    }
}

