@extends('layouts.admin-simple')

@section('title', 'Detail Event - Admin Panel')
@section('page-title', 'Detail Event')
@section('page-subtitle', $event->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-news-line overflow-hidden">
        <!-- Event Header -->
        <div class="px-6 py-4 border-b border-news-line bg-news-paper">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-news-ink">{{ $event->title }}</h1>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-news-muted">
                        @if($event->organizer)
                        <span class="flex items-center">
                            <i class="fas fa-user-tie mr-1"></i>
                            <span class="text-news-muted mr-1">Penyelenggara:</span>
                            <span class="font-medium text-news-ink">{{ $event->organizer }}</span>
                        </span>
                        @endif
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $event->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @if($event->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            <i class="fas fa-check-circle mr-1"></i>Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-news-accent">
                            <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                        </span>
                    @endif
                    @if($event->is_public)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            <i class="fas fa-globe mr-1"></i>Publik
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            <i class="fas fa-lock mr-1"></i>Privat
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $event->priority_color }}-100 text-{{ $event->priority_color }}-800">
                        <i class="fas fa-flag mr-1"></i>{{ $event->priority_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Event Content -->
        <div class="p-6">
            <!-- Featured Image -->
            @if($event->image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $event->image) }}" 
                     alt="{{ $event->title }}" 
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif

            <!-- Event Meta -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-news-paper p-4 rounded-lg">
                    <h3 class="font-semibold text-news-ink mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-news-accent"></i>
                        Tanggal Event
                    </h3>
                    <p class="text-sm text-news-muted">
                        {{ $event->event_date->format('d M Y') }}
                        @if($event->event_date->isToday())
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                                Hari Ini
                            </span>
                        @elseif($event->event_date->isFuture())
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                                {{ $event->days_until_event > 0 ? $event->days_until_event . ' hari lagi' : 'Akan Datang' }}
                            </span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                                Sudah Berlalu
                            </span>
                        @endif
                    </p>
                </div>

                <div class="bg-news-paper p-4 rounded-lg">
                    <h3 class="font-semibold text-news-ink mb-2 flex items-center">
                        <i class="fas fa-clock mr-2 text-news-muted"></i>
                        Waktu
                    </h3>
                    <p class="text-sm text-news-muted">
                        @if($event->start_time && $event->end_time)
                            {{ $event->formatted_start_time }} - {{ $event->formatted_end_time }}
                        @elseif($event->start_time)
                            Mulai: {{ $event->formatted_start_time }}
                        @else
                            Waktu belum ditentukan
                        @endif
                    </p>
                </div>

                @if($event->location)
                <div class="bg-news-paper p-4 rounded-lg">
                    <h3 class="font-semibold text-news-ink mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-news-muted"></i>
                        Lokasi
                    </h3>
                    <p class="text-sm text-news-muted">{{ $event->location }}</p>
                </div>
                @endif

                <div class="bg-news-paper p-4 rounded-lg">
                    <h3 class="font-semibold text-news-ink mb-2 flex items-center">
                        <i class="fas fa-tag mr-2 text-news-muted"></i>
                        Tipe Event
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                        {{ $event->event_type_label }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            @if($event->description)
            <div class="mb-6">
                <h3 class="font-semibold text-news-ink mb-2">Deskripsi</h3>
                <div class="bg-news-paper p-4 rounded-lg">
                    <p class="text-news-ink leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
                </div>
            </div>
            @endif

            <!-- Contact Info -->
            @if($event->contact_info)
            <div class="mb-6">
                <h3 class="font-semibold text-news-ink mb-2 flex items-center">
                    <i class="fas fa-phone mr-2 text-news-muted"></i>
                    Informasi Kontak
                </h3>
                <div class="bg-news-paper p-4 rounded-lg">
                    <p class="text-news-ink">{{ $event->contact_info }}</p>
                </div>
            </div>
            @endif

            <!-- Event Status Info -->
            <div class="mb-6 bg-news-paper border border-news-line rounded-lg p-4">
                <h3 class="font-semibold text-news-ink mb-3">Informasi Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-news-muted">Status Event:</span>
                        <span class="ml-2 font-medium text-news-ink">
                            {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-news-muted">Visibilitas:</span>
                        <span class="ml-2 font-medium text-news-ink">
                            {{ $event->is_public ? 'Publik' : 'Privat' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-news-muted">Prioritas:</span>
                        <span class="ml-2 font-medium text-news-ink">
                            {{ $event->priority_label }}
                        </span>
                    </div>
                    <div>
                        <span class="text-news-muted">Status Tanggal:</span>
                        <span class="ml-2 font-medium text-news-ink">
                            @if($event->status === 'past')
                                Sudah Berlalu
                            @elseif($event->status === 'today')
                                Hari Ini
                            @else
                                Akan Datang
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-news-line bg-news-paper">
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="btn-secondary px-4 py-2 text-sm {{ $event->is_active ? 'text-amber-800' : '' }}">
                            <i class="fas fa-{{ $event->is_active ? 'pause' : 'play' }} mr-1"></i>
                            {{ $event->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.events.edit', $event) }}" 
                       class="bg-news-accent text-white px-4 py-2 hover:bg-news-ink transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" 
                          onsubmit="return window.pesbarConfirmForm(event, 'Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-news-accent text-white px-4 py-2 hover:bg-news-ink transition-colors">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>
                    </form>
                    <a href="{{ route('admin.events.index') }}" 
                       class="bg-news-ink text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

