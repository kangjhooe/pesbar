<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    private const CACHE_KEY = 'weather_bmkg_pasar_krui_v2';
    private const LAST_GOOD_KEY = 'weather_bmkg_pasar_krui_last_good';
    private const CACHE_DURATION = 1800; // 30 menit

    /** Kode adm4 BMKG: Pasar Krui, Kec. Pesisir Tengah, Kab. Pesisir Barat */
    private const ADM4 = '18.13.01.1005';
    private const API_URL = 'https://api.bmkg.go.id/publik/prakiraan-cuaca';

    /**
     * Get weather data for Pesisir Barat (sumber: BMKG saja).
     */
    public function getWeatherData()
    {
        return CacheHelper::remember(
            self::CACHE_KEY,
            self::CACHE_DURATION,
            function () {
                return $this->fetchWeatherFromBMKG();
            }
        );
    }

    /**
     * Fetch from BMKG Open Data API (JSON per kelurahan/desa).
     * @see https://data.bmkg.go.id/prakiraan-cuaca/
     */
    private function fetchWeatherFromBMKG()
    {
        try {
            $response = Http::timeout(12)
                ->acceptJson()
                ->get(self::API_URL, ['adm4' => self::ADM4]);

            if (!$response->successful()) {
                Log::warning('BMKG weather HTTP ' . $response->status());
                return $this->lastGoodOrUnavailable('HTTP ' . $response->status());
            }

            $payload = $response->json();
            $parsed = $this->parseBMKGJson($payload);

            if ($parsed === null) {
                return $this->lastGoodOrUnavailable('Parse gagal');
            }

            Cache::put(self::LAST_GOOD_KEY, $parsed, now()->addDays(2));

            return $parsed;
        } catch (\Exception $e) {
            Log::error('BMKG Weather API Error: ' . $e->getMessage());
            return $this->lastGoodOrUnavailable($e->getMessage());
        }
    }

    private function parseBMKGJson(?array $payload): ?array
    {
        $entry = $payload['data'][0] ?? null;
        if (!$entry || empty($entry['cuaca']) || !is_array($entry['cuaca'])) {
            return null;
        }

        $lokasi = $entry['lokasi'] ?? [];
        $days = $entry['cuaca'];

        $current = $this->pickCurrentSlot($days);
        if (!$current) {
            return null;
        }

        $weatherCode = (string) ($current['weather'] ?? '');
        $condition = $current['weather_desc'] ?? $this->mapWeatherCondition($weatherCode);

        return [
            'temperature' => (int) round((float) ($current['t'] ?? 0)),
            'humidity' => (int) ($current['hu'] ?? 0),
            'wind_speed' => isset($current['ws']) ? round((float) $current['ws'], 1) : null,
            'wind_direction' => $this->mapWindDirection($current['wd'] ?? null),
            'condition' => $condition,
            'icon' => $this->getWeatherIcon($weatherCode, $condition),
            'location' => trim(($lokasi['desa'] ?? 'Pasar Krui') . ', Pesisir Barat'),
            'source' => 'BMKG',
            'updated_at' => now()->format('H:i'),
            'available' => true,
            'forecast' => $this->buildDailyForecast($days),
        ];
    }

    /**
     * Pilih slot prakiraan terdekat dengan waktu sekarang.
     */
    private function pickCurrentSlot(array $days): ?array
    {
        $now = now();
        $best = null;
        $bestDiff = PHP_INT_MAX;

        foreach ($days as $daySlots) {
            if (!is_array($daySlots)) {
                continue;
            }
            foreach ($daySlots as $slot) {
                $local = $slot['local_datetime'] ?? null;
                if (!$local) {
                    continue;
                }
                try {
                    $slotTime = \Carbon\Carbon::parse($local);
                } catch (\Exception $e) {
                    continue;
                }
                $diff = abs($slotTime->diffInSeconds($now));
                if ($diff < $bestDiff) {
                    $bestDiff = $diff;
                    $best = $slot;
                }
            }
        }

        // Fallback: slot pertama hari ini
        if (!$best && !empty($days[0][0])) {
            $best = $days[0][0];
        }

        return $best;
    }

    private function buildDailyForecast(array $days): array
    {
        $forecast = [];
        $today = now()->startOfDay();

        foreach ($days as $index => $daySlots) {
            if (!is_array($daySlots) || empty($daySlots)) {
                continue;
            }

            $temps = [];
            $midday = null;
            $middayScore = PHP_INT_MAX;

            foreach ($daySlots as $slot) {
                if (isset($slot['t'])) {
                    $temps[] = (float) $slot['t'];
                }
                $local = $slot['local_datetime'] ?? null;
                if ($local) {
                    try {
                        $hour = \Carbon\Carbon::parse($local)->hour;
                        $score = abs($hour - 12);
                        if ($score < $middayScore) {
                            $middayScore = $score;
                            $midday = $slot;
                        }
                    } catch (\Exception $e) {
                        // skip
                    }
                }
            }

            $ref = $midday ?: $daySlots[0];
            $date = null;
            if (!empty($ref['local_datetime'])) {
                try {
                    $date = \Carbon\Carbon::parse($ref['local_datetime'])->startOfDay();
                } catch (\Exception $e) {
                    $date = $today->copy()->addDays($index);
                }
            } else {
                $date = $today->copy()->addDays($index);
            }

            // Lewati hari yang sudah lewat
            if ($date->lt($today)) {
                continue;
            }

            $weatherCode = (string) ($ref['weather'] ?? '');
            $condition = $ref['weather_desc'] ?? $this->mapWeatherCondition($weatherCode);

            $forecast[] = [
                'day' => $this->getDayName($date),
                'date' => $date->format('d/m'),
                'temperature' => (int) round((float) ($ref['t'] ?? ($temps[0] ?? 28))),
                'temp_min' => !empty($temps) ? (int) round(min($temps)) : null,
                'temp_max' => !empty($temps) ? (int) round(max($temps)) : null,
                'condition' => $condition,
                'icon' => $this->getWeatherIcon($weatherCode, $condition),
            ];

            if (count($forecast) >= 3) {
                break;
            }
        }

        return $forecast;
    }

    private function lastGoodOrUnavailable(string $reason): array
    {
        $last = Cache::get(self::LAST_GOOD_KEY);
        if (is_array($last) && !empty($last['available'])) {
            $last['updated_at'] = ($last['updated_at'] ?? '') . ' (cache)';
            return $last;
        }

        Log::warning('BMKG weather unavailable: ' . $reason);

        return [
            'temperature' => null,
            'humidity' => null,
            'wind_speed' => null,
            'wind_direction' => null,
            'condition' => 'Data BMKG tidak tersedia',
            'icon' => 'fas fa-cloud',
            'location' => 'Pesisir Barat',
            'source' => 'BMKG',
            'updated_at' => now()->format('H:i'),
            'available' => false,
            'forecast' => [],
        ];
    }

    private function mapWindDirection(?string $wd): ?string
    {
        $map = [
            'N' => 'Utara',
            'NE' => 'Timur Laut',
            'E' => 'Timur',
            'SE' => 'Tenggara',
            'S' => 'Selatan',
            'SW' => 'Barat Daya',
            'W' => 'Barat',
            'NW' => 'Barat Laut',
            'VARIABLE' => 'Berubah-ubah',
        ];

        if (!$wd) {
            return null;
        }

        return $map[strtoupper($wd)] ?? $wd;
    }

    private function mapWeatherCondition(string $weatherCode): string
    {
        $conditions = [
            '0' => 'Cerah',
            '1' => 'Cerah Berawan',
            '2' => 'Cerah Berawan',
            '3' => 'Berawan',
            '4' => 'Berawan Tebal',
            '5' => 'Udara Kabur',
            '10' => 'Asap',
            '45' => 'Kabut',
            '60' => 'Hujan Ringan',
            '61' => 'Hujan Sedang',
            '63' => 'Hujan Lebat',
            '80' => 'Hujan Lokal',
            '95' => 'Hujan Petir',
            '97' => 'Hujan Petir',
        ];

        return $conditions[$weatherCode] ?? 'Cerah';
    }

    private function getWeatherIcon(string $weatherCode, ?string $condition = null): string
    {
        $icons = [
            '0' => 'fas fa-sun',
            '1' => 'fas fa-cloud-sun',
            '2' => 'fas fa-cloud-sun',
            '3' => 'fas fa-cloud',
            '4' => 'fas fa-cloud',
            '5' => 'fas fa-smog',
            '10' => 'fas fa-smog',
            '45' => 'fas fa-smog',
            '60' => 'fas fa-cloud-rain',
            '61' => 'fas fa-cloud-rain',
            '63' => 'fas fa-cloud-showers-heavy',
            '80' => 'fas fa-cloud-rain',
            '95' => 'fas fa-bolt',
            '97' => 'fas fa-bolt',
        ];

        if (isset($icons[$weatherCode])) {
            return $icons[$weatherCode];
        }

        $byName = [
            'Cerah' => 'fas fa-sun',
            'Sunny' => 'fas fa-sun',
            'Cerah Berawan' => 'fas fa-cloud-sun',
            'Berawan' => 'fas fa-cloud',
            'Hujan Ringan' => 'fas fa-cloud-rain',
            'Hujan Sedang' => 'fas fa-cloud-rain',
            'Hujan Lebat' => 'fas fa-cloud-showers-heavy',
            'Hujan Petir' => 'fas fa-bolt',
        ];

        return $byName[$condition ?? ''] ?? 'fas fa-cloud-sun';
    }

    private function getDayName($date): string
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return $days[$date->dayOfWeek] ?? $date->format('l');
    }
}
