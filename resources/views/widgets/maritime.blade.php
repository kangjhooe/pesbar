@php
    $maritimeData = $maritimeData ?? [];
    $isHome = $isHome ?? true;
    $waveCategory = $maritimeData['wave_height_category'] ?? 'Sedang';
    $tideStatus = $maritimeData['tide']['status'] ?? 'Pasang';
@endphp

<div class="border border-news-line" id="{{ $isHome ? 'home-maritime-widget' : 'maritime-widget' }}">
    <div class="bg-news-ink text-white px-4 py-2.5 flex items-center justify-between gap-2">
        <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Informasi Maritim</h2>
        <span class="text-[10px] font-bold uppercase tracking-wider text-white/60 shrink-0">Live</span>
    </div>

    <div class="p-4 space-y-3">
        {{-- Wave height --}}
        <div class="border border-news-line p-3">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-news-muted">Tinggi Gelombang</span>
                <span class="maritime-wave-category text-[10px] font-bold uppercase tracking-wider
                    {{ $waveCategory === 'Sangat Tinggi' ? 'text-red-700' : ($waveCategory === 'Tinggi' ? 'text-orange-700' : ($waveCategory === 'Sedang' ? 'text-news-ink' : 'text-green-700')) }}">
                    {{ $waveCategory }}
                </span>
            </div>
            <div class="flex items-end gap-1.5">
                <div class="maritime-wave-height font-display text-2xl font-bold text-news-ink leading-none">
                    {{ $maritimeData['wave_height'] ?? '1.2' }}
                </div>
                <div class="text-[11px] text-news-muted mb-0.5">meter</div>
            </div>
        </div>

        {{-- Tide --}}
        <div class="border border-news-line p-3">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-news-muted">Pasang Surut</span>
                <span class="flex items-center gap-1.5">
                    <i class="maritime-tide-icon {{ $maritimeData['tide']['icon'] ?? 'fas fa-arrow-up' }} text-news-accent text-xs" aria-hidden="true"></i>
                    <span class="maritime-tide-status text-xs font-bold text-news-ink">{{ $tideStatus }}</span>
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                <div>
                    <div class="text-news-muted">Pasang berikutnya</div>
                    <div class="maritime-next-high-tide font-bold text-news-ink tabular-nums">{{ $maritimeData['tide']['next_high_tide'] ?? '07:00' }}</div>
                </div>
                <div>
                    <div class="text-news-muted">Surut berikutnya</div>
                    <div class="maritime-next-low-tide font-bold text-news-ink tabular-nums">{{ $maritimeData['tide']['next_low_tide'] ?? '13:00' }}</div>
                </div>
            </div>
            <div class="mt-2 h-1.5 bg-news-line overflow-hidden">
                <div class="tide-level-indicator h-full bg-news-accent transition-all duration-700"
                     style="width: {{ $maritimeData['tide']['level'] ?? 50 }}%"></div>
            </div>
        </div>

        {{-- Wind --}}
        <div class="border border-news-line p-3">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-news-muted">Angin</span>
                <i class="fas fa-wind text-news-muted text-xs" aria-hidden="true"></i>
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                <div>
                    <div class="text-news-muted">Kecepatan</div>
                    <div class="maritime-wind-speed font-bold text-news-ink">
                        {{ $maritimeData['wind_speed'] ?? '15' }} <span class="font-normal text-news-muted">km/jam</span>
                    </div>
                </div>
                <div>
                    <div class="text-news-muted">Arah</div>
                    <div class="maritime-wind-direction font-bold text-news-ink">{{ $maritimeData['wind_direction'] ?? 'Barat' }}</div>
                </div>
            </div>
        </div>

        @if(!empty($maritimeData['warning']) && count($maritimeData['warning']) > 0)
        <div class="space-y-1.5">
            @foreach($maritimeData['warning'] as $warning)
            <div class="px-3 py-2 border-l-2 {{ $warning['level'] === 'danger' ? 'border-red-600 bg-red-50' : 'border-amber-500 bg-amber-50' }}">
                <div class="flex items-start gap-2">
                    <i class="{{ $warning['icon'] }} text-xs mt-0.5 {{ $warning['level'] === 'danger' ? 'text-red-700' : 'text-amber-700' }}" aria-hidden="true"></i>
                    <span class="text-xs font-semibold {{ $warning['level'] === 'danger' ? 'text-red-800' : 'text-amber-900' }}">
                        {{ $warning['message'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if(!empty($maritimeData['forecast']) && count($maritimeData['forecast']) > 0)
        <div class="pt-3 border-t border-news-line">
            <div class="text-[10px] font-bold uppercase tracking-wider text-news-muted mb-2">Prakiraan 3 Hari</div>
            <div class="divide-y divide-news-line maritime-forecast-container">
                @foreach($maritimeData['forecast'] as $forecast)
                <div class="flex items-center justify-between gap-2 py-2 maritime-forecast-item">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="{{ $forecast['icon'] }} text-news-accent text-sm w-4 text-center shrink-0" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-news-ink truncate">{{ $forecast['day'] }}, {{ $forecast['date'] }}</div>
                            <div class="text-[10px] text-news-muted">{{ $forecast['wave_category'] }}</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-bold text-news-ink tabular-nums">{{ $forecast['wave_height'] }}m</div>
                        <div class="text-[10px] text-news-muted">{{ $forecast['wind_speed'] }} km/j</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="pt-3 border-t border-news-line text-center text-[10px] text-news-muted">
            <span class="maritime-location">{{ $maritimeData['location'] ?? 'Pesisir Barat' }}</span>
            @if(!empty($maritimeData['updated_at']))
                <span class="maritime-update"> · Update {{ $maritimeData['updated_at'] }}</span>
            @endif
        </div>
    </div>
</div>
