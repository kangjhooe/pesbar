<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', \App\Helpers\SeoHelper::generateTitle())</title>
    <meta name="description" content="@yield('description', \App\Helpers\SeoHelper::generateDescription())">
    <meta name="keywords" content="@yield('keywords', \App\Helpers\SeoHelper::generateKeywords())">
    <meta name="robots" content="@yield('robots', \App\Helpers\SeoHelper::generateRobotsMeta())">
    <link rel="canonical" href="@yield('canonical', \App\Helpers\SeoHelper::generateCanonicalUrl())">

    @php
        $ogData = \App\Helpers\SeoHelper::generateOpenGraph([
            'og:title' => $__env->yieldContent('og:title', \App\Helpers\SeoHelper::generateTitle()),
            'og:description' => $__env->yieldContent('og:description', \App\Helpers\SeoHelper::generateDescription()),
            'og:image' => $__env->yieldContent('og:image', \App\Helpers\SettingsHelper::siteLogo()),
            'og:url' => $__env->yieldContent('og:url', request()->url()),
        ]);
    @endphp
    @foreach($ogData as $property => $content)
        <meta property="{{ $property }}" content="{{ $content }}">
    @endforeach

    @php
        $twitterData = \App\Helpers\SeoHelper::generateTwitterCard([
            'twitter:title' => $__env->yieldContent('twitter:title', \App\Helpers\SeoHelper::generateTitle()),
            'twitter:description' => $__env->yieldContent('twitter:description', \App\Helpers\SeoHelper::generateDescription()),
            'twitter:image' => $__env->yieldContent('twitter:image', \App\Helpers\SettingsHelper::siteLogo()),
        ]);
    @endphp
    @foreach($twitterData as $name => $content)
        <meta name="{{ $name }}" content="{{ $content }}">
    @endforeach

    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">

    @if(\App\Helpers\SettingsHelper::googleSearchConsole())
    <meta name="google-site-verification" content="{{ \App\Helpers\SettingsHelper::googleSearchConsole() }}" />
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --news-ink: #0a0a0a;
            --news-muted: #5c5c5c;
            --news-line: #e5e5e5;
            --news-paper: #fafafa;
            --news-accent: #b91c1c;
        }
        .font-display { font-family: "Source Serif 4", Georgia, serif; }
        .text-news-ink { color: var(--news-ink); }
        .text-news-muted { color: var(--news-muted); }
        .text-news-accent { color: var(--news-accent); }
        .bg-news-ink { background-color: var(--news-ink); }
        .bg-news-accent { background-color: var(--news-accent); }
        .bg-news-paper { background-color: var(--news-paper); }
        .border-news-ink { border-color: var(--news-ink); }
        .border-news-line { border-color: var(--news-line); }
        .border-news-accent { border-color: var(--news-accent); }
        .hover\:text-news-accent:hover { color: var(--news-accent); }
        .hover\:bg-news-accent:hover { background-color: var(--news-accent); }
        .hover\:bg-news-paper:hover { background-color: var(--news-paper); }
        .portal-nav-link.is-active,
        .portal-nav-link:hover {
            color: var(--news-accent);
            box-shadow: inset 0 -2px 0 var(--news-accent);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-news-ink overflow-x-clip" :class="{ 'overflow-hidden': mobileMenuOpen }" x-data="{ mobileMenuOpen: false, searchOpen: false }">
    <x-impersonation-banner />
    <nav class="bg-white border-b-2 border-news-ink sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="relative flex justify-between items-center h-14 sm:h-16 gap-2 min-w-0">
                {{-- Logo kiri (semua ukuran) + nama hanya dari sm ke atas --}}
                <div class="flex items-center flex-shrink-0 min-w-0 z-10">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 min-w-0" title="{{ \App\Helpers\SettingsHelper::siteName() }}">
                        <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}"
                             alt="{{ \App\Helpers\SettingsHelper::siteName() }}"
                             class="w-8 h-8 sm:w-9 sm:h-9 object-contain object-center flex-shrink-0"
                             width="36"
                             height="36">
                        <span class="font-display text-base sm:text-lg md:text-xl font-bold tracking-tight text-news-ink hidden sm:block truncate max-w-[10rem] lg:max-w-[14rem] xl:max-w-none">{{ \App\Helpers\SettingsHelper::siteName() }}</span>
                    </a>
                </div>

                {{-- Mobile: nama platform di tengah layar (tanpa logo) --}}
                <span class="sm:hidden absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 font-display text-base font-bold tracking-tight text-news-ink truncate max-w-[calc(100%-7rem)] text-center pointer-events-none" aria-hidden="true">
                    {{ \App\Helpers\SettingsHelper::siteName() }}
                </span>

                @php
                    $navMore = $navMoreCategories ?? collect();
                    $activeCategoryId = request()->routeIs('categories.show')
                        ? optional(request()->route('category'))->id
                        : null;
                    $isMoreActive = $activeCategoryId && $navMore->contains('id', $activeCategoryId);
                @endphp
                {{-- Lainnya ikut scroll horizontal; dropdown fixed agar tidak terpotong overflow strip --}}
                <div class="hidden lg:flex items-center space-x-1 xl:space-x-2 flex-1 mx-2 xl:mx-4 overflow-x-auto overflow-y-hidden nav-scroll portal-nav-strip min-w-0">
                    @foreach($navCategories ?? [] as $navCategory)
                        <a href="{{ route('categories.show', $navCategory) }}"
                           class="portal-nav-link text-news-ink px-1 py-2 text-xs xl:text-sm font-bold uppercase tracking-wide whitespace-nowrap {{ $activeCategoryId === $navCategory->id ? 'is-active' : '' }}">
                            {{ $navCategory->name }}
                        </a>
                    @endforeach
                    @if($navMore->isNotEmpty())
                        <div class="relative flex-shrink-0"
                             x-data="{
                                open: false,
                                menuStyle: '',
                                toggle() {
                                    this.open = !this.open;
                                    if (this.open) this.$nextTick(() => this.place());
                                },
                                place() {
                                    const btn = this.$refs.btn;
                                    const menu = this.$refs.menu;
                                    if (!btn || !menu) return;
                                    const r = btn.getBoundingClientRect();
                                    const menuW = Math.max(menu.offsetWidth, 192);
                                    let left = r.left;
                                    left = Math.min(left, window.innerWidth - menuW - 8);
                                    left = Math.max(8, left);
                                    this.menuStyle = 'top:' + (r.bottom + 4) + 'px;left:' + left + 'px';
                                },
                                close() { this.open = false; },
                                init() {
                                    const strip = this.$el.closest('.portal-nav-strip');
                                    if (strip) {
                                        strip.addEventListener('scroll', () => { if (this.open) this.close(); }, { passive: true });
                                    }
                                }
                             }"
                             @click.away="close()"
                             @keydown.escape.window="close()"
                             @scroll.window="if (open) close()"
                             @resize.window="if (open) place()">
                            <button type="button"
                                    x-ref="btn"
                                    @click="toggle()"
                                    class="portal-nav-link text-news-ink px-1 py-2 text-xs xl:text-sm font-bold uppercase tracking-wide whitespace-nowrap inline-flex items-center gap-1 {{ $isMoreActive ? 'is-active' : '' }}"
                                    :aria-expanded="open.toString()">
                                Lainnya
                                <i class="fas fa-chevron-down text-[10px]" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open"
                                 x-ref="menu"
                                 x-cloak
                                 x-transition
                                 :style="menuStyle"
                                 class="fixed min-w-[12rem] max-h-72 overflow-y-auto bg-white border border-news-line shadow-lg py-1 z-[60]">
                                @foreach($navMore as $moreCategory)
                                    <a href="{{ route('categories.show', $moreCategory) }}"
                                       class="block px-4 py-2 text-xs xl:text-sm font-bold uppercase tracking-wide whitespace-nowrap {{ $activeCategoryId === $moreCategory->id ? 'text-news-accent bg-news-paper' : 'text-news-ink hover:bg-news-paper hover:text-news-accent' }}">
                                        {{ $moreCategory->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-1 sm:space-x-2 flex-shrink-0 z-10">
                    {{-- Compact search from md (covers tablet + laptop); full on xl --}}
                    <div class="hidden md:block">
                        <form action="{{ route('search.index') }}" method="GET" class="relative">
                            <input type="text" name="q" placeholder="Cari..." value="{{ request('q') }}"
                                   class="w-28 lg:w-36 xl:w-48 pl-3 pr-8 py-1.5 border border-news-line text-sm focus:outline-none focus:ring-1 focus:ring-news-ink focus:border-news-ink"
                                   aria-label="Cari berita">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-news-muted hover:text-news-ink touch-target" aria-label="Submit pencarian">
                                <i class="fas fa-search text-sm"></i>
                            </button>
                        </form>
                    </div>
                    <button type="button" @click="searchOpen = !searchOpen; mobileMenuOpen = false" class="md:hidden text-news-ink p-1.5 inline-flex items-center justify-center" aria-label="Buka pencarian">
                        <i class="fas fa-search text-sm"></i>
                    </button>

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-1 sm:space-x-2 text-news-ink hover:text-news-accent px-1 sm:px-2 py-2 text-sm font-medium touch-target">
                                @if(Auth::user()->profile && Auth::user()->profile->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-news-line">
                                @else
                                    <div class="w-8 h-8 bg-news-ink rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                @endif
                                <span class="hidden xl:block font-semibold max-w-[8rem] truncate">{{ Auth::user()->name }}</span>
                                <span class="hidden lg:inline-flex"><x-user-role-badge :user="Auth::user()" size="xs" /></span>
                                <i class="fas fa-chevron-down text-xs hidden sm:inline"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-52 bg-white shadow-lg py-1 z-50 border border-news-line">
                                @if(auth()->user()->role === 'user')
                                    <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-news-ink hover:bg-news-paper">Akun saya</a>
                                    <a href="{{ route('user.bookmarks') }}" class="block px-4 py-2 text-sm text-news-ink hover:bg-news-paper">Bookmark</a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-news-ink hover:bg-news-paper">Profil</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-news-ink hover:bg-news-paper">Dashboard</a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-news-ink hover:bg-news-paper">Profil</a>
                                @endif
                                <hr class="my-1 border-news-line">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-news-ink hover:bg-news-paper">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                            <a href="{{ route('login') }}" class="text-news-ink hover:text-news-accent px-2 py-2 text-sm font-bold">Login</a>
                            @if(\App\Helpers\SettingsHelper::enableRegistration())
                            <a href="{{ route('register') }}" class="bg-news-ink text-white hover:bg-news-accent px-2.5 lg:px-3 py-1.5 text-sm font-bold">Daftar</a>
                            @endif
                        </div>
                    @endauth

                    <div class="lg:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen; searchOpen = false" class="text-news-ink p-2 touch-target" aria-label="Menu" :aria-expanded="mobileMenuOpen.toString()">
                            <i class="fas fa-bars text-lg" x-show="!mobileMenuOpen"></i>
                            <i class="fas fa-times text-lg" x-show="mobileMenuOpen" x-cloak></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile search bar --}}
        <div x-show="searchOpen" x-transition class="md:hidden bg-white border-t border-news-line px-3 py-3">
            <form action="{{ route('search.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="q" placeholder="Cari berita..." value="{{ request('q') }}"
                       class="flex-1 min-w-0 px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-1 focus:ring-news-ink"
                       autofocus>
                <button type="submit" class="px-4 py-2.5 bg-news-ink text-white touch-target shrink-0"><i class="fas fa-search"></i></button>
            </form>
        </div>

        {{-- Mobile / tablet menu (< lg) --}}
        <div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-white border-t border-news-line max-h-[calc(100vh-3.5rem)] overflow-y-auto">
            <div class="px-2 pt-2 pb-4 space-y-1 safe-bottom">
                @foreach($navCategories ?? [] as $navCategory)
                    <a href="{{ route('categories.show', $navCategory) }}" @click="mobileMenuOpen = false"
                       class="block px-3 py-3 text-sm font-bold uppercase touch-target {{ optional(request()->route('category'))->id === $navCategory->id ? 'text-news-accent bg-news-paper' : 'text-news-ink' }}">
                        {{ $navCategory->name }}
                    </a>
                @endforeach
                @if(($navMoreCategories ?? collect())->isNotEmpty())
                    <p class="px-3 pt-3 pb-1 text-xs font-bold uppercase tracking-wide text-news-muted">Lainnya</p>
                    @foreach($navMoreCategories as $moreCategory)
                        <a href="{{ route('categories.show', $moreCategory) }}" @click="mobileMenuOpen = false"
                           class="block px-3 py-3 text-sm font-bold uppercase touch-target {{ optional(request()->route('category'))->id === $moreCategory->id ? 'text-news-accent bg-news-paper' : 'text-news-ink' }}">
                            {{ $moreCategory->name }}
                        </a>
                    @endforeach
                @endif
                @auth
                    <hr class="my-2 border-news-line">
                    @if(auth()->user()->role === 'user')
                        <a href="{{ route('user.dashboard') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Akun saya</a>
                        <a href="{{ route('user.bookmarks') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Bookmark</a>
                        <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Profil</a>
                    @else
                        <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Dashboard</a>
                        <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Profil</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" @click="mobileMenuOpen = false" class="block w-full text-left px-3 py-3 text-sm font-semibold text-news-ink touch-target">Logout</button>
                    </form>
                @else
                    <hr class="my-2 border-news-line">
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Login</a>
                    @if(\App\Helpers\SettingsHelper::enableRegistration())
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 text-sm font-semibold text-news-ink touch-target">Daftar</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <main class="min-w-0 w-full">
        @yield('content')
    </main>

    <footer class="bg-news-ink text-white mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="text-center">
                <div class="flex justify-center items-center space-x-3 mb-4">
                    <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="w-10 h-10 object-contain object-center brightness-0 invert" width="40" height="40">
                    <h3 class="font-display text-lg font-bold">{{ \App\Helpers\SettingsHelper::siteName() }}</h3>
                </div>
                <div class="flex justify-center flex-wrap gap-x-5 gap-y-2 mb-6">
                    <a href="{{ route('home') }}" class="text-white/70 hover:text-white text-sm">Beranda</a>
                    <a href="{{ route('articles.index') }}" class="text-white/70 hover:text-white text-sm">Semua Berita</a>
                    @foreach(($navCategories ?? collect())->take(5) as $navCategory)
                        <a href="{{ route('categories.show', $navCategory) }}" class="text-white/70 hover:text-white text-sm">{{ $navCategory->name }}</a>
                    @endforeach
                </div>
                <div class="flex justify-center space-x-4 mb-6">
                    <a href="{{ route('terms') }}" class="text-white/50 hover:text-white text-xs">Syarat dan Ketentuan</a>
                    <span class="text-white/30">|</span>
                    <a href="{{ route('privacy') }}" class="text-white/50 hover:text-white text-xs">Kebijakan Privasi</a>
                </div>
                <div class="flex justify-center space-x-4 mb-4">
                    @if(\App\Helpers\SettingsHelper::facebookUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::facebookUrl() }}" target="_blank" rel="noopener noreferrer" class="text-white/70 hover:text-white" aria-label="Facebook"><i class="fab fa-facebook text-lg"></i></a>
                    @endif
                    @if(\App\Helpers\SettingsHelper::instagramUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::instagramUrl() }}" target="_blank" rel="noopener noreferrer" class="text-white/70 hover:text-white" aria-label="Instagram"><i class="fab fa-instagram text-lg"></i></a>
                    @endif
                    @if(\App\Helpers\SettingsHelper::youtubeUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::youtubeUrl() }}" target="_blank" rel="noopener noreferrer" class="text-white/70 hover:text-white" aria-label="YouTube"><i class="fab fa-youtube text-lg"></i></a>
                    @endif
                    @if(\App\Helpers\SettingsHelper::twitterUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::twitterUrl() }}" target="_blank" rel="noopener noreferrer" class="text-white/70 hover:text-white" aria-label="Twitter"><i class="fab fa-x-twitter text-lg"></i></a>
                    @endif
                </div>
                <p class="text-white/50 text-sm">&copy; {{ date('Y') }} {{ \App\Helpers\SettingsHelper::siteName() }}. Hak cipta dilindungi undang-undang.</p>
            </div>
        </div>
    </footer>

    @if(!empty($eventPopup))
    <div id="eventPopup" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="eventPopupTitle">
        <div id="popupContent" class="w-full max-w-lg max-h-[90vh] overflow-y-auto border border-news-line bg-white shadow-xl opacity-0 scale-95 transition-all duration-300">
            <div class="bg-news-ink px-4 sm:px-6 py-4 text-white">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-white/60 mb-1">Pemberitahuan</p>
                        <h2 id="eventPopupTitle" class="font-display text-lg font-bold leading-snug">{{ $eventPopup->title }}</h2>
                    </div>
                    <button type="button" id="closePopup" class="shrink-0 p-1 text-white/70 hover:text-white transition-colors" aria-label="Tutup">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-5">
                <p class="text-sm sm:text-base text-news-ink/80 leading-relaxed whitespace-pre-line">{{ $eventPopup->message }}</p>
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <button type="button" id="closePopupBtn" class="flex-1 bg-news-accent text-white px-4 py-2.5 text-sm font-semibold hover:bg-red-800 transition-colors">
                        Mengerti
                    </button>
                    <button type="button" id="closePopupBtn2" class="px-4 py-2.5 border border-news-line text-sm font-medium text-news-ink hover:bg-news-paper transition-colors">
                        Tutup
                    </button>
                </div>
            </div>

            <div class="border-t border-news-line bg-news-paper px-4 sm:px-6 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-news-muted">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                        {{ $eventPopup->start_date->format('d M Y') }} – {{ $eventPopup->end_date->format('d M Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        Berlaku {{ $eventPopup->start_date->diffInDays($eventPopup->end_date) + 1 }} hari
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('eventPopup');
        const popupContent = document.getElementById('popupContent');
        if (!popup || !popupContent) return;

        const storageKey = 'eventPopup_{{ $eventPopup->id }}';
        const closeButtons = [
            document.getElementById('closePopup'),
            document.getElementById('closePopupBtn'),
            document.getElementById('closePopupBtn2'),
        ];

        function showPopup() {
            popup.classList.remove('hidden');
            popup.classList.add('flex');
            requestAnimationFrame(function () {
                popupContent.classList.remove('opacity-0', 'scale-95');
                popupContent.classList.add('opacity-100', 'scale-100');
            });
        }

        function hidePopup() {
            popupContent.classList.remove('opacity-100', 'scale-100');
            popupContent.classList.add('opacity-0', 'scale-95');
            setTimeout(function () {
                popup.classList.add('hidden');
                popup.classList.remove('flex');
                try { localStorage.setItem(storageKey, 'dismissed'); } catch (e) {}
            }, 280);
        }

        try {
            if (!localStorage.getItem(storageKey)) {
                setTimeout(showPopup, 1200);
            }
        } catch (e) {
            setTimeout(showPopup, 1200);
        }

        closeButtons.forEach(function (btn) {
            if (btn) btn.addEventListener('click', hidePopup);
        });

        popup.addEventListener('click', function (e) {
            if (e.target === popup) hidePopup();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !popup.classList.contains('hidden')) {
                hidePopup();
            }
        });
    });
    </script>
    @endif

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @yield('structured-data')

    <x-confirm-dialog />

    <script>
    (function () {
        function csrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function setFollowingState(btn, following) {
            var icon = btn.querySelector('i');
            var text = btn.querySelector('.follow-text');
            var compact = btn.getAttribute('data-compact') === '1';

            btn.setAttribute('data-following', following ? '1' : '0');

            if (following) {
                btn.classList.remove('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-accent', 'hover:text-news-accent');
                btn.classList.add('bg-news-ink', 'text-white', 'border-news-ink');
                if (icon) {
                    icon.classList.remove('fa-user-plus');
                    icon.classList.add('fa-user-check');
                }
                if (text) text.textContent = 'Mengikuti';
            } else {
                btn.classList.remove('bg-news-ink', 'text-white', 'border-news-ink');
                btn.classList.add('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-accent', 'hover:text-news-accent');
                if (icon) {
                    icon.classList.remove('fa-user-check');
                    icon.classList.add('fa-user-plus');
                }
                if (text) text.textContent = compact ? 'Ikuti' : 'Ikuti Penulis';
            }
        }

        function setBookmarkedState(btn, bookmarked) {
            var text = btn.querySelector('.bookmark-text');
            btn.setAttribute('data-bookmarked', bookmarked ? '1' : '0');

            if (bookmarked) {
                btn.classList.remove('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-ink');
                btn.classList.add('bg-yellow-50', 'text-yellow-800', 'border-yellow-300');
                if (text) text.textContent = 'Bookmarked';
            } else {
                btn.classList.remove('bg-yellow-50', 'text-yellow-800', 'border-yellow-300');
                btn.classList.add('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-ink');
                if (text) text.textContent = 'Bookmark';
            }
        }

        document.addEventListener('click', function (event) {
            var followBtn = event.target.closest('[data-follow-url]');
            if (followBtn) {
                event.preventDefault();
                event.stopPropagation();

                var followUrl = followBtn.getAttribute('data-follow-url');
                var followLoginUrl = followBtn.getAttribute('data-login-url') || '{{ route('login') }}';

                @guest
                window.location.href = followLoginUrl;
                return;
                @endguest

                if (followBtn.disabled) return;
                followBtn.disabled = true;

                fetch(followUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(function (response) {
                    if (response.status === 401 || response.status === 419) {
                        window.location.href = followLoginUrl;
                        return null;
                    }
                    if (!response.ok) {
                        throw new Error('Follow request failed: ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (!data || !data.success) return;
                    setFollowingState(followBtn, !!data.following);
                })
                .catch(function (error) {
                    console.error('Follow error:', error);
                })
                .finally(function () {
                    followBtn.disabled = false;
                });
                return;
            }

            var bookmarkBtn = event.target.closest('[data-bookmark-url]');
            if (!bookmarkBtn) return;

            event.preventDefault();
            event.stopPropagation();

            var bookmarkUrl = bookmarkBtn.getAttribute('data-bookmark-url');
            var bookmarkLoginUrl = bookmarkBtn.getAttribute('data-login-url') || '{{ route('login') }}';

            @guest
            window.location.href = bookmarkLoginUrl;
            return;
            @endguest

            if (bookmarkBtn.disabled) return;
            bookmarkBtn.disabled = true;

            fetch(bookmarkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(function (response) {
                if (response.status === 401 || response.status === 419) {
                    window.location.href = bookmarkLoginUrl;
                    return null;
                }
                if (!response.ok) {
                    throw new Error('Bookmark request failed: ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                if (!data || !data.success) return;
                setBookmarkedState(bookmarkBtn, !!data.bookmarked);
            })
            .catch(function (error) {
                console.error('Bookmark error:', error);
            })
            .finally(function () {
                bookmarkBtn.disabled = false;
            });
        });
    })();
    </script>

    @stack('scripts')

    @if(\App\Helpers\SettingsHelper::googleAnalytics())
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Helpers\SettingsHelper::googleAnalytics() }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ \App\Helpers\SettingsHelper::googleAnalytics() }}');
    </script>
    @endif

    @if(\App\Helpers\SettingsHelper::facebookPixel())
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ \App\Helpers\SettingsHelper::facebookPixel() }}');
        fbq('track', 'PageView');
    </script>
    @endif
</body>
</html>
