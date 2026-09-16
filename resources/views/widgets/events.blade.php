@php
    $eventService = new \App\Services\EventService();
    $events = $eventService->getWidgetEvents(5);
@endphp

@if($events['events']->count() > 0)
<div class="public-widget border border-news-line">
    <div class="bg-news-ink text-white px-4 py-2.5 flex items-center justify-between gap-2">
        <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Agenda Kegiatan</h2>
        <span class="text-[10px] font-bold uppercase tracking-wider text-white/60 shrink-0">
            {{ $events['total_count'] }} kegiatan
        </span>
    </div>

    <ul class="divide-y divide-news-line">
        @foreach($events['events'] as $event)
        <li class="flex gap-3 px-4 py-3">
            <div class="w-12 shrink-0 text-center border-r border-news-line pr-3 flex flex-col justify-center">
                <span class="font-display text-xl font-bold text-news-accent leading-none">{{ $event->event_date->format('d') }}</span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-news-muted mt-1">{{ $event->event_date->format('M') }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-news-accent">{{ $event->event_type_label }}</span>
                <h3 class="text-sm font-bold leading-snug text-news-ink mt-0.5 line-clamp-2" title="{{ $event->title }}">
                    {{ $event->title }}
                </h3>
                <p class="mt-1 text-[11px] text-news-muted space-y-0.5">
                    @if($event->start_time)
                        <span class="block">
                            <i class="far fa-clock mr-0.5" aria-hidden="true"></i>
                            {{ $event->formatted_start_time }}@if($event->end_time)–{{ $event->formatted_end_time }}@endif
                        </span>
                    @endif
                    @if($event->location)
                        <span class="block">
                            <i class="fas fa-map-marker-alt mr-0.5" aria-hidden="true"></i>
                            {{ Str::limit($event->location, 36) }}
                        </span>
                    @endif
                </p>
            </div>
        </li>
        @endforeach
    </ul>

    <div class="border-t border-news-line px-4 py-3 text-center">
        <a href="{{ route('events.index') }}" class="text-xs font-bold uppercase tracking-wider text-news-accent hover:underline">
            Lihat semua agenda
        </a>
        @if(!empty($events['updated_at']))
            <p class="mt-1 text-[10px] text-news-muted">Update {{ $events['updated_at'] }}</p>
        @endif
    </div>
</div>
@endif
