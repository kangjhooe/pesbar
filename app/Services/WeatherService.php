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
                
                return [
                    'temperature' => $temperature ?: 28,
                    'humidity' => $humidity ?: 75,
                    'condition' => $this->mapWeatherCondition($weather),
                    'icon' => $this->getWeatherIcon($weather),
                    'location' => 'Pesisir Barat',
                    'source' => 'BMKG',
                    'updated_at' => now()->format('H:i')
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
        $param = $parameter->xpath("//parameter[@id='{$type}']")[0] ?? null;
        
        if ($param && isset($param->timerange[$index])) {
            $value = $param->timerange[$index]->value;
            return (string) $value;
        }
        
        return null;
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
        
        return [
            'temperature' => rand(26, 32),
            'humidity' => rand(70, 85),
            'condition' => $condition,
            'icon' => $this->getFallbackIcon($condition),
            'location' => 'Pesisir Barat',
            'source' => 'Estimasi',
            'updated_at' => now()->format('H:i')
        ];
    }
    
    /**
     * Get fallback icon
     */
    private function getFallbackIcon($condition)
    {
        $icons = [
            'Cerah' => 'fas fa-sun',
            'Berawan' => 'fas fa-cloud',
            'Hujan Ringan' => 'fas fa-cloud-rain'
        ];
        
        return $icons[$condition] ?? 'fas fa-sun';
    }
}
