<!-- Maritime Widget -->
<div class="bg-white border border-gray-200 rounded-lg shadow-lg {{ isset($isHome) && $isHome ? 'p-4 mb-4' : 'p-6 mb-8' }} widget" id="{{ isset($isHome) && $isHome ? 'home-maritime-widget' : 'maritime-widget' }}">
    <div class="flex items-center justify-between mb-3">
        <h3 class="{{ isset($isHome) && $isHome ? 'text-base' : 'text-lg' }} font-bold text-gray-800 flex items-center">
            <i class="fas fa-water text-blue-500 mr-2 {{ isset($isHome) && $isHome ? 'text-sm' : '' }} maritime-icon"></i>Informasi Maritim
        </h3>
        <span class="text-xs text-gray-500 bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-semibold relative">Live</span>
    </div>

    <!-- Wave Height -->
    <div class="mb-3 p-3 bg-gradient-to-r from-blue-50 via-cyan-50 to-blue-50 rounded-lg border border-blue-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-700">Tinggi Gelombang</span>
            <span class="maritime-wave-category px-2 py-0.5 rounded-full text-xs font-bold {{ 
                ($maritimeData['wave_height_category'] ?? 'Sedang') === 'Sangat Tinggi' ? 'bg-red-100 text-red-800' : 
                (($maritimeData['wave_height_category'] ?? 'Sedang') === 'Tinggi' ? 'bg-orange-100 text-orange-800' : 
                (($maritimeData['wave_height_category'] ?? 'Sedang') === 'Sedang' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'))
            }}">
                {{ $maritimeData['wave_height_category'] ?? 'Sedang' }}
            </span>
        </div>
        <div class="flex items-end space-x-2">
            <div class="maritime-wave-height {{ isset($isHome) && $isHome ? 'text-2xl' : 'text-3xl' }} font-bold text-blue-600">{{ $maritimeData['wave_height'] ?? '1.2' }}</div>
            <div class="text-xs text-gray-600 mb-1">meter</div>
        </div>
        <!-- Wave Animation -->
        <div class="mt-2 relative {{ isset($isHome) && $isHome ? 'h-6' : 'h-8' }} overflow-hidden rounded-lg bg-blue-100/50">
            <div class="wave-animation absolute inset-0 flex items-end">
                <div class="wave-bar" style="height: {{ min(100, (($maritimeData['wave_height'] ?? 1.2) / 4) * 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Tide Information -->
    <div class="mb-3 p-3 bg-gradient-to-r from-teal-50 via-green-50 to-teal-50 rounded-lg border border-teal-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-700">Pasang Surut</span>
            <div class="flex items-center space-x-1.5">
                <i class="maritime-tide-icon {{ $maritimeData['tide']['icon'] ?? 'fas fa-arrow-up' }} {{ 
                    ($maritimeData['tide']['status'] ?? 'Pasang') === 'Pasang' ? 'text-blue-600' : 'text-gray-600'
                }} tide-animation text-sm"></i>
                <span class="maritime-tide-status font-bold text-xs {{ 
                    ($maritimeData['tide']['status'] ?? 'Pasang') === 'Pasang' ? 'text-blue-600' : 'text-gray-600'
                }}">
                    {{ $maritimeData['tide']['status'] ?? 'Pasang' }}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
            <div>
                <div class="text-gray-600 text-xs">Pasang Berikutnya</div>
                <div class="maritime-next-high-tide font-semibold text-gray-800">{{ $maritimeData['tide']['next_high_tide'] ?? '07:00' }}</div>
            </div>
            <div>
                <div class="text-gray-600 text-xs">Surut Berikutnya</div>
                <div class="maritime-next-low-tide font-semibold text-gray-800">{{ $maritimeData['tide']['next_low_tide'] ?? '13:00' }}</div>
            </div>
        </div>
        <!-- Tide Level Indicator -->
        <div class="mt-2 relative h-2 bg-gray-200 rounded-full overflow-hidden shadow-inner">
            <div class="tide-level-indicator absolute top-0 left-0 h-full bg-gradient-to-r from-blue-400 via-teal-400 to-green-400 rounded-full transition-all duration-1000" 
                 style="width: {{ $maritimeData['tide']['level'] ?? 50 }}%"></div>
        </div>
    </div>

    <!-- Wind Information -->
    <div class="mb-3 p-3 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 rounded-lg border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-700">Angin</span>
            <i class="fas fa-wind text-gray-500 wind-icon text-sm"></i>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div>
                <div class="text-gray-600 text-xs">Kecepatan</div>
                <div class="maritime-wind-speed font-bold text-gray-800">{{ $maritimeData['wind_speed'] ?? '15' }} <span class="text-xs font-normal">km/jam</span></div>
            </div>
            <div>
                <div class="text-gray-600 text-xs">Arah</div>
                <div class="maritime-wind-direction font-bold text-gray-800">{{ $maritimeData['wind_direction'] ?? 'Barat' }}</div>
            </div>
        </div>
    </div>

    <!-- Warnings -->
    @if(isset($maritimeData['warning']) && count($maritimeData['warning']) > 0)
    <div class="mb-3 space-y-1.5">
        @foreach($maritimeData['warning'] as $warning)
        <div class="p-2 rounded-lg border-l-4 {{ 
            $warning['level'] === 'danger' ? 'bg-red-50 border-red-500' : 'bg-yellow-50 border-yellow-500'
        }} warning-animation">
            <div class="flex items-center space-x-2">
                <i class="{{ $warning['icon'] }} {{ 
                    $warning['level'] === 'danger' ? 'text-red-600' : 'text-yellow-600'
                }} text-xs"></i>
                <span class="text-xs font-semibold {{ 
                    $warning['level'] === 'danger' ? 'text-red-800' : 'text-yellow-800'
                }}">{{ $warning['message'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Forecast -->
    @if(isset($maritimeData['forecast']) && count($maritimeData['forecast']) > 0)
    <div class="mt-3 pt-3 border-t border-gray-200">
        <div class="text-xs font-semibold text-gray-700 mb-2">Prakiraan 3 Hari</div>
        <div class="space-y-1.5 maritime-forecast-container">
            @foreach($maritimeData['forecast'] as $forecast)
            <div class="flex items-center justify-between p-1.5 hover:bg-gray-50 rounded transition-all duration-200 maritime-forecast-item">
                <div class="flex items-center space-x-2 flex-1">
                    <i class="{{ $forecast['icon'] }} {{ isset($isHome) && $isHome ? 'text-sm' : 'text-lg' }}"></i>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-gray-700 truncate">{{ $forecast['day'] }}, {{ $forecast['date'] }}</div>
                        <div class="text-xs text-gray-500">{{ $forecast['wave_category'] }}</div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <div class="text-xs font-bold text-blue-600">{{ $forecast['wave_height'] }}m</div>
                    <div class="text-xs text-gray-500">{{ $forecast['wind_speed'] }} km/j</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="text-center mt-3 pt-3 border-t border-gray-100">
        <div class="text-xs text-gray-500">
            <span class="maritime-location">{{ $maritimeData['location'] ?? 'Pesisir Barat' }}</span>
            @if(isset($maritimeData['updated_at']))
                <br><span class="maritime-update text-xs">Update: {{ $maritimeData['updated_at'] }}</span>
            @endif
        </div>
    </div>
</div>

