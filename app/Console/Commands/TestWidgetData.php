<?php

namespace App\Console\Commands;

use App\Services\WeatherService;
use App\Services\PrayerTimeService;
use Illuminate\Console\Command;

class TestWidgetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widget:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test widget data services (weather and prayer times)';

    /**
     * Execute the console command.
     */
    public function handle(WeatherService $weatherService, PrayerTimeService $prayerTimeService)
    {
        $this->info('Testing Widget Data Services...');
        $this->newLine();

        // Test Weather Service
        $this->info('🌤️  Testing Weather Service:');
        try {
            $weatherData = $weatherService->getWeatherData();
            $this->line("   Temperature: {$weatherData['temperature']}°C");
            $this->line("   Condition: {$weatherData['condition']}");
            $this->line("   Location: {$weatherData['location']}");
            $this->line("   Source: {$weatherData['source']}");
            $this->line("   Updated: {$weatherData['updated_at']}");
            $this->info('   ✅ Weather service working correctly');
        } catch (\Exception $e) {
            $this->error("   ❌ Weather service failed: {$e->getMessage()}");
        }

        $this->newLine();

        // Test Prayer Time Service
        $this->info('🕌 Testing Prayer Time Service:');
        try {
            $prayerData = $prayerTimeService->getPrayerTimes();
            $this->line("   Location: {$prayerData['location']}");
            $this->line("   Date: {$prayerData['date']}");
            $this->line("   Fajr: {$prayerData['prayers']['fajr']}");
            $this->line("   Dhuhr: {$prayerData['prayers']['dhuhr']}");
            $this->line("   Asr: {$prayerData['prayers']['asr']}");
            $this->line("   Maghrib: {$prayerData['prayers']['maghrib']}");
            $this->line("   Isha: {$prayerData['prayers']['isha']}");
            $this->line("   Updated: {$prayerData['updated_at']}");
            $this->info('   ✅ Prayer time service working correctly');
        } catch (\Exception $e) {
            $this->error("   ❌ Prayer time service failed: {$e->getMessage()}");
        }

        $this->newLine();

        // Test Next Prayer
        $this->info('⏰ Testing Next Prayer:');
        try {
            $nextPrayer = $prayerTimeService->getNextPrayer();
            $this->line("   Next Prayer: {$nextPrayer['name']}");
            $this->line("   Time: {$nextPrayer['time']}");
            $this->line("   Remaining: {$nextPrayer['remaining']}");
            $this->info('   ✅ Next prayer service working correctly');
        } catch (\Exception $e) {
            $this->error("   ❌ Next prayer service failed: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info('🎉 Widget data testing completed!');
    }
}