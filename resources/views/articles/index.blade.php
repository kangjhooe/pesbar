@extends('layouts.public')

@section('title', 'Berita Terkini - ' . \App\Helpers\SettingsHelper::siteName())
@section('description', \App\Helpers\SettingsHelper::siteDescription())

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
    <header class="border-b-2 border-news-ink pb-4 mb-8">
        <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-news-ink tracking-tight">Berita Terkini</h1>
        <p class="mt-2 text-sm sm:text-base text-news-muted max-w-2xl">{{ \App\Helpers\SettingsHelper::siteDescription() }}</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-9 min-w-0">
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($articles as $article)
                    <article class="group">
                        <a href="{{ $article->publicUrl() }}" class="block aspect-[16/10] overflow-hidden bg-news-line mb-3">
                            <img
                                src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}';"
                            >
                        </a>
                        @if($article->category)
                            <a href="{{ route('categories.show', $article->category) }}" class="text-[10px] font-bold uppercase tracking-widest text-news-accent hover:underline">
                                {{ $article->category->name }}
                            </a>
                        @endif
                        <h2 class="font-display text-[15px] md:text-base font-bold leading-snug text-news-ink mt-1 line-clamp-2">
                            <a href="{{ $article->publicUrl() }}" class="hover:text-news-accent transition-colors">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="mt-1.5 text-sm text-news-muted line-clamp-2">
                            {{ Str::limit(strip_tags($article->content), 100) }}
                        </p>
                        <div class="mt-2 flex items-center gap-2 text-[11px] text-news-muted">
                            <time datetime="{{ optional($article->published_at)->toIso8601String() }}">
                                {{ $article->published_at ? $article->published_at->format('d M Y') : 'Belum dipublikasi' }}
                            </time>
                            <span aria-hidden="true">·</span>
                            <span>{{ number_format($article->views) }} views</span>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="flex justify-center border-t border-news-line pt-6">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="text-center py-20 border border-news-line">
                    <p class="font-display text-xl text-news-ink font-bold mb-2">Belum Ada Berita</p>
                    <p class="text-sm text-news-muted">Saat ini belum ada berita yang tersedia. Silakan kembali lagi nanti.</p>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-3 space-y-8" aria-label="Sidebar">
            @if($categories->count() > 0)
            <div class="border border-news-line">
                <div class="bg-news-ink text-white px-4 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Kategori</h2>
                </div>
                <ul class="divide-y divide-news-line">
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}"
                           class="flex items-center justify-between px-4 py-3 text-sm text-news-ink hover:text-news-accent transition-colors">
                            <span class="font-medium">{{ $category->name }}</span>
                            <span class="text-[11px] text-news-muted tabular-nums">{{ $category->publishedArticles()->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @php
                $popularArticles = \App\Models\Article::published()->popular()->take(5)->get();
            @endphp
            <div class="border border-news-line">
                <div class="bg-news-ink text-white px-4 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Terpopuler</h2>
                </div>
                <ol class="divide-y divide-news-line">
                    @forelse($popularArticles as $index => $popularArticle)
                    <li class="flex gap-3 px-4 py-3">
                        <span class="font-display text-2xl font-bold text-news-accent leading-none w-7 shrink-0">{{ $index + 1 }}</span>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold leading-snug text-news-ink line-clamp-2">
                                <a href="{{ $popularArticle->publicUrl() }}" class="hover:text-news-accent transition-colors" title="{{ $popularArticle->title }}">
                                    {{ $popularArticle->title }}
                                </a>
                            </h3>
                            <p class="mt-1 text-[11px] text-news-muted">{{ number_format($popularArticle->views) }} views</p>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-6 text-sm text-news-muted">Belum ada data.</li>
                    @endforelse
                </ol>
            </div>

            <div class="border border-news-line bg-news-ink text-white p-5">
                <h2 class="text-xs font-bold uppercase tracking-[0.15em] mb-2">Newsletter</h2>
                <p class="text-sm text-white/70 mb-4">Dapatkan berita terbaru langsung di email Anda.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="email"
                           name="email"
                           placeholder="Email Anda"
                           required
                           class="w-full px-3 py-2 bg-white text-news-ink text-sm border-0 focus:outline-none focus:ring-2 focus:ring-news-accent">
                    <button type="submit"
                            class="w-full bg-news-accent text-white font-semibold py-2 text-sm hover:bg-red-800 transition-colors">
                        Berlangganan
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection
