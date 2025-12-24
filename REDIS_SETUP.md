# Redis Integration Guide

## Overview

Redis telah diintegrasikan ke dalam aplikasi untuk meningkatkan performa caching, session management, dan queue processing.

## Instalasi

### Windows (XAMPP)

1. **Download Redis untuk Windows:**
   - Download dari: https://github.com/microsoftarchive/redis/releases
   - Atau gunakan WSL (Windows Subsystem for Linux)

2. **Install Redis:**
   ```bash
   # Via WSL
   sudo apt-get update
   sudo apt-get install redis-server
   sudo service redis-server start
   ```

3. **Install PHP Redis Extension:**
   - Download dari: https://pecl.php.net/package/redis
   - Atau install via PECL:
   ```bash
   pecl install redis
   ```

### Linux/Mac

```bash
# Install Redis
sudo apt-get install redis-server  # Ubuntu/Debian
brew install redis                  # Mac

# Start Redis
sudo service redis-server start     # Ubuntu/Debian
brew services start redis           # Mac

# Install PHP Redis Extension
sudo apt-get install php-redis      # Ubuntu/Debian
pecl install redis                 # Mac
```

## Konfigurasi

### 1. Update `.env` file

```env
# Cache Configuration
CACHE_DRIVER=redis

# Session Configuration
SESSION_DRIVER=redis

# Queue Configuration
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=phpredis

# Redis Queue Configuration
REDIS_QUEUE_CONNECTION=default
REDIS_QUEUE=default
```

### 2. Test Redis Connection

```bash
php artisan redis:test
```

## Penggunaan

### Caching dengan Redis

```php
use Illuminate\Support\Facades\Cache;

// Cache dengan Redis
Cache::remember('key', 3600, function () {
    return 'value';
});

// Clear cache
Cache::forget('key');
Cache::flush();
```

### Session dengan Redis

Session akan otomatis menggunakan Redis jika `SESSION_DRIVER=redis` di `.env`.

### Queue dengan Redis

```bash
# Start queue worker
php artisan queue:work redis

# Process specific queue
php artisan queue:work redis --queue=high,default
```

## Helper Functions

### RedisHelper

```php
use App\Helpers\RedisHelper;

// Check if Redis is available
if (RedisHelper::isAvailable()) {
    // Redis is ready
}

// Get Redis info
$info = RedisHelper::getInfo();

// Get cache statistics
$stats = RedisHelper::getCacheStats();

// Clear cache by pattern
RedisHelper::clearByPattern('articles:*');

// Get memory usage
$memory = RedisHelper::getMemoryUsage();
```

## Monitoring

### Check Redis Status

```bash
# Via command
php artisan redis:test

# Via Redis CLI
redis-cli ping
# Should return: PONG

# Get Redis info
redis-cli info
```

### Monitor Redis Commands

```bash
redis-cli monitor
```

## Troubleshooting

### Redis tidak bisa connect

1. **Check Redis service:**
   ```bash
   # Linux
   sudo service redis-server status
   
   # Windows (WSL)
   wsl redis-cli ping
   ```

2. **Check configuration:**
   - Pastikan `REDIS_HOST` dan `REDIS_PORT` benar
   - Pastikan Redis server berjalan

3. **Check PHP extension:**
   ```bash
   php -m | grep redis
   ```

### Fallback ke File Cache

Jika Redis tidak tersedia, aplikasi akan otomatis fallback ke file cache. Pastikan:
- `CACHE_DRIVER=file` di `.env` sebagai fallback
- Folder `storage/framework/cache` writable

## Performance Benefits

1. **Faster Cache Access:** Redis lebih cepat daripada file-based cache
2. **Better Session Management:** Session tersimpan di memory
3. **Efficient Queue Processing:** Queue processing lebih cepat dengan Redis
4. **Scalability:** Redis mendukung clustering dan replication

## Best Practices

1. **Set appropriate TTL:** Jangan cache terlalu lama
2. **Use cache tags:** Untuk invalidation yang lebih baik
3. **Monitor memory:** Redis menggunakan RAM, monitor usage
4. **Backup regularly:** Backup Redis data jika diperlukan

## Production Setup

Untuk production, pertimbangkan:

1. **Redis Persistence:**
   ```conf
   # redis.conf
   save 900 1
   save 300 10
   save 60 10000
   ```

2. **Redis Password:**
   ```env
   REDIS_PASSWORD=your_secure_password
   ```

3. **Redis Replication:** Setup master-slave untuk high availability

4. **Monitoring:** Setup monitoring tools (RedisInsight, etc.)

## Command Reference

```bash
# Test Redis connection
php artisan redis:test

# Clear all cache
php artisan cache:clear

# Clear specific cache pattern (via RedisHelper)
# Use in code: RedisHelper::clearByPattern('pattern:*')
```

## Support

Jika ada masalah dengan Redis setup, check:
- Laravel Redis Documentation: https://laravel.com/docs/redis
- Redis Official Docs: https://redis.io/documentation

