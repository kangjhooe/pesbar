<?php

namespace App\Console\Commands;

use App\Models\EventPopup;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestEventPopup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event-popup:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Event Popup functionality and display active popups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Testing Event Popup Functionality');
        $this->line('');

        // Display all event popups
        $this->info('📋 All Event Popups:');
        $this->line('');

        $eventPopups = EventPopup::orderBy('created_at', 'desc')->get();

        if ($eventPopups->isEmpty()) {
            $this->warn('No event popups found. Run seeder first: php artisan db:seed --class=EventPopupSeeder');
            return;
        }

        $headers = ['ID', 'Title', 'Start Date', 'End Date', 'Status', 'Active'];
        $rows = [];

        foreach ($eventPopups as $popup) {
            $rows[] = [
                $popup->id,
                Str::limit($popup->title, 30),
                $popup->start_date->format('d-m-Y'),
                $popup->end_date->format('d-m-Y'),
                $popup->status ? '✅ Active' : '❌ Inactive',
                $popup->isActive() ? '🟢 Running' : '🔴 Not Running'
            ];
        }

        $this->table($headers, $rows);

        // Display active popup
        $this->line('');
        $this->info('🎯 Currently Active Popup:');
        
        $activePopup = EventPopup::active()->first();
        
        if ($activePopup) {
            $this->line('');
            $this->info("Title: {$activePopup->title}");
            $this->line("Message: {$activePopup->message}");
            $this->line("Period: {$activePopup->start_date->format('d-m-Y')} to {$activePopup->end_date->format('d-m-Y')}");
            $this->line("Days remaining: " . max(0, Carbon::now()->diffInDays($activePopup->end_date, false)) . " days");
            $this->line('');
            $this->info('✅ This popup will be displayed to visitors on the homepage');
        } else {
            $this->warn('❌ No active popup found');
            $this->line('To create an active popup:');
            $this->line('1. Go to admin panel: /admin/event-popups');
            $this->line('2. Create new event popup');
            $this->line('3. Set status to active');
            $this->line('4. Set dates to include today');
        }

        // Test localStorage simulation
        $this->line('');
        $this->info('💾 localStorage Simulation:');
        $this->line('Each popup uses localStorage key: eventPopup_{id}');
        $this->line('To reset popup visibility, clear localStorage in browser:');
        $this->line('localStorage.removeItem("eventPopup_' . ($activePopup->id ?? 'X') . '")');

        $this->line('');
        $this->info('🎉 Event Popup test completed!');
    }
}
