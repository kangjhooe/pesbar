<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Helpers\CacheHelper;
use Carbon\Carbon;

class MaritimeService
{
    private const CACHE_KEY = 'maritime_pesisir_barat';
    private const CACHE_DURATION = 1800; // 30 menit
    
    // Koordinat Pesisir Barat, Lampung
    private const LATITUDE = -5.1167;
    private const LONGITUDE = 103.9500;
    
    /**
     * Get maritime data for Pesisir Barat
     */
    public function getMaritimeData()
    {
        return CacheHelper::remember(
            self::CACHE_KEY,
            self::CACHE_DURATION,
            function () {
                return $this->fetchMaritimeFromBMKG();
            }
        );
    }
    
    /**
     * Fetch maritime data from BMKG API
     */
    private function fetchMaritimeFromBMKG()
    {
        try {
            // BMKG API untuk prakiraan gelombang
            $response = Http::timeout(10)->get('https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-Lampung.xml');
            
            if ($response->successful()) {
                $data = $this->parseBMKGMaritimeResponse($response->body());
                if ($data) {
                    return $data;
                }
            }
            
            // Fallback ke data estimasi jika API gagal
            return $this->getFallbackMaritimeData();
            
        } catch (\Exception $e) {
            \Log::error('Maritime API Error: ' . $e->getMessage());
            return $this->getFallbackMaritimeData();
        }
    }
    
    /**
     * Parse BMKG XML response for maritime data
     */
    private function parseBMKGMaritimeResponse($xmlData)
    {
        try {
            $xml = simplexml_load_string($xmlData);
            
            if ($xml === false) {
                return null;
            }
            
            // Cari data untuk Pesisir Barat (area ID: 1801)
            $area = $xml->xpath("//area[@id='1801']")[0] ?? null;
            
            if ($area) {
                $parameter = $area->parameter;
                
                // Ambil data gelombang (jika tersedia dalam XML)
                // BMKG biasanya menyediakan data gelombang dalam parameter terpisah
                $waveHeight = $this->extractParameterValue($parameter, 'wave', 0);
                $windSpeed = $this->extractParameterValue($parameter, 'ws', 0);
                $windDirection = $this->extractParameterValue($parameter, 'wd', 0);
                
                // Jika tidak ada data gelombang langsung, gunakan estimasi berdasarkan cuaca
                if (!$waveHeight) {
                    $weather = $this->extractParameterValue($parameter, 'weather', 0);
                    $waveHeight = $this->estimateWaveHeight($weather, $windSpeed);
                }
                
                // Hitung pasang surut berdasarkan koordinat
                $tideData = $this->calculateTideData();
                
                // Tentukan status peringatan
                $warning = $this->determineWarning($waveHeight, $windSpeed, $weather ?? null);
                
                return [
                    'wave_height' => round($waveHeight, 1),
                    'wave_height_category' => $this->getWaveCategory($waveHeight),
                    'wind_speed' => $windSpeed ?: $this->estimateWindSpeed(),
                    'wind_direction' => $windDirection ?: $this->estimateWindDirection(),
                    'tide' => $tideData,
                    'warning' => $warning,
                    'location' => 'Pesisir Barat',
                    'source' => 'BMKG',
                    'updated_at' => now()->format('H:i'),
                    'forecast' => $this->getMaritimeForecast($parameter)
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            \Log::error('BMKG Maritime Parse Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Extract parameter value from BMKG XML
     */
    private function extractParameterValue($parameter, $type, $index = 0)
    {
        $param = null;
        foreach ($parameter->children() as $child) {
            if ((string)$child['id'] === $type) {
                $param = $child;
                break;
            }
        }
        
        if ($param && isset($param->timerange[$index])) {
            $value = $param->timerange[$index]->value;
            return (float) $value;
        }
        
        return null;
    }
    
    /**
     * Estimate wave height based on weather and wind
     */
    private function estimateWaveHeight($weatherCode, $windSpeed)
    {
        // Estimasi tinggi gelombang berdasarkan kondisi cuaca dan kecepatan angin
        $baseWave = 0.5; // Gelombang dasar
        
        if ($windSpeed) {
            // Kecepatan angin mempengaruhi tinggi gelombang
            if ($windSpeed > 30) {
                $baseWave = 2.5; // Ombak besar
            } elseif ($windSpeed > 20) {
                $baseWave = 1.5; // Ombak sedang
            } elseif ($windSpeed > 10) {
                $baseWave = 1.0; // Ombak kecil
            }
        }
        
        // Kondisi cuaca juga mempengaruhi
        if (in_array($weatherCode, ['63', '80', '95', '97'])) {
            // Hujan lebat atau petir = ombak lebih besar
            $baseWave += 0.5;
        }
        
        return max(0.3, min(4.0, $baseWave)); // Batasi antara 0.3 - 4.0 meter
    }
    
    /**
     * Get wave category
     */
    private function getWaveCategory($height)
    {
        if ($height >= 3.0) {
            return 'Sangat Tinggi';
        } elseif ($height >= 2.0) {
            return 'Tinggi';
        } elseif ($height >= 1.0) {
            return 'Sedang';
        } else {
            return 'Tenang';
        }
    }
    
    /**
     * Calculate tide data based on location and date
     * Untuk Pesisir Barat, Lampung (-5.1167°S, 103.9500°E)
     * Menggunakan perhitungan pasang surut semi-diurnal (2 kali pasang, 2 kali surut per hari)
     */
    private function calculateTideData()
    {
        $now = Carbon::now('Asia/Jakarta');
        $hour = $now->hour;
        $minute = $now->minute;
        $dayOfYear = $now->dayOfYear;
        
        // Siklus pasang surut semi-diurnal: 12 jam 25 menit (745 menit)
        // Waktu pasang surut bergeser sekitar 50 menit setiap hari karena pengaruh bulan
        $tideCycleMinutes = 745; // 12 jam 25 menit
        $dailyOffset = ($dayOfYear * 50) % $tideCycleMinutes; // Pergeseran harian
        
        // Waktu dalam menit sejak tengah malam
        $currentMinutes = ($hour * 60) + $minute;
        
        // Hitung posisi dalam siklus pasang surut (0-745 menit)
        $tidePosition = ($currentMinutes + $dailyOffset) % $tideCycleMinutes;
        
        // Untuk Pesisir Barat, pola pasang surut:
        // Pasang 1: sekitar jam 06:00-08:00 (360-480 menit)
        // Surut 1: sekitar jam 12:00-14:00 (720-840 menit, tapi karena siklus, ini sekitar 0-120 menit)
        // Pasang 2: sekitar jam 18:00-20:00 (1080-1200 menit, dalam siklus = 335-455 menit)
        // Surut 2: sekitar jam 00:00-02:00 (0-120 menit)
        
        // Normalisasi posisi untuk perhitungan yang lebih akurat
        $normalizedPosition = $tidePosition;
        if ($normalizedPosition > $tideCycleMinutes / 2) {
            $normalizedPosition = $tideCycleMinutes - $normalizedPosition;
        }
        
        // Hitung level pasang surut menggunakan fungsi sinusoidal
        // Pasang tertinggi di tengah siklus, surut terendah di awal/akhir siklus
        $tideLevel = 0.5 + (0.5 * cos(($tidePosition / $tideCycleMinutes) * 2 * pi()));
        
        // Tentukan status pasang/surut
        // Pasang jika level > 0.5, surut jika level < 0.5
        $isHighTide = $tideLevel > 0.5;
        
        // Hitung waktu pasang/surut berikutnya
        $nextHighTide = $this->calculateNextTideTime(true, $currentMinutes, $dailyOffset, $tideCycleMinutes);
        $nextLowTide = $this->calculateNextTideTime(false, $currentMinutes, $dailyOffset, $tideCycleMinutes);
        
        return [
            'status' => $isHighTide ? 'Pasang' : 'Surut',
            'level' => round($tideLevel * 100),
            'next_high_tide' => $nextHighTide,
            'next_low_tide' => $nextLowTide,
            'icon' => $isHighTide ? 'fas fa-arrow-up' : 'fas fa-arrow-down'
        ];
    }
    
    /**
     * Calculate next tide time based on current time and tide cycle
     * Untuk Pesisir Barat, Lampung
     * Menggunakan perhitungan berdasarkan siklus pasang surut semi-diurnal
     */
    private function calculateNextTideTime($isHighTide, $currentMinutes, $dailyOffset, $tideCycleMinutes)
    {
        // Hitung posisi dalam siklus saat ini
        $currentTidePosition = ($currentMinutes + $dailyOffset) % $tideCycleMinutes;
        
        // Cari waktu pasang/surut berikutnya dengan mencari titik ekstrem
        $nextTidePosition = null;
        $bestLevel = $isHighTide ? 0 : 1; // Untuk pasang cari max, untuk surut cari min
        
        // Cari dalam 1 siklus penuh (745 menit)
        for ($offset = 1; $offset <= $tideCycleMinutes; $offset++) {
            $testPosition = ($currentTidePosition + $offset) % $tideCycleMinutes;
            $testLevel = 0.5 + (0.5 * cos(($testPosition / $tideCycleMinutes) * 2 * pi()));
            
            if ($isHighTide) {
                // Cari puncak pasang (level tertinggi, mendekati 1.0)
                if ($testLevel > 0.7 && $testLevel > $bestLevel) {
                    $bestLevel = $testLevel;
                    $nextTidePosition = $testPosition;
                    // Jika sudah cukup tinggi (>0.9), anggap sudah menemukan puncak
                    if ($testLevel > 0.9) {
                        break;
                    }
                }
            } else {
                // Cari titik surut terendah (level terendah, mendekati 0.0)
                if ($testLevel < 0.3 && $testLevel < $bestLevel) {
                    $bestLevel = $testLevel;
                    $nextTidePosition = $testPosition;
                    // Jika sudah cukup rendah (<0.1), anggap sudah menemukan titik terendah
                    if ($testLevel < 0.1) {
                        break;
                    }
                }
            }
        }
        
        // Jika tidak ditemukan, gunakan estimasi berdasarkan pola rata-rata
        if ($nextTidePosition === null) {
            // Untuk pasang: sekitar 6 jam 12 menit dari sekarang (setengah siklus)
            // Untuk surut: sekitar 6 jam 12 menit dari sekarang (setengah siklus)
            $nextTidePosition = ($currentTidePosition + ($tideCycleMinutes / 2)) % $tideCycleMinutes;
        }
        
        // Hitung menit dari tengah malam untuk waktu berikutnya
        $nextTideMinutes = ($currentMinutes + ($nextTidePosition - $currentTidePosition + $tideCycleMinutes) % $tideCycleMinutes);
        
        // Jika waktu berikutnya lebih kecil dari waktu sekarang, berarti besok
        if ($nextTideMinutes < $currentMinutes) {
            $nextTideMinutes += 1440; // Tambah 24 jam (1440 menit)
        }
        
        // Konversi ke jam:menit (dalam 24 jam)
        $nextHour = floor($nextTideMinutes / 60) % 24;
        $nextMinute = $nextTideMinutes % 60;
        
        // Format waktu
        return sprintf('%02d:%02d', $nextHour, $nextMinute);
    }
    
    /**
     * Determine warning status
     */
    private function determineWarning($waveHeight, $windSpeed, $weatherCode = null)
    {
        $warnings = [];
        
        if ($waveHeight >= 3.0) {
            $warnings[] = [
                'level' => 'danger',
                'message' => 'Ombak Sangat Tinggi',
                'icon' => 'fas fa-exclamation-triangle'
            ];
        } elseif ($waveHeight >= 2.0) {
            $warnings[] = [
                'level' => 'warning',
                'message' => 'Ombak Tinggi',
                'icon' => 'fas fa-exclamation-circle'
            ];
        }
        
        if ($windSpeed && $windSpeed > 30) {
            $warnings[] = [
                'level' => 'danger',
                'message' => 'Angin Kencang',
                'icon' => 'fas fa-wind'
            ];
        }
        
        if (in_array($weatherCode, ['95', '97'])) {
            $warnings[] = [
                'level' => 'danger',
                'message' => 'Cuaca Ekstrem',
                'icon' => 'fas fa-bolt'
            ];
        }
        
        return $warnings;
    }
    
    /**
     * Get maritime forecast
     */
    private function getMaritimeForecast($parameter)
    {
        $forecast = [];
        $today = Carbon::now()->startOfDay();
        
        for ($day = 1; $day <= 3; $day++) {
            $targetDate = $today->copy()->addDays($day);
            $timerangeIndex = ($day * 4) + 2;
            
            $windSpeed = $this->extractParameterValue($parameter, 'ws', $timerangeIndex);
            $weather = $this->extractParameterValue($parameter, 'weather', $timerangeIndex);
            
            $waveHeight = $this->estimateWaveHeight($weather, $windSpeed);
            
            $forecast[] = [
                'day' => $this->getDayName($targetDate),
                'date' => $targetDate->format('d/m'),
                'wave_height' => round($waveHeight, 1),
                'wave_category' => $this->getWaveCategory($waveHeight),
                'wind_speed' => $windSpeed ?: $this->estimateWindSpeed(),
                'icon' => $this->getWaveIcon($waveHeight)
            ];
        }
        
        return $forecast;
    }
    
    /**
     * Get day name in Indonesian
     */
    private function getDayName($date)
    {
        $days = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 
            'Kamis', 'Jumat', 'Sabtu'
        ];
        
        return $days[$date->dayOfWeek] ?? $date->format('l');
    }
    
    /**
     * Get wave icon based on height
     */
    private function getWaveIcon($height)
    {
        if ($height >= 3.0) {
            return 'fas fa-water text-red-500';
        } elseif ($height >= 2.0) {
            return 'fas fa-water text-orange-500';
        } elseif ($height >= 1.0) {
            return 'fas fa-water text-yellow-500';
        } else {
            return 'fas fa-water text-blue-500';
        }
    }
    
    /**
     * Estimate wind speed (fallback)
     */
    private function estimateWindSpeed()
    {
        // Estimasi berdasarkan waktu (angin biasanya lebih kencang siang hari)
        $hour = Carbon::now()->hour;
        if ($hour >= 10 && $hour <= 16) {
            return rand(15, 25);
        } else {
            return rand(8, 18);
        }
    }
    
    /**
     * Estimate wind direction (fallback)
     */
    private function estimateWindDirection()
    {
        $directions = ['Timur', 'Tenggara', 'Selatan', 'Barat Daya', 'Barat', 'Barat Laut', 'Utara', 'Timur Laut'];
        return $directions[array_rand($directions)];
    }
    
    /**
     * Fallback maritime data when API fails
     */
    private function getFallbackMaritimeData()
    {
        $waveHeight = rand(8, 20) / 10; // 0.8 - 2.0 meter
        $windSpeed = rand(12, 22);
        
        $tideData = $this->calculateTideData();
        $warning = $this->determineWarning($waveHeight, $windSpeed);
        
        // Generate forecast
        $forecast = [];
        $today = Carbon::now()->startOfDay();
        
        for ($day = 1; $day <= 3; $day++) {
            $targetDate = $today->copy()->addDays($day);
            $forecastWave = rand(8, 25) / 10;
            
            $forecast[] = [
                'day' => $this->getDayName($targetDate),
                'date' => $targetDate->format('d/m'),
                'wave_height' => round($forecastWave, 1),
                'wave_category' => $this->getWaveCategory($forecastWave),
                'wind_speed' => rand(10, 20),
                'icon' => $this->getWaveIcon($forecastWave)
            ];
        }
        
        return [
            'wave_height' => round($waveHeight, 1),
            'wave_height_category' => $this->getWaveCategory($waveHeight),
            'wind_speed' => $windSpeed,
            'wind_direction' => $this->estimateWindDirection(),
            'tide' => $tideData,
            'warning' => $warning,
            'location' => 'Pesisir Barat',
            'source' => 'Estimasi',
            'updated_at' => now()->format('H:i'),
            'forecast' => $forecast
        ];
    }
}

