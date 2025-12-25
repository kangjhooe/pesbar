@extends('layouts.admin-simple')

@section('title', 'Detail Event - Admin Panel')
@section('page-title', 'Detail Event')
@section('page-subtitle', $event->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Event Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                        @if($event->organizer)
                        <span class="flex items-center">
                            <i class="fas fa-user-tie mr-1"></i>
                            <span class="text-gray-500 mr-1">Penyelenggara:</span>
                            <span class="font-medium text-gray-900">{{ $event->organizer }}</span>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                        </span>
                    @endif
                    @if($event->is_public)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-globe mr-1"></i>Publik
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
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
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                        Tanggal Event
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $event->event_date->format('d M Y') }}
                        @if($event->event_date->isToday())
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Hari Ini
                            </span>
                        @elseif($event->event_date->isFuture())
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $event->days_until_event > 0 ? $event->days_until_event . ' hari lagi' : 'Akan Datang' }}
                            </span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Sudah Berlalu
                            </span>
                        @endif
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-clock mr-2 text-purple-600"></i>
                        Waktu
                    </h3>
                    <p class="text-sm text-gray-600">
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
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                        Lokasi
                    </h3>
                    <p class="text-sm text-gray-600">{{ $event->location }}</p>
                </div>
                @endif

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-tag mr-2 text-green-600"></i>
                        Tipe Event
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $event->event_type_label }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            @if($event->description)
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-2">Deskripsi</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
                </div>
            </div>
            @endif

            <!-- Contact Info -->
            @if($event->contact_info)
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-phone mr-2 text-indigo-600"></i>
                    Informasi Kontak
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700">{{ $event->contact_info }}</p>
                </div>
            </div>
            @endif

            <!-- Event Status Info -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Informasi Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600">Status Event:</span>
                        <span class="ml-2 font-medium text-gray-900">
                            {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Visibilitas:</span>
                        <span class="ml-2 font-medium text-gray-900">
                            {{ $event->is_public ? 'Publik' : 'Privat' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Prioritas:</span>
                        <span class="ml-2 font-medium text-gray-900">
                            {{ $event->priority_label }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Status Tanggal:</span>
                        <span class="ml-2 font-medium text-gray-900">
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
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $event->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                            <i class="fas fa-{{ $event->is_active ? 'pause' : 'play' }} mr-1"></i>
                            {{ $event->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.events.edit', $event) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>
                    </form>
                    <a href="{{ route('admin.events.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

