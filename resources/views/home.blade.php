@extends('layouts.public')

@section('title', $siteTitle)
@section('description', $siteDescription)

@section('content')
<div class="portal-home">
    {{-- Breaking ticker (multi-item + animasi) --}}
    @if($breakingNews->isNotEmpty())
    <div
        class="breaking-bar bg-news-ink text-white border-b-4 border-news-accent"
        x-data="{
            index: 0,
            total: {{ $breakingNews->count() }},
            paused: false,
            timer: null,
            init() {
                if (this.total > 1) {
                    this.timer = setInterval(() => { if (!this.paused) this.next() }, 4800);
                }
            },
            next() { this.index = (this.index + 1) % this.total },
            prev() { this.index = (this.index - 1 + this.total) % this.total },
            go(i) { this.index = i }
        }"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        role="region"
        aria-label="Breaking news"
        aria-live="polite"
    >
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-2.5 flex items-center gap-2 sm:gap-3 min-w-0">
            <span class="breaking-label shrink-0 inline-flex items-center gap-1.5 bg-news-accent text-white text-[10px] sm:text-[11px] font-bold tracking-wider uppercase px-2 sm:px-2.5 py-1">
                <span class="breaking-pulse" aria-hidden="true"></span>
                Breaking
            </span>

            <div class="relative flex-1 min-w-0 h-6 sm:h-7 overflow-hidden">
                @foreach($breakingNews as $i => $item)
                <a
                    href="{{ $item->publicUrl() }}"
                    class="absolute inset-0 flex items-center text-sm md:text-base font-semibold hover:underline line-clamp-1"
                    x-show="index === {{ $i }}"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    @if($i > 0) style="display: none;" @endif
                >
                    {{ $item->title }}
                </a>
                @endforeach
            </div>

            @if($breakingNews->count() > 1)
            <div class="shrink-0 flex items-center gap-1.5 sm:gap-2">
                <span class="hidden sm:inline text-[10px] text-white/50 font-medium tabular-nums" x-text="(index + 1) + '/' + total"></span>
                <button type="button" class="breaking-nav touch-target p-1 text-white/60 hover:text-white" @click="prev()" aria-label="Berita sebelumnya">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <div class="flex items-center gap-1" role="tablist" aria-label="Daftar breaking">
                    @foreach($breakingNews as $i => $item)
                    <button
                        type="button"
                        class="breaking-dot"
                        :class="index === {{ $i }} ? 'is-active' : ''"
                        @click="go({{ $i }})"
                        aria-label="Breaking {{ $i + 1 }}: {{ Str::limit($item->title, 40) }}"
                        :aria-selected="(index === {{ $i }}).toString()"
                    ></button>
                    @endforeach
                </div>
                <button type="button" class="breaking-nav touch-target p-1 text-white/60 hover:text-white" @click="next()" aria-label="Berita berikutnya">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    <style>
        .breaking-label { position: relative; }
        .breaking-pulse {
            width: 6px; height: 6px; border-radius: 9999px;
            background: #fff; display: inline-block;
            box-shadow: 0 0 0 0 rgba(255,255,255,0.7);
            animation: breaking-pulse 1.6s ease-out infinite;
        }
        @keyframes breaking-pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255,255,255,0.55); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255,255,255,0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }
        .breaking-dot {
            width: 6px; height: 6px; border-radius: 9999px;
            background: rgba(255,255,255,0.35); border: 0; padding: 0;
            transition: background 0.2s, transform 0.2s;
        }
        .breaking-dot.is-active {
            background: #fff; transform: scale(1.25);
        }
        .breaking-bar .breaking-nav:focus-visible,
        .breaking-bar .breaking-dot:focus-visible {
            outline: 2px solid #fff; outline-offset: 2px;
        }
        @media (prefers-reduced-motion: reduce) {
            .breaking-pulse { animation: none; }
        }
    </style>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Main column --}}
            <div class="lg:col-span-9 space-y-10">
                {{-- Hero: slider headline + 3 berita samping --}}
                @if($headlines->isNotEmpty())
                <section class="grid grid-cols-1 md:grid-cols-12 gap-5 md:gap-6" aria-label="Headline">
                    <div
                        class="md:col-span-8 relative"
                        x-data="{
                            index: 0,
                            total: {{ $headlines->count() }},
                            paused: false,
                            timer: null,
                            init() {
                                if (this.total > 1) {
                                    this.timer = setInterval(() => { if (!this.paused) this.next() }, 4000);
                                }
                            },
                            next() { this.index = (this.index + 1) % this.total },
                            prev() { this.index = (this.index - 1 + this.total) % this.total },
                            go(i) { this.index = i }
                        }"
                        @mouseenter="paused = true"
                        @mouseleave="paused = false"
                        role="region"
                        aria-roledescription="carousel"
                        aria-label="Headline utama"
                    >
                        <div class="relative overflow-hidden bg-news-ink aspect-[16/10]">
                            @foreach($headlines as $i => $slide)
                            <article
                                class="absolute inset-0"
                                x-show="index === {{ $i }}"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                @if($i > 0) style="display: none;" @endif
                            >
                                <a href="{{ $slide->publicUrl() }}" class="group block relative h-full w-full">
                                    <img
                                        src="{{ $slide->featured_image ? asset('storage/' . $slide->featured_image) : asset('images/default-news.jpg') }}"
                                        alt="{{ $slide->title }}"
                                        class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:scale-[1.02] transition-transform duration-500"
                                        @if($i > 0) loading="lazy" @endif
                                        onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}';"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-5 md:p-7 pr-16 md:pr-20">
                                        @if($slide->category)
                                            <span class="inline-block bg-white text-news-accent text-[11px] font-bold uppercase tracking-widest px-2 py-0.5 mb-2 shadow-sm">
                                                {{ $slide->category->name }}
                                            </span>
                                        @endif
                                        <{{ $i === 0 ? 'h1' : 'h2' }} class="font-display text-2xl md:text-4xl lg:text-[2.6rem] leading-tight text-white font-bold line-clamp-3">
                                            {{ $slide->title }}
                                        </{{ $i === 0 ? 'h1' : 'h2' }}>
                                        @if($slide->excerpt)
                                            <p class="mt-2 text-sm md:text-base text-white/80 line-clamp-2 max-w-2xl">
                                                {{ $slide->excerpt }}
                                            </p>
                                        @endif
                                        <div class="mt-3 text-xs text-white/60 flex items-center gap-3">
                                            <span>{{ $slide->author->name ?? 'Redaksi' }}</span>
                                            <span aria-hidden="true">·</span>
                                            <time datetime="{{ optional($slide->published_at)->toIso8601String() }}">{{ $slide->formatted_date }}</time>
                                        </div>
                                    </div>
                                </a>
                            </article>
                            @endforeach

                            @if($headlines->count() > 1)
                            <div class="absolute bottom-4 right-4 z-10 flex items-center gap-2">
                                <button type="button" class="w-8 h-8 bg-black/50 hover:bg-news-accent text-white flex items-center justify-center transition-colors" @click="prev()" aria-label="Slide sebelumnya">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                                <div class="flex items-center gap-1.5 px-1" role="tablist" aria-label="Slide headline">
                                    @foreach($headlines as $i => $slide)
                                    <button
                                        type="button"
                                        class="hero-dot"
                                        :class="index === {{ $i }} ? 'is-active' : ''"
                                        @click="go({{ $i }})"
                                        aria-label="Headline {{ $i + 1 }}"
                                        :aria-selected="(index === {{ $i }}).toString()"
                                    ></button>
                                    @endforeach
                                </div>
                                <button type="button" class="w-8 h-8 bg-black/50 hover:bg-news-accent text-white flex items-center justify-center transition-colors" @click="next()" aria-label="Slide berikutnya">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="md:col-span-4 flex flex-col divide-y divide-news-line border-t md:border-t-0 md:border-l border-news-line md:pl-5">
                        @forelse($sideNews as $side)
                        <article class="py-3 first:pt-0 last:pb-0 group">
                            @if($side->category)
                                <a href="{{ route('categories.show', $side->category) }}" class="text-[10px] font-bold uppercase tracking-widest text-news-accent hover:underline">
                                    {{ $side->category->name }}
                                </a>
                            @endif
                            <h2 class="font-display text-base md:text-lg leading-snug font-bold text-news-ink mt-1 line-clamp-2">
                                <a href="{{ $side->publicUrl() }}" class="hover:text-news-accent transition-colors">
                                    {{ $side->title }}
                                </a>
                            </h2>
                            <time class="block mt-1 text-[11px] text-news-muted" datetime="{{ optional($side->published_at)->toIso8601String() }}">
                                {{ $side->formatted_date }}
                            </time>
                        </article>
                        @empty
                        <p class="text-sm text-news-muted py-4">Belum ada berita samping.</p>
                        @endforelse
                    </div>
                </section>

                <style>
                    .hero-dot {
                        width: 7px; height: 7px; border-radius: 9999px;
                        background: rgba(255,255,255,0.4); border: 0; padding: 0;
                        transition: background 0.2s, transform 0.2s;
                    }
                    .hero-dot.is-active {
                        background: #fff; transform: scale(1.3);
                    }
                </style>
                @endif

                {{-- Pilihan Redaksi --}}
                @if($editorPicks->isNotEmpty())
                <section class="border-t-2 border-news-ink pt-5" aria-labelledby="editor-picks">
                    <div class="flex items-baseline justify-between gap-4 mb-4">
                        <h2 id="editor-picks" class="font-display text-xl md:text-2xl font-bold text-news-ink tracking-tight">
                            Pilihan Redaksi
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($editorPicks as $pick)
                        <article class="group flex gap-4">
                            <a href="{{ $pick->publicUrl() }}" class="w-28 sm:w-32 shrink-0 aspect-[4/3] overflow-hidden bg-news-line">
                                <img
                                    src="{{ $pick->featured_image ? asset('storage/' . $pick->featured_image) : asset('images/default-news.jpg') }}"
                                    alt=""
                                    class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}';"
                                >
                            </a>
                            <div class="min-w-0 flex flex-col justify-center">
                                @if($pick->category)
                                    <a href="{{ route('categories.show', $pick->category) }}" class="text-[10px] font-bold uppercase tracking-widest text-news-accent hover:underline">
                                        {{ $pick->category->name }}
                                    </a>
                                @endif
                                <h3 class="font-display text-base md:text-lg font-bold leading-snug text-news-ink mt-1 line-clamp-2">
                                    <a href="{{ $pick->publicUrl() }}" class="hover:text-news-accent transition-colors">
                                        {{ $pick->title }}
                                    </a>
                                </h3>
                                <time class="block mt-1.5 text-[11px] text-news-muted" datetime="{{ optional($pick->published_at)->toIso8601String() }}">
                                    {{ $pick->formatted_date }}
                                </time>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Agenda Minggu Ini --}}
                @if($weekEvents->isNotEmpty())
                <section class="border-t-2 border-news-ink pt-5" aria-labelledby="week-agenda">
                    <div class="flex items-baseline justify-between gap-4 mb-4">
                        <h2 id="week-agenda" class="font-display text-xl md:text-2xl font-bold text-news-ink tracking-tight">
                            Agenda Minggu Ini
                        </h2>
                        <a href="{{ route('events.index') }}" class="text-xs font-bold uppercase tracking-wider text-news-accent hover:underline shrink-0">
                            Lihat semua
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($weekEvents as $event)
                        <article class="flex gap-3 border border-news-line p-3 hover:border-news-ink transition-colors">
                            <div class="w-14 shrink-0 text-center border-r border-news-line pr-3 flex flex-col justify-center">
                                <span class="font-display text-2xl font-bold text-news-accent leading-none">{{ $event->event_date->format('d') }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-news-muted mt-1">{{ $event->event_date->format('M') }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-news-accent">{{ $event->event_type_label }}</span>
                                <h3 class="font-display text-sm md:text-base font-bold leading-snug text-news-ink mt-0.5 line-clamp-2" title="{{ $event->title }}">
                                    {{ $event->title }}
                                </h3>
                                <p class="mt-1 text-[11px] text-news-muted flex flex-wrap gap-x-2 gap-y-0.5">
                                    @if($event->start_time)
                                        <span><i class="far fa-clock mr-0.5"></i>{{ $event->formatted_start_time }}@if($event->end_time)–{{ $event->formatted_end_time }}@endif</span>
                                    @endif
                                    @if($event->location)
                                        <span><i class="fas fa-map-marker-alt mr-0.5"></i>{{ Str::limit($event->location, 40) }}</span>
                                    @endif
                                </p>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Sections per kategori --}}
                @foreach($categorySections as $section)
                <section class="border-t-2 border-news-ink pt-5" aria-labelledby="cat-{{ $section['category']->slug }}">
                    <div class="flex items-baseline justify-between gap-4 mb-4">
                        <h2 id="cat-{{ $section['category']->slug }}" class="font-display text-xl md:text-2xl font-bold text-news-ink tracking-tight">
                            {{ $section['category']->name }}
                        </h2>
                        <a href="{{ route('categories.show', $section['category']) }}" class="text-xs font-bold uppercase tracking-wider text-news-accent hover:underline shrink-0">
                            Lihat semua
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-5">
                        @foreach($section['articles'] as $article)
                        <article class="group min-w-0">
                            <a href="{{ $article->publicUrl() }}" class="block aspect-[16/10] overflow-hidden bg-news-line mb-2 sm:mb-3">
                                <img
                                    src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}"
                                    alt="{{ $article->title }}"
                                    class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/default-news.jpg') }}';"
                                >
                            </a>
                            <h3 class="font-display text-[13px] sm:text-[15px] md:text-base font-bold leading-snug text-news-ink line-clamp-2">
                                <a href="{{ $article->publicUrl() }}" class="hover:text-news-accent transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <time class="block mt-1 sm:mt-1.5 text-[10px] sm:text-[11px] text-news-muted" datetime="{{ optional($article->published_at)->toIso8601String() }}">
                                {{ $article->formatted_date }}
                            </time>
                        </article>
                        @endforeach
                    </div>
                </section>
                @endforeach

                {{-- Penulis aktif --}}
                @if($activeAuthors->isNotEmpty())
                <section class="border-t-2 border-news-ink pt-5" aria-labelledby="active-authors">
                    <div class="flex items-baseline justify-between gap-4 mb-4">
                        <h2 id="active-authors" class="font-display text-xl md:text-2xl font-bold text-news-ink tracking-tight">
                            Penulis
                        </h2>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                        @foreach($activeAuthors as $author)
                        <div class="text-center">
                            <a href="{{ route('penulis.public-profile', $author) }}" class="group block">
                                @if($author->profile && $author->profile->avatar)
                                    <img
                                        src="{{ asset('storage/' . $author->profile->avatar) }}"
                                        alt="{{ $author->name }}"
                                        class="w-16 h-16 mx-auto object-cover border border-news-line group-hover:border-news-accent transition-colors"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-16 h-16 mx-auto bg-news-ink text-white flex items-center justify-center font-display text-xl font-bold group-hover:bg-news-accent transition-colors">
                                        {{ strtoupper(substr($author->name, 0, 1)) }}
                                    </div>
                                @endif
                                <p class="mt-2 text-sm font-bold text-news-ink group-hover:text-news-accent transition-colors line-clamp-1">{{ $author->name }}</p>
                                <p class="text-[11px] text-news-muted">{{ $author->articles_count }} artikel</p>
                            </a>
                            <x-follow-button :user="$author" compact />
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                @if($categorySections->isEmpty() && $headlines->isEmpty())
                <div class="text-center py-20 border border-news-line">
                    <p class="font-display text-xl text-news-ink font-bold mb-2">Belum ada berita</p>
                    <p class="text-sm text-news-muted">Konten akan muncul di sini setelah dipublikasikan.</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-3 space-y-8" aria-label="Sidebar">
                <div class="border border-news-line">
                    <div class="bg-news-ink text-white px-4 py-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Terpopuler</h2>
                    </div>
                    <ol class="divide-y divide-news-line">
                        @forelse($popularArticles as $index => $article)
                        <li class="flex gap-3 px-4 py-3 group">
                            <span class="font-display text-2xl font-bold text-news-accent leading-none w-7 shrink-0">{{ $index + 1 }}</span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold leading-snug text-news-ink line-clamp-2">
                                    <a href="{{ $article->publicUrl() }}" class="hover:text-news-accent transition-colors" title="{{ $article->title }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-[11px] text-news-muted">{{ number_format($article->views) }} views</p>
                            </div>
                        </li>
                        @empty
                        <li class="px-4 py-6 text-sm text-news-muted">Belum ada data.</li>
                        @endforelse
                    </ol>
                </div>

                <div class="border border-news-line">
                    <div class="bg-news-ink text-white px-4 py-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Terkini</h2>
                    </div>
                    <ul class="divide-y divide-news-line">
                        @forelse($latestSidebar as $article)
                        <li class="flex gap-3 px-4 py-3 group">
                            <a href="{{ $article->publicUrl() }}" class="w-16 h-16 shrink-0 overflow-hidden bg-news-line">
                                <img
                                    src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}"
                                    alt=""
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            </a>
                            <div class="min-w-0">
                                @if($article->category)
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-news-accent">{{ $article->category->name }}</span>
                                @endif
                                <h3 class="text-sm font-bold leading-snug text-news-ink mt-0.5 line-clamp-2">
                                    <a href="{{ $article->publicUrl() }}" class="hover:text-news-accent transition-colors" title="{{ $article->title }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <time class="block mt-1 text-[11px] text-news-muted">{{ $article->formatted_date }}</time>
                            </div>
                        </li>
                        @empty
                        <li class="px-4 py-6 text-sm text-news-muted">Belum ada data.</li>
                        @endforelse
                    </ul>
                </div>

                @include('widgets.weather')
                @include('widgets.prayer')
                @include('widgets.maritime', ['maritimeData' => $maritimeData ?? [], 'isHome' => true])
                @include('widgets.events')
                @include('widgets.poll')
                @include('widgets.contact-important')
                @include('widgets.newsletter')
            </aside>
        </div>
    </div>
</div>
@endsection
