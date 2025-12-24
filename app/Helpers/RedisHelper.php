<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RedisHelper
{
    /**
     * Check if Redis is available
     */
    public static function isAvailable(): bool
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            Log::warning('Redis is not available: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Redis connection info
     */
    public static function getInfo(): array
    {
        try {
            if (!self::isAvailable()) {
                return ['status' => 'unavailable'];
            }

            $info = Redis::info();
            
            return [
                'status' => 'available',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory_human' => $info['used_memory_human'] ?? '0B',
                'used_memory_peak_human' => $info['used_memory_peak_human'] ?? '0B',
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'uptime_in_seconds' => $info['uptime_in_seconds'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Redis info: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Clear cache by pattern
     */
    public static function clearByPattern(string $pattern): int
    {
        try {
            if (!self::isAvailable()) {
                return 0;
            }

            $keys = Redis::keys($pattern);
            if (empty($keys)) {
                return 0;
            }

            return Redis::del($keys);
        } catch (\Exception $e) {
            Log::error('Failed to clear Redis cache by pattern: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        try {
            if (!self::isAvailable()) {
                return ['available' => false];
            }

            $info = Redis::info('stats');
            $keyspace = Redis::info('keyspace');
            
            $dbStats = [];
            foreach ($keyspace as $key => $value) {
                if (strpos($key, 'db') === 0) {
                    preg_match('/keys=(\d+),expires=(\d+)/', $value, $matches);
                    $dbStats[$key] = [
                        'keys' => $matches[1] ?? 0,
                        'expires' => $matches[2] ?? 0,
                    ];
                }
            }

            return [
                'available' => true,
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => self::calculateHitRate($info['keyspace_hits'] ?? 0, $info['keyspace_misses'] ?? 0),
                'databases' => $dbStats,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Redis cache stats: ' . $e->getMessage());
            return ['available' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Calculate cache hit rate
     */
    private static function calculateHitRate(int $hits, int $misses): float
    {
        $total = $hits + $misses;
        if ($total === 0) {
            return 0.0;
        }
        
        return round(($hits / $total) * 100, 2);
    }

    /**
     * Flush all cache (use with caution!)
     */
    public static function flushAll(): bool
    {
        try {
            if (!self::isAvailable()) {
                return false;
            }

            Redis::flushAll();
            Log::warning('Redis cache flushed - all keys deleted');
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to flush Redis cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get memory usage
     */
    public static function getMemoryUsage(): array
    {
        try {
            if (!self::isAvailable()) {
                return ['available' => false];
            }

            $info = Redis::info('memory');
            
            return [
                'available' => true,
                'used_memory' => $info['used_memory'] ?? 0,
                'used_memory_human' => $info['used_memory_human'] ?? '0B',
                'used_memory_peak' => $info['used_memory_peak'] ?? 0,
                'used_memory_peak_human' => $info['used_memory_peak_human'] ?? '0B',
                'used_memory_rss' => $info['used_memory_rss'] ?? 0,
                'used_memory_rss_human' => self::formatBytes($info['used_memory_rss'] ?? 0),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Redis memory usage: ' . $e->getMessage());
            return ['available' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Format bytes to human readable
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

