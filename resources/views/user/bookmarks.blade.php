@extends('layouts.user')

@section('title', 'Bookmark Saya')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">Bookmark Saya</h1>
    <p class="text-news-muted mt-1">Artikel yang telah Anda simpan</p>
</div>

@if($bookmarks->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-8">
        @foreach($bookmarks as $bookmark)
        <article class="bg-white border border-news-line overflow-hidden hover:border-news-ink transition-colors flex flex-col">
            @if($bookmark->article->featured_image)
            <a href="{{ $bookmark->article->publicUrl() }}" class="aspect-video bg-news-line overflow-hidden block">
                <img src="{{ asset('storage/' . $bookmark->article->featured_image) }}"
                     alt="{{ $bookmark->article->title }}"
                     class="w-full h-full object-cover">
            </a>
            @else
            <div class="aspect-video bg-news-ink flex items-center justify-center">
                <i class="fas fa-newspaper text-white/40 text-3xl"></i>
            </div>
            @endif

            <div class="p-4 sm:p-5 flex flex-col flex-1">
                @if($bookmark->article->category)
                <a href="{{ route('categories.show', $bookmark->article->category) }}"
                   class="text-[10px] font-bold uppercase tracking-wider text-news-accent hover:underline mb-2 inline-block">
                    {{ $bookmark->article->category->name }}
                </a>
                @endif

                <h2 class="font-display text-base font-bold text-news-ink leading-snug mb-2 line-clamp-2">
                    <a href="{{ $bookmark->article->publicUrl() }}" class="hover:text-news-accent transition-colors">
                        {{ $bookmark->article->title }}
                    </a>
                </h2>

                <p class="text-news-muted text-sm leading-relaxed mb-4 line-clamp-3 flex-1">
                    {{ Str::limit(strip_tags($bookmark->article->excerpt ?? $bookmark->article->content), 120) }}
                </p>

                <div class="flex items-center justify-between text-xs text-news-muted pt-3 border-t border-news-line">
                    <span>
                        {{ $bookmark->article->published_at ? $bookmark->article->published_at->format('d M Y') : 'Belum dipublikasi' }}
                    </span>
                    <a href="{{ $bookmark->article->publicUrl() }}"
                       class="font-bold uppercase tracking-wide text-news-ink hover:text-news-accent">
                        Baca
                    </a>
                </div>
                <p class="text-[11px] text-news-muted mt-2">
                    Disimpan {{ $bookmark->created_at->format('d M Y') }}
                </p>
            </div>
        </article>
        @endforeach
    </div>

    <div class="flex justify-center">
        {{ $bookmarks->links() }}
    </div>
@else
    <div class="bg-white border border-news-line p-10 sm:p-12 text-center">
        <i class="fas fa-bookmark text-news-line text-4xl mb-4"></i>
        <h3 class="font-display text-xl font-bold text-news-ink mb-2">Belum Ada Bookmark</h3>
        <p class="text-news-muted mb-6">Simpan artikel favorit untuk dibaca nanti</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-5 py-2.5 text-sm font-semibold transition-colors">
            Ke Beranda
        </a>
    </div>
@endif
@endsection
