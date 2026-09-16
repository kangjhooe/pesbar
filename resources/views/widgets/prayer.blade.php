@php
    $prayerData = $prayerData ?? [];
    $prayers = [
        'imsak' => ['name' => 'Imsak', 'icon' => 'fas fa-moon'],
        'fajr' => ['name' => 'Subuh', 'icon' => 'fas fa-sun'],
        'sunrise' => ['name' => 'Terbit', 'icon' => 'fas fa-sun'],
        'dhuha' => ['name' => 'Duha', 'icon' => 'fas fa-cloud-sun'],
        'dhuhr' => ['name' => 'Dzuhur', 'icon' => 'fas fa-sun'],
        'asr' => ['name' => 'Ashar', 'icon' => 'fas fa-cloud-sun'],
        'maghrib' => ['name' => 'Maghrib', 'icon' => 'fas fa-moon'],
        'isha' => ['name' => 'Isya', 'icon' => 'fas fa-moon'],
    ];
@endphp

<div class="public-widget border border-news-line" id="home-prayer-times-widget">
    <div class="bg-news-ink text-white px-4 py-2.5 flex items-center justify-between gap-2">
        <h2 class="text-xs font-bold uppercase tracking-[0.15em] flex items-center gap-2 min-w-0">
            <i class="fas fa-mosque text-news-accent shrink-0"></i>
            <span class="truncate">Waktu Sholat</span>
        </h2>
        <span class="text-[10px] font-bold uppercase tracking-wider text-white/60 shrink-0">Kemenag</span>
    </div>

    <div class="p-4">
        <div class="flex items-baseline justify-between gap-2 mb-3 pb-3 border-b border-news-line">
            <div>
                <div class="home-prayer-location text-sm font-bold text-news-ink">
                    {{ $prayerData['location'] ?? 'Kab. Pesisir Barat' }}
                </div>
                <div class="home-prayer-date text-[11px] text-news-muted mt-0.5">
                    {{ !empty($prayerData['date']) ? \Carbon\Carbon::parse($prayerData['date'])->translatedFormat('d M Y') : now()->translatedFormat('d M Y') }}
                    @if(!empty($prayerData['region']))
                        · {{ $prayerData['region'] }}
                    @endif
                </div>
            </div>
            @if(!empty($prayerData['updated_at']))
                <div class="home-prayer-update text-[10px] text-news-muted shrink-0">Update {{ $prayerData['updated_at'] }}</div>
            @endif
        </div>

        <div class="space-y-0 divide-y divide-news-line home-prayer-times-list">
            @foreach($prayers as $key => $prayer)
            <div class="flex items-center justify-between gap-2 py-2">
                <span class="flex items-center gap-2 text-sm text-news-ink">
                    <i class="{{ $prayer['icon'] }} text-news-accent text-xs w-4 text-center" aria-hidden="true"></i>
                    <span class="font-medium">{{ $prayer['name'] }}</span>
                </span>
                <span class="home-prayer-{{ $key }} font-display text-sm font-bold text-news-ink tabular-nums">
                    {{ $prayerData['prayers'][$key] ?? '--:--' }}
                </span>
            </div>
            @endforeach
        </div>

        <p class="mt-3 pt-3 border-t border-news-line text-center text-[10px] text-news-muted">
            Sumber: {{ $prayerData['source'] ?? 'Kemenag' }} RI
        </p>
    </div>
</div>
