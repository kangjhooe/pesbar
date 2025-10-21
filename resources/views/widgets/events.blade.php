@php
    $eventService = new \App\Services\EventService();
    $events = $eventService->getWidgetEvents(5);
@endphp

@if($events['events']->count() > 0)
<div class="widget events-widget">
    <div class="widget-header">
        <h4 class="widget-title">
            <i class="fas fa-calendar-alt"></i>
            Agenda Kegiatan
        </h4>
        <span class="text-xs text-gray-500 bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-semibold">
            {{ $events['total_count'] }} kegiatan
        </span>
    </div>
    
    <div class="widget-content">
        <div class="events-list">
            @foreach($events['events'] as $event)
                <div class="event-item">
                    <div class="event-date">
                        <div class="event-day">{{ $event->event_date->format('d') }}</div>
                        <div class="event-month">{{ $event->event_date->format('M') }}</div>
                    </div>
                    
                    <div class="event-info">
                        <div class="event-title">
                            <strong>{{ Str::limit($event->title, 40) }}</strong>
                            <span class="event-type badge bg-{{ $event->priority_color }}-100 text-{{ $event->priority_color }}-600">
                                {{ $event->event_type_label }}
                            </span>
                        </div>
                        
                        @if($event->start_time)
                            <div class="event-time">
                                <i class="fas fa-clock text-blue-600"></i>
                                {{ $event->formatted_start_time }}
                                @if($event->end_time)
                                    - {{ $event->formatted_end_time }}
                                @endif
                            </div>
                        @endif
                        
                        @if($event->location)
                            <div class="event-location">
                                <i class="fas fa-map-marker-alt text-green-600"></i>
                                {{ Str::limit($event->location, 30) }}
                            </div>
                        @endif
                        
                        @if($event->organizer)
                            <div class="event-organizer">
                                <i class="fas fa-user text-purple-600"></i>
                                {{ Str::limit($event->organizer, 25) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="widget-footer">
            <div class="text-center">
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-calendar-plus mr-1"></i>
                    Lihat Semua Agenda
                </a>
            </div>
            @if(isset($events['updated_at']))
            <div class="text-center mt-2">
                <small class="text-muted">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Update: {{ $events['updated_at'] }}
                </small>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.events-widget {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.events-widget .widget-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.events-widget .widget-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.events-widget .widget-title i {
    margin-right: 6px;
    font-size: 12px;
}

.events-widget .widget-content {
    padding: 0;
}

.events-list {
    max-height: 300px;
    overflow-y: auto;
}

.event-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: background-color 0.2s ease;
}

.event-item:last-child {
    border-bottom: none;
}

.event-item:hover {
    background-color: #f8f9fa;
}

.event-date {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px;
    padding: 6px 10px;
    text-align: center;
    min-width: 45px;
    flex-shrink: 0;
}

.event-day {
    font-size: 16px;
    font-weight: bold;
    line-height: 1;
}

.event-month {
    font-size: 9px;
    text-transform: uppercase;
    margin-top: 2px;
}

.event-info {
    flex: 1;
    min-width: 0;
}

.event-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    gap: 6px;
}

.event-title strong {
    color: #333;
    font-size: 13px;
    line-height: 1.3;
}

.event-type {
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 4px;
    white-space: nowrap;
}

.event-time,
.event-location,
.event-organizer {
    color: #666;
    font-size: 11px;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.event-time i,
.event-location i,
.event-organizer i {
    width: 10px;
    text-align: center;
    font-size: 10px;
}

.widget-footer {
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.widget-footer .btn {
    font-size: 11px;
    padding: 5px 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .events-widget .widget-header {
        padding: 12px 15px;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .event-item {
        padding: 12px 15px;
        flex-direction: column;
        gap: 10px;
    }
    
    .event-date {
        align-self: flex-start;
    }
    
    .event-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .event-type {
        align-self: flex-start;
    }
}

/* Scrollbar styling */
.events-list::-webkit-scrollbar {
    width: 4px;
}

.events-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.events-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.events-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endif
