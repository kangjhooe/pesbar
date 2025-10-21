@extends('layouts.public')

@section('title', 'Agenda Kegiatan - Portal Berita Kabupaten Pesisir Barat')
@section('description', 'Lihat semua agenda dan kegiatan yang akan diselenggarakan di Kabupaten Pesisir Barat')

@section('content')
<div class="container-responsive py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                    Agenda Kegiatan
                </h1>
                <p class="text-gray-600">
                    Daftar lengkap agenda dan kegiatan yang akan diselenggarakan di Kabupaten Pesisir Barat
                </p>
            </div>
            <div class="text-right">
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                    <div class="text-2xl font-bold">{{ $total_count }}</div>
                    <div class="text-sm">Total Kegiatan</div>
                </div>
            </div>
        </div>
    </div>

    @if($events->count() > 0)
        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
            <div class="card group hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <!-- Event Date -->
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-lg p-3 text-center min-w-[60px]">
                            <div class="text-xl font-bold">{{ $event->event_date->format('d') }}</div>
                            <div class="text-xs uppercase">{{ $event->event_date->format('M') }}</div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-800 text-lg line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $event->priority_color }}-100 text-{{ $event->priority_color }}-600">
                                    {{ $event->event_type_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="space-y-3">
                        @if($event->start_time)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock text-blue-600 w-4 mr-3"></i>
                            <span class="text-sm">
                                {{ $event->formatted_start_time }}
                                @if($event->end_time)
                                    - {{ $event->formatted_end_time }}
                                @endif
                            </span>
                        </div>
                        @endif

                        @if($event->location)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt text-green-600 w-4 mr-3"></i>
                            <span class="text-sm">{{ $event->location }}</span>
                        </div>
                        @endif

                        @if($event->organizer)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user text-purple-600 w-4 mr-3"></i>
                            <span class="text-sm">{{ $event->organizer }}</span>
                        </div>
                        @endif

                        @if($event->description)
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-gray-600 text-sm line-clamp-3">
                                {{ $event->description }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Event Status -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $event->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                                <i class="fas fa-circle w-2 h-2 mr-1 {{ $event->is_active ? 'text-green-500' : 'text-gray-400' }}"></i>
                                {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $event->created_at->format('d-m-Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Empty State (if no events) -->
    @else
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-calendar-alt text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Agenda</h3>
                <p class="text-gray-500 mb-6">
                    Saat ini belum ada agenda kegiatan yang tersedia. Silakan kembali lagi nanti.
                </p>
                <a href="{{ route('home') }}" class="btn-primary inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif

    <!-- Update Info -->
    @if($updated_at)
    <div class="mt-8 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-600">
            <i class="fas fa-sync-alt mr-2"></i>
            Terakhir diperbarui: {{ $updated_at }}
        </div>
    </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
