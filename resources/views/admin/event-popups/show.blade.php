@extends('layouts.admin-simple')

@section('title', 'Detail Event Popup')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.event-popups.index') }}" class="text-news-muted hover:text-news-ink mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-news-ink">Detail Event Popup</h1>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Event Details -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-news-ink mb-1">Judul Event</label>
                        <p class="text-lg font-semibold text-news-ink">{{ $eventPopup->title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-news-ink mb-1">Pesan</label>
                        <div class="bg-news-paper p-4 rounded-lg">
                            <p class="text-news-ink leading-relaxed">{{ $eventPopup->message }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-news-ink mb-1">Tanggal Mulai</label>
                            <p class="text-news-ink">{{ $eventPopup->start_date->format('d-m-Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-news-ink mb-1">Tanggal Selesai</label>
                            <p class="text-news-ink">{{ $eventPopup->end_date->format('d-m-Y') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-news-ink mb-1">Status</label>
                        @if($eventPopup->status)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-800">
                                <i class="fas fa-check-circle mr-2"></i>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i>Non-Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Event Status & Actions -->
                <div class="space-y-4">
                    <div class="bg-news-paper p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-news-ink mb-2">Status Event</h3>
                        @if($eventPopup->isActive())
                            <div class="flex items-center text-emerald-700">
                                <i class="fas fa-play-circle mr-2"></i>
                                <span class="font-medium">Sedang Berjalan</span>
                            </div>
                            <p class="text-sm text-news-muted mt-1">Event popup sedang aktif dan akan ditampilkan kepada pengunjung</p>
                        @else
                            <div class="flex items-center text-news-muted">
                                <i class="fas fa-pause-circle mr-2"></i>
                                <span class="font-medium">Tidak Aktif</span>
                            </div>
                            <p class="text-sm text-news-ink mt-1">
                                @if(!$eventPopup->status)
                                    Event popup dinonaktifkan
                                @elseif($eventPopup->start_date > now())
                                    Event popup belum dimulai
                                @else
                                    Event popup sudah berakhir
                                @endif
                            </p>
                        @endif
                    </div>

                    <div class="bg-news-paper p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-news-ink mb-2">Informasi Tambahan</h3>
                        <div class="space-y-2 text-sm text-news-muted">
                            <div class="flex justify-between">
                                <span>Dibuat:</span>
                                <span>{{ $eventPopup->created_at->format('d-m-Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Diperbarui:</span>
                                <span>{{ $eventPopup->updated_at->format('d-m-Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Durasi:</span>
                                <span>{{ $eventPopup->start_date->diffInDays($eventPopup->end_date) + 1 }} hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('admin.event-popups.edit', $eventPopup) }}" class="flex-1 bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors text-center">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <form action="{{ route('admin.event-popups.toggle-status', $eventPopup) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full btn-secondary">
                                <i class="fas fa-toggle-{{ $eventPopup->status ? 'on' : 'off' }} mr-2"></i>
                                {{ $eventPopup->status ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
