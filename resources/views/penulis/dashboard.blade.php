@extends('layouts.penulis')

@section('title', 'Dashboard Penulis')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan performa konten Anda')

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">
                Halo, {{ auth()->user()->name }}
            </h1>
            <p class="text-news-muted mt-1">Ringkasan performa konten Anda</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('penulis.articles.index') }}"
               class="inline-flex items-center border border-news-line px-4 py-2.5 text-sm font-semibold text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
                <i class="fas fa-newspaper mr-2 text-xs"></i>
                Artikel Saya
            </a>
            <a href="{{ route('penulis.articles.create') }}"
               class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                <i class="fas fa-plus mr-2 text-xs"></i>
                Buat Artikel
            </a>
        </div>
    </div>
</div>

@endauth

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
    <a href="{{ route('penulis.articles.index') }}" class="bg-white border border-news-line border-t-2 border-t-news-ink p-4 sm:p-5 hover:border-news-ink transition-colors">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Total Artikel</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ $stats['total_articles'] }}</p>
    </a>
    <a href="{{ route('penulis.articles.index', ['status' => 'published']) }}" class="bg-white border border-news-line border-t-2 border-t-news-ink p-4 sm:p-5 hover:border-news-ink transition-colors">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Terbit</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ $stats['published_articles'] }}</p>
    </a>
    <a href="{{ route('penulis.articles.index', ['status' => 'suspended']) }}" class="bg-white border border-news-line border-t-2 border-t-orange-500 p-4 sm:p-5 hover:border-news-ink transition-colors">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Ditangguhkan</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ $stats['suspended_articles'] }}</p>
    </a>
    <div class="bg-white border border-news-line border-t-2 border-t-news-accent p-4 sm:p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Total Views</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ number_format($stats['total_views']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
    <a href="{{ route('penulis.articles.index', ['status' => 'draft']) }}" class="bg-white border border-news-line p-4 flex items-center justify-between hover:border-news-ink transition-colors">
        <div>
            <p class="text-xs text-news-muted uppercase tracking-wide font-semibold">Draft</p>
            <p class="text-2xl font-bold text-news-ink tabular-nums">{{ $stats['draft_articles'] ?? 0 }}</p>
        </div>
        <i class="fas fa-pen text-news-muted"></i>
    </a>
    <a href="{{ route('penulis.comments.advanced') }}" class="bg-white border border-news-line p-4 flex items-center justify-between hover:border-news-ink transition-colors">
        <div>
            <p class="text-xs text-news-muted uppercase tracking-wide font-semibold">Komentar</p>
            <p class="text-2xl font-bold text-news-ink tabular-nums">{{ number_format($stats['total_comments']) }}</p>
        </div>
        <i class="fas fa-comments text-news-muted"></i>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    {{-- Recent --}}
    <div class="bg-white border border-news-line">
        <div class="px-4 sm:px-5 py-4 border-b border-news-line flex items-center justify-between gap-3">
            <div>
                <h2 class="font-display text-lg font-bold text-news-ink">Artikel Terbaru</h2>
                <p class="text-sm text-news-muted mt-0.5">5 terakhir diperbarui</p>
            </div>
            <a href="{{ route('penulis.articles.index') }}" class="text-sm font-semibold text-news-accent hover:text-news-ink shrink-0">
                Lihat semua
            </a>
        </div>
        @if($recentArticles->count() > 0)
            <div class="divide-y divide-news-line">
                @foreach($recentArticles as $article)
                <a href="{{ route('penulis.articles.show', $article) }}"
                   class="flex items-center gap-3 px-4 sm:px-5 py-3.5 hover:bg-news-paper transition-colors group">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-news-ink group-hover:text-news-accent transition-colors line-clamp-1" title="{{ $article->title }}">
                            {{ $article->title }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-news-muted">
                            <span>{{ $article->category->name ?? 'Tanpa Kategori' }}</span>
                            <span>{{ $article->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-news-muted group-hover:text-news-accent text-xs shrink-0"></i>
                </a>
                @endforeach
            </div>
        @else
            <div class="px-4 sm:px-5 py-10 text-center">
                <p class="text-sm text-news-muted mb-3">Belum ada artikel</p>
                <a href="{{ route('penulis.articles.create') }}" class="text-sm font-semibold text-news-accent hover:text-news-ink">
                    Buat artikel pertama
                </a>
            </div>
        @endif
    </div>

    {{-- Popular --}}
    <div class="bg-white border border-news-line">
        <div class="px-4 sm:px-5 py-4 border-b border-news-line flex items-center justify-between gap-3">
            <div>
                <h2 class="font-display text-lg font-bold text-news-ink">Artikel Populer</h2>
                <p class="text-sm text-news-muted mt-0.5">Views terbanyak</p>
            </div>
            <a href="{{ route('penulis.articles.index', ['sort_by' => 'views']) }}" class="text-sm font-semibold text-news-accent hover:text-news-ink shrink-0">
                Lihat semua
            </a>
        </div>
        @if($popularArticles->count() > 0)
            <div class="divide-y divide-news-line">
                @foreach($popularArticles as $index => $popularArticle)
                <a href="{{ route('penulis.articles.show', $popularArticle) }}"
                   class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 hover:bg-news-paper transition-colors group">
                    <span class="w-7 h-7 shrink-0 bg-news-ink text-white flex items-center justify-center text-xs font-bold tabular-nums">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-news-ink group-hover:text-news-accent transition-colors line-clamp-1" title="{{ $popularArticle->title }}">
                            {{ $popularArticle->title }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-news-muted">
                            <span class="inline-flex items-center">
                                <i class="fas fa-eye mr-1"></i>
                                {{ number_format($popularArticle->views) }}
                            </span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-news-muted group-hover:text-news-accent text-xs shrink-0"></i>
                </a>
                @endforeach
            </div>
        @else
            <div class="px-4 sm:px-5 py-10 text-center">
                <p class="text-sm text-news-muted">Belum ada artikel terbit</p>
            </div>
        @endif
    </div>
</div>
@endsection
