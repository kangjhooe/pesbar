@php
    $weatherData = $weatherData ?? [];
@endphp

<div class="public-widget border border-news-line" id="home-weather-widget">
    <div class="bg-news-ink text-white px-4 py-2.5 flex items-center justify-between gap-2">
        <h2 class="text-xs font-bold uppercase tracking-[0.15em] flex items-center gap-2 min-w-0">
            <i class="home-weather-icon {{ $weatherData['icon'] ?? 'fas fa-cloud-sun' }} text-news-accent shrink-0"></i>
            <span class="truncate">Prakiraan Cuaca</span>
        </h2>
        <span class="text-[10px] font-bold uppercase tracking-wider text-white/60 shrink-0">Live</span>
    </div>

    <div class="p-4">
        <div class="flex items-center gap-3">
            <div class="text-3xl text-news-accent shrink-0" aria-hidden="true">
                <i class="home-weather-icon-large {{ $weatherData['icon'] ?? 'fas fa-sun' }}"></i>
            </div>
            <div class="min-w-0">
                <div class="home-weather-temp font-display text-2xl font-bold text-news-ink leading-none">
                    @if(isset($weatherData['temperature']) && $weatherData['temperature'] !== null)
                        {{ $weatherData['temperature'] }}°C
                    @else
                        —
                    @endif
                </div>
                <div class="home-weather-condition mt-1 text-sm text-news-muted">
                    {{ $weatherData['condition'] ?? 'Memuat…' }}
                </div>
                <div class="mt-1 text-[11px] text-news-muted">
                    <span class="home-weather-location">{{ $weatherData['location'] ?? 'Pesisir Barat' }}</span>
                    @if(!empty($weatherData['updated_at']))
                        <span class="home-weather-update"> · Update {{ $weatherData['updated_at'] }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-news-line grid grid-cols-2 gap-2 text-center">
            <div class="border border-news-line px-2 py-2">
                <div class="text-[10px] font-bold uppercase tracking-wider text-news-muted">Kelembaban</div>
                <div class="mt-0.5 text-sm font-bold text-news-ink">
                    {{ isset($weatherData['humidity']) && $weatherData['humidity'] !== null ? $weatherData['humidity'] . '%' : '—' }}
                </div>
            </div>
            <div class="border border-news-line px-2 py-2">
                <div class="text-[10px] font-bold uppercase tracking-wider text-news-muted">Sumber</div>
                <div class="mt-0.5 text-sm font-bold text-news-ink">BMKG</div>
            </div>
        </div>
        @if(!empty($weatherData['wind_speed']))
        <div class="mt-2 border border-news-line px-2 py-2 text-center">
            <div class="text-[10px] font-bold uppercase tracking-wider text-news-muted">Angin</div>
            <div class="mt-0.5 text-sm font-bold text-news-ink">
                {{ $weatherData['wind_speed'] }} km/j
                @if(!empty($weatherData['wind_direction']))
                    · {{ $weatherData['wind_direction'] }}
                @endif
            </div>
        </div>
        @endif

        @if(!empty($weatherData['forecast']) && count($weatherData['forecast']) > 0)
        <div class="mt-4 pt-3 border-t border-news-line">
            <div class="text-[10px] font-bold uppercase tracking-wider text-news-muted mb-2">Prakiraan 3 Hari</div>
            <div class="space-y-0 divide-y divide-news-line home-weather-forecast-container">
                @foreach($weatherData['forecast'] as $forecast)
                <div class="flex items-center justify-between gap-2 py-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="{{ $forecast['icon'] ?? 'fas fa-sun' }} text-news-accent text-sm w-4 text-center shrink-0"></i>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-news-ink truncate">{{ $forecast['day'] ?? '—' }}</div>
                            <div class="text-[10px] text-news-muted">{{ $forecast['date'] ?? '' }}</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-bold text-news-ink tabular-nums">
                            @if(isset($forecast['temp_min'], $forecast['temp_max']))
                                {{ $forecast['temp_min'] }}–{{ $forecast['temp_max'] }}°
                            @else
                                {{ $forecast['temperature'] ?? 28 }}°
                            @endif
                        </div>
                        <div class="text-[10px] text-news-muted">{{ $forecast['condition'] ?? 'Cerah' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
