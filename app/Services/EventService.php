<?php

namespace App\Services;

use App\Models\Event;
use App\Helpers\CacheHelper;
use Carbon\Carbon;

class EventService
{
    private const CACHE_KEY = 'events_widget';
    private const CACHE_DURATION = 3600; // 1 jam

    /**
     * Get events for widget display
     */
    public function getWidgetEvents($limit = 5)
    {
        $cacheKey = self::CACHE_KEY . '_' . $limit;
        
        return CacheHelper::remember(
            $cacheKey,
            self::CACHE_DURATION,
            function () use ($limit) {
                return $this->fetchWidgetEvents($limit);
            }
        );
    }

    /**
     * Fetch events for widget
     */
    private function fetchWidgetEvents($limit)
    {
        $events = Event::active()
            ->public()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->orderBy('priority', 'desc')
            ->limit($limit)
            ->get();

        return [
            'events' => $events,
            'total_count' => Event::active()->public()->upcoming()->count(),
            'updated_at' => now()->format('H:i')
        ];
    }

    /**
     * Get today's events
     */
    public function getTodayEvents()
    {
        return Event::active()
            ->public()
            ->today()
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get this week's events
     */
    public function getThisWeekEvents()
    {
        return Event::active()
            ->public()
            ->thisWeek()
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get this month's events
     */
    public function getThisMonthEvents()
    {
        return Event::active()
            ->public()
            ->thisMonth()
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get events by type
     */
    public function getEventsByType($type, $limit = 10)
    {
        return Event::active()
            ->public()
            ->byType($type)
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get high priority events
     */
    public function getHighPriorityEvents($limit = 3)
    {
        return Event::active()
            ->public()
            ->byPriority('high')
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get events for calendar view
     */
    public function getCalendarEvents($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->startOfMonth();
        $endDate = $endDate ?: now()->endOfMonth();

        return Event::active()
            ->public()
            ->whereBetween('event_date', [$startDate, $endDate])
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get event statistics
     */
    public function getEventStats()
    {
        $totalEvents = Event::active()->public()->count();
        $todayEvents = Event::active()->public()->today()->count();
        $thisWeekEvents = Event::active()->public()->thisWeek()->count();
        $thisMonthEvents = Event::active()->public()->thisMonth()->count();

        return [
            'total' => $totalEvents,
            'today' => $todayEvents,
            'this_week' => $thisWeekEvents,
            'this_month' => $thisMonthEvents
        ];
    }

    /**
     * Get event types with counts
     */
    public function getEventTypesWithCounts()
    {
        return Event::active()
            ->public()
            ->upcoming()
            ->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Search events
     */
    public function searchEvents($query, $limit = 20)
    {
        return Event::active()
            ->public()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhere('organizer', 'like', "%{$query}%");
            })
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->limit($limit)
            ->get();
    }
}
