@extends('layouts.user')

@section('title', 'Riwayat Membaca')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">Riwayat Membaca</h1>
    <p class="text-news-muted mt-1">Artikel yang telah Anda baca</p>
</div>

@if($history->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-8">
        @foreach($history as $item)
        <article class="bg-white border border-news-line overflow-hidden hover:border-news-ink transition-colors flex flex-col">
            @if($item->article->featured_image)
            <a href="{{ $item->article->publicUrl() }}" class="aspect-video bg-news-line overflow-hidden block">
                <img src="{{ asset('storage/' . $item->article->featured_image) }}"
                     alt="{{ $item->article->title }}"
                     class="w-full h-full object-cover">
            </a>
            @else
            <div class="aspect-video bg-news-ink flex items-center justify-center">
                <i class="fas fa-newspaper text-white/40 text-3xl"></i>
            </div>
            @endif

            <div class="p-4 sm:p-5 flex flex-col flex-1">
                @if($item->article->category)
                <a href="{{ route('categories.show', $item->article->category) }}"
                   class="text-[10px] font-bold uppercase tracking-wider text-news-accent hover:underline mb-2 inline-block">
                    {{ $item->article->category->name }}
                </a>
                @endif

                <h2 class="font-display text-base font-bold text-news-ink leading-snug mb-2 line-clamp-2">
                    <a href="{{ $item->article->publicUrl() }}" class="hover:text-news-accent transition-colors">
                        {{ $item->article->title }}
                    </a>
                </h2>

                <p class="text-news-muted text-sm leading-relaxed mb-4 line-clamp-3 flex-1">
                    {{ Str::limit(strip_tags($item->article->excerpt ?? $item->article->content), 120) }}
                </p>

                <div class="flex items-center justify-between text-xs text-news-muted pt-3 border-t border-news-line">
                    <span>
                        @if($item->article->author)
                            {{ $item->article->author->name }}
                        @else
                            —
                        @endif
                    </span>
                    <a href="{{ $item->article->publicUrl() }}"
                       class="font-bold uppercase tracking-wide text-news-ink hover:text-news-accent">
                        Baca Lagi
                    </a>
                </div>
                <p class="text-[11px] text-news-muted mt-2">
                    Dibaca {{ $item->read_at->format('d M Y, H:i') }}
                </p>
            </div>
        </article>
        @endforeach
    </div>

    <div class="flex justify-center">
        {{ $history->links() }}
    </div>
@else
    <div class="bg-white border border-news-line p-10 sm:p-12 text-center">
        <i class="fas fa-history text-news-line text-4xl mb-4"></i>
        <h3 class="font-display text-xl font-bold text-news-ink mb-2">Belum Ada Riwayat</h3>
        <p class="text-news-muted mb-6">Mulai baca artikel untuk melihat riwayat di sini</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-5 py-2.5 text-sm font-semibold transition-colors">
            Ke Beranda
        </a>
    </div>
@endif
@endsection
