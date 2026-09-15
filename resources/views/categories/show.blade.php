@extends('layouts.public')

@section('title', $category->name . ' - ' . \App\Helpers\SettingsHelper::siteName())
@section('description', $category->description ?: ('Berita kategori ' . $category->name . ' di ' . \App\Helpers\SettingsHelper::siteName()))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-news-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-news-accent transition-colors">Beranda</a>
        <span aria-hidden="true" class="text-news-line">/</span>
        <span class="text-news-ink font-medium break-words">{{ $category->name }}</span>
    </nav>

    <header class="border-b-2 border-news-ink pb-4 mb-8">
        <div class="flex items-start gap-3 sm:gap-4">
            @if($category->icon)
            <div class="w-10 h-10 sm:w-12 sm:h-12 border border-news-line flex items-center justify-center shrink-0 text-news-accent">
                <i class="{{ $category->icon }} text-lg sm:text-xl"></i>
            </div>
            @endif
            <div class="min-w-0">
                <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-news-ink tracking-tight break-words">{{ $category->name }}</h1>
                @if($category->description)
                <p class="mt-2 text-sm sm:text-base text-news-muted">{{ $category->description }}</p>
                @endif
                <p class="mt-2 text-[11px] font-bold uppercase tracking-wider text-news-muted">{{ $articles->total() }} artikel</p>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-9 min-w-0">
            @if($articles->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 mb-8">
                    @foreach($articles as $article)
                    <article class="group min-w-0">
                        <a href="{{ $article->publicUrl() }}" class="block aspect-[4/3] overflow-hidden bg-news-line mb-2">
                            <img
                                src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}';"
                            >
                        </a>
                        <h2 class="font-display text-[13px] sm:text-sm font-bold leading-snug text-news-ink line-clamp-2">
                            <a href="{{ $article->publicUrl() }}" class="hover:text-news-accent transition-colors">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="mt-1 text-xs text-news-muted line-clamp-2 hidden sm:block">
                            {{ Str::limit(strip_tags($article->content), 90) }}
                        </p>
                        <div class="mt-1.5 flex items-center gap-1.5 text-[10px] sm:text-[11px] text-news-muted">
                            <time datetime="{{ optional($article->published_at)->toIso8601String() }}">
                                {{ $article->published_at ? $article->published_at->format('d M Y') : 'Belum dipublikasi' }}
                            </time>
                            <span aria-hidden="true" class="hidden sm:inline">·</span>
                            <span class="hidden sm:inline">{{ number_format($article->views) }} views</span>
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
                    <p class="text-sm text-news-muted mb-6">Saat ini belum ada berita dalam kategori {{ $category->name }}.</p>
                    <a href="{{ route('articles.index') }}"
                       class="inline-flex items-center gap-2 bg-news-ink text-white px-4 py-2 text-sm font-semibold hover:bg-news-accent transition-colors">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Lihat Semua Berita
                    </a>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-3 space-y-8" aria-label="Sidebar">
            @php
                $allCategories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
            @endphp

            @if($allCategories->count() > 0)
            <div class="border border-news-line">
                <div class="bg-news-ink text-white px-4 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Semua Kategori</h2>
                </div>
                <ul class="divide-y divide-news-line">
                    @foreach($allCategories as $cat)
                    <li>
                        <a href="{{ route('categories.show', $cat) }}"
                           class="flex items-center justify-between px-4 py-3 text-sm transition-colors {{ $cat->id === $category->id ? 'text-news-accent font-bold bg-news-paper' : 'text-news-ink hover:text-news-accent' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[11px] text-news-muted tabular-nums">{{ $cat->publishedArticles()->count() }}</span>
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
                <ul class="divide-y divide-news-line">
                    @forelse($popularArticles as $popularArticle)
                    <li class="flex gap-3 px-4 py-3">
                        <a href="{{ $popularArticle->publicUrl() }}" class="w-16 h-16 shrink-0 overflow-hidden bg-news-line">
                            <img
                                src="{{ $popularArticle->featured_image ? asset('storage/' . $popularArticle->featured_image) : asset('images/default-news.jpg') }}"
                                alt=""
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        </a>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold leading-snug text-news-ink line-clamp-2">
                                <a href="{{ $popularArticle->publicUrl() }}" class="hover:text-news-accent transition-colors" title="{{ $popularArticle->title }}">
                                    {{ $popularArticle->title }}
                                </a>
                            </h3>
                            <time class="block mt-1 text-[11px] text-news-muted">
                                {{ $popularArticle->published_at ? $popularArticle->published_at->format('d M Y') : 'Belum dipublikasi' }}
                            </time>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-6 text-sm text-news-muted">Belum ada data.</li>
                    @endforelse
                </ul>
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
