<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Akun - ' . \App\Helpers\SettingsHelper::siteName())</title>

    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">

    @if(\App\Helpers\SettingsHelper::googleSearchConsole())
    <meta name="google-site-verification" content="{{ \App\Helpers\SettingsHelper::googleSearchConsole() }}" />
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .logo-image {
            width: 32px; height: 32px;
            object-fit: contain; object-position: center;
            flex-shrink: 0; display: block;
        }
        @media (min-width: 640px) {
            .logo-image { width: 36px; height: 36px; }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.25s ease-out; }
    </style>
</head>
<body class="font-sans antialiased bg-news-paper text-news-ink">
    <x-impersonation-banner />
    <nav class="bg-white border-b-2 border-news-ink sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16 gap-2 min-w-0">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 sm:gap-3 min-w-0 hover:opacity-90 transition-opacity">
                    <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}"
                         alt="{{ \App\Helpers\SettingsHelper::siteName() }}"
                         class="logo-image"
                         width="36"
                         height="36">
                    <div class="min-w-0 hidden sm:block">
                        <span class="font-display text-base sm:text-lg font-bold tracking-tight text-news-ink block truncate">
                            {{ \App\Helpers\SettingsHelper::siteName() }}
                        </span>
                        <span class="text-[11px] text-news-muted uppercase tracking-wider font-semibold">Akun saya</span>
                    </div>
                </a>

                <div class="flex items-center gap-1 sm:gap-3">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center text-sm font-medium text-news-muted hover:text-news-accent px-2 py-2 transition-colors">
                        <i class="fas fa-newspaper mr-1.5 text-xs"></i>
                        <span class="hidden sm:inline">Ke Portal</span>
                        <span class="sm:hidden">Portal</span>
                    </a>

                    <div class="relative" x-data="{ open: false }">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 text-news-ink hover:text-news-accent px-1.5 sm:px-2 py-2 text-sm font-medium touch-target"
                                aria-haspopup="true"
                                :aria-expanded="open.toString()">
                            @if(Auth::user()->profile && Auth::user()->profile->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}"
                                     alt="{{ Auth::user()->name }}"
                                     class="w-8 h-8 rounded-full object-cover border border-news-line">
                            @else
                                <div class="w-8 h-8 bg-news-ink rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="hidden sm:inline truncate max-w-[8rem]">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-[10px] text-news-muted"></i>
                        </button>

                        {{-- Dropdown: hanya aksi akun (navigasi fitur ada di sub-nav) --}}
                        <div x-show="open"
                             x-cloak
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 bg-white border border-news-line shadow-lg py-1 z-50">
                            <p class="px-4 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-news-muted">Akun</p>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent">
                                Edit Profil
                            </a>
                            @if(auth()->user()->role === 'user' && auth()->user()->canRequestUpgrade())
                                <a href="{{ route('user.upgrade-request') }}" class="block px-4 py-2.5 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent">
                                    Jadi Penulis
                                </a>
                            @endif
                            <div class="border-t border-news-line my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Sub-nav: navigasi utama area akun --}}
    <div class="bg-white border-b border-news-line">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <nav class="flex gap-0.5 sm:gap-1 overflow-x-auto nav-scroll py-1.5 -mx-1 px-1" aria-label="Menu akun">
                @php
                    $userNav = [
                        ['route' => 'user.dashboard', 'label' => 'Ringkasan', 'match' => 'user.dashboard'],
                        ['route' => 'user.bookmarks', 'label' => 'Bookmark', 'match' => 'user.bookmarks'],
                        ['route' => 'user.reading-history', 'label' => 'Riwayat Baca', 'match' => 'user.reading-history'],
                        ['route' => 'user.following', 'label' => 'Mengikuti', 'match' => 'user.following'],
                        ['route' => 'profile.edit', 'label' => 'Profil', 'match' => 'profile.edit'],
                    ];
                @endphp
                @foreach($userNav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="px-3 py-2.5 text-xs sm:text-sm font-semibold whitespace-nowrap transition-colors {{ request()->routeIs($item['match']) ? 'text-news-accent border-b-2 border-news-accent' : 'text-news-muted hover:text-news-ink border-b-2 border-transparent' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8 min-w-0">
        @if(session('success'))
            <div class="notification-toast mb-5 animate-slide-down">
                <div class="bg-white border-l-4 border-emerald-600 border border-news-line p-4 flex items-start gap-3">
                    <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-news-ink">Berhasil</p>
                        <p class="text-sm text-news-muted mt-0.5">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.closest('.notification-toast').remove()" class="text-news-muted hover:text-news-ink" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="notification-toast mb-5 animate-slide-down">
                <div class="bg-white border-l-4 border-amber-500 border border-news-line p-4 flex items-start gap-3">
                    <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-news-ink">Info</p>
                        <p class="text-sm text-news-muted mt-0.5">{{ session('info') }}</p>
                    </div>
                    <button type="button" onclick="this.closest('.notification-toast').remove()" class="text-news-muted hover:text-news-ink" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="notification-toast mb-5 animate-slide-down">
                <div class="bg-white border-l-4 border-news-accent border border-news-line p-4 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-news-accent mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-news-ink">Error</p>
                        <p class="text-sm text-news-muted mt-0.5">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.closest('.notification-toast').remove()" class="text-news-muted hover:text-news-ink" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-news-line mt-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <p class="text-center text-sm text-news-muted">
                &copy; {{ date('Y') }} {{ \App\Helpers\SettingsHelper::siteName() }}. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.notification-toast').forEach(function (el) {
                setTimeout(function () {
                    el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(function () { el.remove(); }, 400);
                }, 5000);
            });
        });
    </script>

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
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ \App\Helpers\SettingsHelper::facebookPixel() }}&ev=PageView&noscript=1"
    /></noscript>
    @endif

    <x-confirm-dialog />

    @stack('scripts')
</body>
</html>
