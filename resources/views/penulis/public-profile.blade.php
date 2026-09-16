@extends('layouts.public')

@section('title', 'Profil ' . $user->name)
@section('description', optional($user->profile)->bio ?: ('Profil penulis ' . $user->name . ' di Pesisir Barat Hub'))

@section('content')
@php
    $socialLinks = collect(is_array($user->profile?->social_links) ? $user->profile->social_links : [])
        ->filter(fn ($url) => filled($url));
    $hasSocial = $socialLinks->isNotEmpty() || filled($user->profile?->website);
@endphp

<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Breadcrumb --}}
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-news-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-news-accent transition-colors">Beranda</a>
        <span aria-hidden="true" class="text-news-line">/</span>
        <span class="text-news-ink font-medium">Penulis</span>
        <span aria-hidden="true" class="text-news-line">/</span>
        <span class="text-news-ink font-medium line-clamp-1">{{ $user->name }}</span>
    </nav>

    {{-- Admin verification panel (admin only) --}}
    @if(!empty($isAdmin))
    <section class="mb-6 border-2 border-news-ink bg-news-paper" aria-label="Panel admin">
        <div class="bg-news-ink text-white px-4 py-2.5 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-bold uppercase tracking-wider">Panel Verifikasi Admin</h2>
            <a href="{{ route('admin.verification-requests') }}" class="text-xs text-white/80 hover:text-white underline underline-offset-2">
                Kembali ke daftar
            </a>
        </div>
        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <div class="border border-news-line bg-white px-3 py-2.5">
                    <p class="text-[11px] uppercase tracking-wider text-news-muted mb-1">Status</p>
                    @if($user->isVerified())
                        <p class="text-sm font-semibold text-news-ink">Terverifikasi</p>
                    @elseif($user->verification_request_status === 'pending')
                        <p class="text-sm font-semibold text-news-accent">Menunggu verifikasi</p>
                    @elseif($user->verification_request_status === 'rejected')
                        <p class="text-sm font-semibold text-news-accent">Ditolak</p>
                    @else
                        <p class="text-sm font-semibold text-news-muted">Belum mengajukan</p>
                    @endif
                </div>
                <div class="border border-news-line bg-white px-3 py-2.5">
                    <p class="text-[11px] uppercase tracking-wider text-news-muted mb-1">Tipe</p>
                    <p class="text-sm font-semibold text-news-ink">
                        {{ $user->verification_type === 'lembaga' ? 'Lembaga' : ($user->verification_type === 'perorangan' ? 'Perorangan' : '—') }}
                    </p>
                </div>
                <div class="border border-news-line bg-white px-3 py-2.5">
                    <p class="text-[11px] uppercase tracking-wider text-news-muted mb-1">Tanggal request</p>
                    <p class="text-sm font-semibold text-news-ink">
                        {{ $user->verification_requested_at?->format('d M Y, H:i') ?? '—' }}
                    </p>
                </div>
            </div>

            @if($user->verification_document)
                @php
                    $documentPath = Storage::url($user->verification_document);
                    $documentExtension = strtolower(pathinfo($user->verification_document, PATHINFO_EXTENSION));
                @endphp
                <div class="mb-4 border border-news-line bg-white p-3">
                    <p class="text-[11px] uppercase tracking-wider text-news-muted mb-2">Dokumen verifikasi</p>
                    @if(in_array($documentExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <a href="{{ $documentPath }}" target="_blank" rel="noopener" class="block">
                            <img src="{{ $documentPath }}" alt="Dokumen verifikasi" class="max-h-56 w-auto mx-auto border border-news-line object-contain">
                        </a>
                    @else
                        <a href="{{ $documentPath }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm border border-news-ink text-news-ink hover:bg-news-ink hover:text-white transition-colors">
                            <i class="fas fa-file-alt"></i>
                            Buka dokumen
                        </a>
                    @endif
                </div>
            @endif

            <div class="flex flex-wrap gap-2">
                @if($user->verification_request_status === 'pending')
                    <form method="POST" action="{{ route('admin.verification-requests.approve', $user) }}" class="inline">
                        @csrf
                        <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-news-ink text-white text-sm font-semibold hover:bg-news-accent transition-colors"
                            onclick="window.pesbarConfirmSubmit(this.form, 'Setujui verifikasi untuk {{ addslashes($user->name) }}? Artikel pending akan otomatis dipublish.')">
                            <i class="fas fa-check"></i>
                            Setujui
                        </button>
                    </form>
                    <button type="button"
                        onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-news-accent text-white text-sm font-semibold hover:bg-news-ink transition-colors">
                        <i class="fas fa-times"></i>
                        Tolak
                    </button>
                @elseif($user->isVerified())
                    <form method="POST" action="{{ route('admin.users.toggle-verified', $user) }}" class="inline">
                        @csrf
                        <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-news-ink text-news-ink text-sm font-semibold hover:bg-news-ink hover:text-white transition-colors"
                            onclick="window.pesbarConfirmSubmit(this.form, 'Cabut verifikasi untuk {{ addslashes($user->name) }}?')">
                            <i class="fas fa-user-slash"></i>
                            Cabut verifikasi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    @if($user->verification_request_status === 'pending')
    <div id="rejectModal" class="fixed inset-0 z-50 hidden bg-news-ink/50" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
        <div class="min-h-full flex items-center justify-center p-4" onclick="if(event.target===this) document.getElementById('rejectModal').classList.add('hidden')">
            <div class="w-full max-w-md bg-white border border-news-line p-5">
                <h3 id="rejectModalTitle" class="font-display text-lg font-bold text-news-ink mb-3">Tolak verifikasi</h3>
                <form method="POST" action="{{ route('admin.verification-requests.reject', $user) }}">
                    @csrf
                    <label for="reason" class="block text-sm font-medium text-news-ink mb-1.5">Alasan penolakan (opsional)</label>
                    <textarea id="reason" name="reason" rows="4"
                        class="w-full px-3 py-2 border border-news-line text-sm focus:outline-none focus:border-news-accent"
                        placeholder="Masukkan alasan penolakan..."></textarea>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button"
                            onclick="document.getElementById('rejectModal').classList.add('hidden')"
                            class="px-4 py-2 text-sm border border-news-line text-news-ink hover:bg-news-paper transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm bg-news-accent text-white font-semibold hover:bg-news-ink transition-colors">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- Profile header --}}
    <header class="border border-news-line mb-6 sm:mb-8">
        <div class="bg-news-paper border-b border-news-line px-4 sm:px-6 py-2.5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-news-muted">Profil Penulis</p>
        </div>

        <div class="px-4 sm:px-6 py-5 sm:py-6">
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-5">
                <div class="shrink-0">
                    @if($user->profile?->avatar)
                        <img
                            src="{{ asset('storage/' . $user->profile->avatar) }}"
                            alt="{{ $user->name }}"
                            class="w-20 h-20 sm:w-24 sm:h-24 object-cover border border-news-line bg-news-line"
                        >
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 border border-news-line bg-news-ink text-white flex items-center justify-center font-display text-3xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="font-display text-2xl sm:text-3xl font-bold text-news-ink leading-tight">
                                    {{ $user->name }}
                                </h1>
                                <x-user-role-badge :user="$user" size="md" />
                            </div>
                            <p class="mt-1 text-sm text-news-muted">{{ '@' . $user->username }}</p>
                        </div>

                        @if($hasSocial)
                        <div class="flex items-center gap-1.5">
                            @if($user->profile?->website)
                                <a href="{{ $user->profile->website }}" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 border border-news-line text-news-muted flex items-center justify-center hover:border-news-ink hover:text-news-ink transition-colors touch-target"
                                   title="Website" aria-label="Website">
                                    <i class="fas fa-globe"></i>
                                </a>
                            @endif
                            @foreach($socialLinks as $platform => $url)
                                @php
                                    $icons = [
                                        'facebook' => 'fab fa-facebook-f',
                                        'twitter' => 'fab fa-twitter',
                                        'instagram' => 'fab fa-instagram',
                                        'linkedin' => 'fab fa-linkedin-in',
                                    ];
                                    $icon = $icons[$platform] ?? 'fas fa-link';
                                @endphp
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 border border-news-line text-news-muted flex items-center justify-center hover:border-news-ink hover:text-news-ink transition-colors touch-target"
                                   title="{{ ucfirst($platform) }}" aria-label="{{ ucfirst($platform) }}">
                                    <i class="{{ $icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    @if($user->profile?->bio)
                        <p class="mt-3 text-news-ink leading-relaxed max-w-3xl">{{ $user->profile->bio }}</p>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-news-muted">
                        @if($user->profile?->location)
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-map-marker-alt text-news-accent text-xs"></i>
                                {{ $user->profile->location }}
                            </span>
                        @endif
                        @if($user->created_at)
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-xs"></i>
                                Bergabung {{ $user->created_at->translatedFormat('F Y') }}
                            </span>
                        @endif
                        @if($user->isVerified())
                            <span class="inline-flex items-center gap-1.5 text-news-ink">
                                <i class="fas fa-check-circle text-blue-500 text-xs"></i>
                                Penulis terverifikasi
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Stats cards --}}
    @if(!empty($stats))
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8" role="group" aria-label="Statistik penulis">
        <div class="border border-news-line bg-white px-4 py-4 flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 bg-news-paper border border-news-line flex items-center justify-center text-news-accent">
                <i class="fas fa-newspaper"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Artikel</p>
                <p class="font-display text-2xl font-bold text-news-ink tabular-nums leading-none mt-0.5">{{ number_format($stats['total_articles']) }}</p>
            </div>
        </div>
        <div class="border border-news-line bg-white px-4 py-4 flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 bg-news-paper border border-news-line flex items-center justify-center text-news-accent">
                <i class="fas fa-eye"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Dilihat</p>
                <p class="font-display text-2xl font-bold text-news-ink tabular-nums leading-none mt-0.5">{{ number_format($stats['total_views']) }}</p>
            </div>
        </div>
        <div class="border border-news-line bg-white px-4 py-4 flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 bg-news-paper border border-news-line flex items-center justify-center text-news-accent">
                <i class="fas fa-comments"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Komentar</p>
                <p class="font-display text-2xl font-bold text-news-ink tabular-nums leading-none mt-0.5">{{ number_format($stats['total_comments']) }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Articles --}}
    <section aria-labelledby="author-articles">
        <div class="flex items-baseline justify-between gap-4 border-b-2 border-news-ink pb-2 mb-5">
            <h2 id="author-articles" class="font-display text-xl sm:text-2xl font-bold text-news-ink tracking-tight">
                Artikel
            </h2>
            <span class="text-xs text-news-muted tabular-nums">{{ $articles->total() }} tulisan</span>
        </div>

        @if($articles->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                @foreach($articles as $article)
                <article class="group border border-news-line hover:border-news-ink transition-colors bg-white">
                    <a href="{{ $article->publicUrl() }}" class="block aspect-[16/10] overflow-hidden bg-news-line">
                        @if($article->featured_image)
                            <img
                                src="{{ Storage::url($article->featured_image) }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full bg-news-paper flex items-center justify-center text-news-muted">
                                <i class="fas fa-newspaper text-xl"></i>
                            </div>
                        @endif
                    </a>
                    <div class="p-3">
                        @if($article->category)
                            <a href="{{ route('categories.show', $article->category) }}"
                               class="text-[10px] font-bold uppercase tracking-widest text-news-accent hover:underline">
                                {{ $article->category->name }}
                            </a>
                        @endif
                        <h3 class="font-display text-sm sm:text-base font-bold leading-snug text-news-ink mt-1 line-clamp-2">
                            <a href="{{ $article->publicUrl() }}" class="hover:text-news-accent transition-colors">
                                {{ $article->title }}
                            </a>
                        </h3>
                        <div class="mt-2 flex items-center justify-between gap-2 text-[11px] text-news-muted">
                            <time datetime="{{ optional($article->published_at ?? $article->created_at)->toIso8601String() }}">
                                {{ ($article->published_at ?? $article->created_at)->format('d M Y') }}
                            </time>
                            <span class="inline-flex items-center gap-1 tabular-nums shrink-0">
                                <i class="fas fa-eye text-[10px]"></i>
                                {{ number_format($article->views) }}
                            </span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            @if($articles->hasPages())
                <div class="mt-8 border-t border-news-line pt-6">
                    {{ $articles->links() }}
                </div>
            @endif
        @else
            <div class="border border-news-line text-center py-14 px-4">
                <p class="font-display text-lg font-bold text-news-ink mb-1">Belum ada artikel</p>
                <p class="text-sm text-news-muted">Penulis ini belum mempublikasikan tulisan.</p>
            </div>
        @endif
    </section>
</div>
@endsection
