<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Helpers\CacheHelper;

class PrayerTimeService
{
    private const CACHE_KEY = 'prayer_times_kemenag_pesisir_barat_v2';
    private const LAST_GOOD_PREFIX = 'prayer_times_kemenag_last_good_';
    private const CACHE_DURATION = 21600; // 6 jam

    /** Kode kota myQuran (data Kemenag Bimas Islam): Kab. Pesisir Barat */
    private const CITY_ID = '1008';
    private const API_BASE = 'https://api.myquran.com/v2/sholat/jadwal';

    /**
     * Get prayer times for Pesisir Barat (sumber: Kemenag via myQuran).
     */
    public function getPrayerTimes($date = null)
    {
        $date = $date ?: now()->format('Y-m-d');
        $cacheKey = self::CACHE_KEY . '_' . $date;

        return CacheHelper::remember(
            $cacheKey,
            self::CACHE_DURATION,
            function () use ($date) {
                return $this->fetchFromKemenag($date);
            }
        );
    }

    /**
     * Fetch jadwal from myQuran API (scraped from Kemenag Bimas Islam).
     * Includes imsak, terbit, dhuha.
     */
    private function fetchFromKemenag(string $date): array
    {
        try {
            $pathDate = str_replace('-', '/', $date); // YYYY/MM/DD
            $response = Http::timeout(12)
                ->acceptJson()
                ->get(self::API_BASE . '/' . self::CITY_ID . '/' . $pathDate);

            if (!$response->successful()) {
                Log::warning('Kemenag prayer HTTP ' . $response->status());
                return $this->lastGoodOrUnavailable($date, 'HTTP ' . $response->status());
            }

            $payload = $response->json();
            if (empty($payload['status']) || empty($payload['data']['jadwal'])) {
                return $this->lastGoodOrUnavailable($date, 'Respons tidak valid');
            }

            $jadwal = $payload['data']['jadwal'];
            $parsed = [
                'date' => $jadwal['date'] ?? $date,
                'location' => $payload['data']['lokasi'] ?? 'Kab. Pesisir Barat',
                'region' => $payload['data']['daerah'] ?? 'Lampung',
                'source' => 'Kemenag',
                'prayers' => [
                    'imsak' => $jadwal['imsak'] ?? '--:--',
                    'fajr' => $jadwal['subuh'] ?? '--:--',
                    'sunrise' => $jadwal['terbit'] ?? '--:--',
                    'dhuha' => $jadwal['dhuha'] ?? '--:--',
                    'dhuhr' => $jadwal['dzuhur'] ?? '--:--',
                    'asr' => $jadwal['ashar'] ?? '--:--',
                    'maghrib' => $jadwal['maghrib'] ?? '--:--',
                    'isha' => $jadwal['isya'] ?? '--:--',
                ],
                'available' => true,
                'updated_at' => now()->format('H:i'),
            ];

            Cache::put(self::LAST_GOOD_PREFIX . $date, $parsed, now()->addDays(2));

            return $parsed;
        } catch (\Exception $e) {
            Log::error('Kemenag Prayer API Error: ' . $e->getMessage());
            return $this->lastGoodOrUnavailable($date, $e->getMessage());
        }
    }

    private function lastGoodOrUnavailable(string $date, string $reason): array
    {
        $last = Cache::get(self::LAST_GOOD_PREFIX . $date);
        if (is_array($last) && !empty($last['available'])) {
            $last['updated_at'] = ($last['updated_at'] ?? '') . ' (cache)';
            return $last;
        }

        Log::warning('Kemenag prayer unavailable: ' . $reason);

        return [
            'date' => $date,
            'location' => 'Kab. Pesisir Barat',
            'region' => 'Lampung',
            'source' => 'Kemenag',
            'prayers' => [
                'imsak' => '--:--',
                'fajr' => '--:--',
                'sunrise' => '--:--',
                'dhuha' => '--:--',
                'dhuhr' => '--:--',
                'asr' => '--:--',
                'maghrib' => '--:--',
                'isha' => '--:--',
            ],
            'available' => false,
            'updated_at' => now()->format('H:i'),
        ];
    }

    /**
     * Get next prayer time (5 waktu wajib).
     */
    public function getNextPrayer()
    {
        $prayerTimes = $this->getPrayerTimes();
        $currentTime = now()->format('H:i');

        $prayers = [
            'Subuh' => $prayerTimes['prayers']['fajr'],
            'Dzuhur' => $prayerTimes['prayers']['dhuhr'],
            'Ashar' => $prayerTimes['prayers']['asr'],
            'Maghrib' => $prayerTimes['prayers']['maghrib'],
            'Isya' => $prayerTimes['prayers']['isha'],
        ];

        foreach ($prayers as $name => $time) {
            if ($time !== '--:--' && $time > $currentTime) {
                return [
                    'name' => $name,
                    'time' => $time,
                    'remaining' => $this->getTimeRemaining($currentTime, $time),
                ];
            }
        }

        $tomorrowPrayers = $this->getPrayerTimes(now()->addDay()->format('Y-m-d'));

        return [
            'name' => 'Subuh',
            'time' => $tomorrowPrayers['prayers']['fajr'],
            'remaining' => 'Besok',
        ];
    }

    private function getTimeRemaining($currentTime, $prayerTime)
    {
        $current = strtotime($currentTime);
        $prayer = strtotime($prayerTime);

        $diff = $prayer - $current;
        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}j {$minutes}m";
        }

        return "{$minutes}m";
    }
}
