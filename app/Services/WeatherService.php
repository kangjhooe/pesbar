<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Helpers\CacheHelper;

class WeatherService
{
    private const CACHE_KEY = 'weather_pesisir_barat';
    private const CACHE_DURATION = 1800; // 30 menit
    
    // Koordinat Pesisir Barat, Lampung
    private const LATITUDE = -5.1167;
    private const LONGITUDE = 103.9500;
    
    /**
     * Get weather data for Pesisir Barat
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
     * Fetch weather data from BMKG API
     */
    private function fetchWeatherFromBMKG()
    {
        try {
            // BMKG API untuk cuaca wilayah Lampung
            $response = Http::timeout(10)->get('https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-Lampung.xml');
            
            if ($response->successful()) {
                return $this->parseBMKGResponse($response->body());
            }
            
            // Fallback ke data statis jika API gagal
            return $this->getFallbackWeatherData();
            
        } catch (\Exception $e) {
            \Log::error('Weather API Error: ' . $e->getMessage());
            return $this->getFallbackWeatherData();
        }
    }
    
    /**
     * Parse BMKG XML response
     */
    private function parseBMKGResponse($xmlData)
    {
        try {
            $xml = simplexml_load_string($xmlData);
            
            if ($xml === false) {
                return $this->getFallbackWeatherData();
            }
            
            // Cari data untuk Pesisir Barat (area ID: 1801)
            $area = $xml->xpath("//area[@id='1801']")[0] ?? null;
            
            if ($area) {
                $parameter = $area->parameter;
                
                // Ambil data cuaca hari ini
                $temperature = $this->extractParameterValue($parameter, 't', 0); // Suhu
                $humidity = $this->extractParameterValue($parameter, 'hu', 0); // Kelembaban
                $weather = $this->extractParameterValue($parameter, 'weather', 0); // Kondisi cuaca
                
                // Ambil data prakiraan cuaca untuk 3 hari ke depan
                $forecast = $this->getWeatherForecast($parameter);
                
                return [
                    'temperature' => $temperature ?: 28,
                    'humidity' => $humidity ?: 75,
                    'condition' => $this->mapWeatherCondition($weather),
                    'icon' => $this->getWeatherIcon($weather),
                    'location' => 'Pesisir Barat',
                    'source' => 'BMKG',
                    'updated_at' => now()->format('H:i'),
                    'forecast' => $forecast
                ];
            }
            
            return $this->getFallbackWeatherData();
            
        } catch (\Exception $e) {
            \Log::error('BMKG Parse Error: ' . $e->getMessage());
            return $this->getFallbackWeatherData();
        }
    }
    
    /**
     * Extract parameter value from BMKG XML
     */
    private function extractParameterValue($parameter, $type, $index = 0)
    {
        // Cari parameter dengan ID yang sesuai dalam parent parameter
        $param = null;
        foreach ($parameter->children() as $child) {
            if ((string)$child['id'] === $type) {
                $param = $child;
                break;
            }
        }
        
        if ($param && isset($param->timerange[$index])) {
            $value = $param->timerange[$index]->value;
            return (string) $value;
        }
        
        return null;
    }
    
    /**
     * Get weather forecast for next 3 days
     */
    private function getWeatherForecast($parameter)
    {
        $forecast = [];
        $today = now()->startOfDay();
        
        // Ambil data untuk 3 hari ke depan
        for ($day = 1; $day <= 3; $day++) {
            $targetDate = $today->copy()->addDays($day);
            
            // Cari timerange yang sesuai dengan tanggal target (biasanya interval 6 jam)
            // Ambil data untuk siang hari (index sekitar 2-3 untuk hari berikutnya)
            $timerangeIndex = ($day * 4) + 2; // Estimasi index untuk siang hari
            
            $temp = $this->extractParameterValue($parameter, 't', $timerangeIndex);
            $weather = $this->extractParameterValue($parameter, 'weather', $timerangeIndex);
            
            // Jika tidak ada data, coba ambil dari index sebelumnya atau berikutnya
            if (!$temp || !$weather) {
                for ($offset = -2; $offset <= 2; $offset++) {
                    $testIndex = $timerangeIndex + $offset;
                    if ($testIndex >= 0) {
                        $testTemp = $this->extractParameterValue($parameter, 't', $testIndex);
                        $testWeather = $this->extractParameterValue($parameter, 'weather', $testIndex);
                        if ($testTemp && $testWeather) {
                            $temp = $testTemp;
                            $weather = $testWeather;
                            break;
                        }
                    }
                }
            }
            
            // Ambil suhu min dan max untuk hari tersebut
            $tempMin = $this->getMinTemperatureForDay($parameter, $day);
            $tempMax = $this->getMaxTemperatureForDay($parameter, $day);
            
            $forecast[] = [
                'day' => $this->getDayName($targetDate),
                'date' => $targetDate->format('d/m'),
                'temperature' => $temp ?: ($tempMax ?: 28),
                'temp_min' => $tempMin ?: ($temp ?: 26),
                'temp_max' => $tempMax ?: ($temp ?: 30),
                'condition' => $this->mapWeatherCondition($weather),
                'icon' => $this->getWeatherIcon($weather)
            ];
        }
        
        return $forecast;
    }
    
    /**
     * Get minimum temperature for a specific day
     */
    private function getMinTemperatureForDay($parameter, $dayOffset)
    {
        $startIndex = $dayOffset * 4;
        $endIndex = ($dayOffset + 1) * 4;
        $minTemp = null;
        
        for ($i = $startIndex; $i < $endIndex && $i < 20; $i++) {
            $temp = $this->extractParameterValue($parameter, 't', $i);
            if ($temp) {
                $temp = (int) $temp;
                if ($minTemp === null || $temp < $minTemp) {
                    $minTemp = $temp;
                }
            }
        }
        
        return $minTemp;
    }
    
    /**
     * Get maximum temperature for a specific day
     */
    private function getMaxTemperatureForDay($parameter, $dayOffset)
    {
        $startIndex = $dayOffset * 4;
        $endIndex = ($dayOffset + 1) * 4;
        $maxTemp = null;
        
        for ($i = $startIndex; $i < $endIndex && $i < 20; $i++) {
            $temp = $this->extractParameterValue($parameter, 't', $i);
            if ($temp) {
                $temp = (int) $temp;
                if ($maxTemp === null || $temp > $maxTemp) {
                    $maxTemp = $temp;
                }
            }
        }
        
        return $maxTemp;
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
     * Map BMKG weather condition to readable text
     */
    private function mapWeatherCondition($weatherCode)
    {
        $conditions = [
            '0' => 'Cerah',
            '1' => 'Cerah Berawan',
            '2' => 'Berawan',
            '3' => 'Berawan Tebal',
            '4' => 'Berawan Tebal',
            '5' => 'Udara Kabur',
            '10' => 'Asap',
            '45' => 'Kabut',
            '60' => 'Hujan Ringan',
            '61' => 'Hujan Sedang',
            '63' => 'Hujan Lebat',
            '80' => 'Hujan Lokal',
            '95' => 'Hujan Petir',
            '97' => 'Hujan Petir'
        ];
        
        return $conditions[$weatherCode] ?? 'Cerah';
    }
    
    /**
     * Get weather icon based on condition
     */
    private function getWeatherIcon($weatherCode)
    {
        $icons = [
            '0' => 'fas fa-sun',
            '1' => 'fas fa-cloud-sun',
            '2' => 'fas fa-cloud',
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
            '97' => 'fas fa-bolt'
        ];
        
        return $icons[$weatherCode] ?? 'fas fa-sun';
    }
    
    /**
     * Fallback weather data when API fails
     */
    private function getFallbackWeatherData()
    {
        // Data cuaca umum untuk Pesisir Barat
        $conditions = ['Cerah', 'Berawan', 'Hujan Ringan'];
        $condition = $conditions[array_rand($conditions)];
        
        // Generate forecast data fallback
        $forecast = [];
        $today = now()->startOfDay();
        $forecastConditions = ['Cerah', 'Berawan', 'Hujan Ringan', 'Cerah Berawan'];
        
        for ($day = 1; $day <= 3; $day++) {
            $targetDate = $today->copy()->addDays($day);
            $forecastCondition = $forecastConditions[array_rand($forecastConditions)];
            
            $forecast[] = [
                'day' => $this->getDayName($targetDate),
                'date' => $targetDate->format('d/m'),
                'temperature' => rand(26, 30),
                'temp_min' => rand(24, 26),
                'temp_max' => rand(28, 32),
                'condition' => $forecastCondition,
                'icon' => $this->getFallbackIcon($forecastCondition)
            ];
        }
        
        return [
            'temperature' => rand(26, 32),
            'humidity' => rand(70, 85),
            'condition' => $condition,
            'icon' => $this->getFallbackIcon($condition),
            'location' => 'Pesisir Barat',
            'source' => 'Estimasi',
            'updated_at' => now()->format('H:i'),
            'forecast' => $forecast
        ];
    }
    
    /**
     * Get fallback icon
     */
    private function getFallbackIcon($condition)
    {
        $icons = [
            'Cerah' => 'fas fa-sun',
            'Cerah Berawan' => 'fas fa-cloud-sun',
            'Berawan' => 'fas fa-cloud',
            'Hujan Ringan' => 'fas fa-cloud-rain',
            'Hujan Sedang' => 'fas fa-cloud-rain',
            'Hujan Lebat' => 'fas fa-cloud-showers-heavy'
        ];
        
        return $icons[$condition] ?? 'fas fa-sun';
    }
}
