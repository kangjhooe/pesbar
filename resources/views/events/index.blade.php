@extends('layouts.public')

@section('title', 'Agenda Kegiatan - ' . \App\Helpers\SettingsHelper::siteName())
@section('description', 'Lihat semua agenda dan kegiatan yang akan diselenggarakan di Kabupaten Pesisir Barat')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
    <header class="border-b-2 border-news-ink pb-4 mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-news-ink tracking-tight">Agenda Kegiatan</h1>
            <p class="mt-2 text-sm sm:text-base text-news-muted max-w-2xl">
                Daftar lengkap agenda dan kegiatan di Kabupaten Pesisir Barat
            </p>
        </div>
        <div class="shrink-0 text-left sm:text-right">
            <p class="font-display text-3xl font-bold text-news-accent leading-none">{{ $total_count }}</p>
            <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-news-muted">Total Kegiatan</p>
        </div>
    </header>

    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($events as $event)
            <article class="flex gap-3 border border-news-line p-4 hover:border-news-ink transition-colors">
                <div class="w-14 shrink-0 text-center border-r border-news-line pr-3 flex flex-col justify-center">
                    <span class="font-display text-2xl font-bold text-news-accent leading-none">{{ $event->event_date->format('d') }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-news-muted mt-1">{{ $event->event_date->format('M') }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-news-accent">{{ $event->event_type_label }}</span>
                    <h2 class="font-display text-base font-bold leading-snug text-news-ink mt-0.5 line-clamp-2" title="{{ $event->title }}">
                        {{ $event->title }}
                    </h2>
                    <div class="mt-2 space-y-1 text-[11px] text-news-muted">
                        @if($event->start_time)
                        <p>
                            {{ $event->formatted_start_time }}
                            @if($event->end_time)
                                – {{ $event->formatted_end_time }}
                            @endif
                        </p>
                        @endif
                        @if($event->location)
                        <p class="line-clamp-1">{{ $event->location }}</p>
                        @endif
                        @if($event->organizer)
                        <p class="line-clamp-1">{{ $event->organizer }}</p>
                        @endif
                    </div>
                    @if($event->description)
                    <p class="mt-2 text-sm text-news-muted line-clamp-2">{{ $event->description }}</p>
                    @endif
                    <p class="mt-3 text-[10px] font-bold uppercase tracking-wider {{ $event->is_active ? 'text-news-accent' : 'text-news-muted' }}">
                        {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </p>
                </div>
            </article>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 border border-news-line">
            <p class="font-display text-xl text-news-ink font-bold mb-2">Belum Ada Agenda</p>
            <p class="text-sm text-news-muted mb-6">Saat ini belum ada agenda kegiatan yang tersedia.</p>
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 bg-news-ink text-white px-4 py-2 text-sm font-semibold hover:bg-news-accent transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali ke Beranda
            </a>
        </div>
    @endif

    @if($updated_at)
    <p class="mt-8 text-center text-[11px] text-news-muted">
        Terakhir diperbarui: {{ $updated_at }}
    </p>
    @endif
</div>
@endsection
