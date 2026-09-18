<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Penulis - ' . \App\Helpers\SettingsHelper::siteName())</title>

    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">

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
        @media (min-width: 1024px) {
            .logo-image { width: 36px; height: 36px; }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.25s ease-out; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-news-paper text-news-ink">
    <x-impersonation-banner />
    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="fixed inset-0 bg-black/40 z-40 lg:hidden hidden" id="mobile-overlay"></div>

        <aside class="w-full lg:w-64 bg-white border-r border-news-line fixed lg:fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col" id="sidebar">
            <div class="h-14 sm:h-16 px-3 sm:px-4 border-b-2 border-news-ink flex-shrink-0 flex items-center">
                <div class="flex items-center justify-between gap-2 w-full min-w-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0 hover:opacity-90 transition-opacity">
                        <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}"
                             alt="{{ \App\Helpers\SettingsHelper::siteName() }}"
                             class="logo-image"
                             width="36"
                             height="36">
                        <div class="min-w-0 leading-tight">
                            <span class="font-display text-base font-bold tracking-tight text-news-ink block truncate">
                                {{ \App\Helpers\SettingsHelper::siteName() }}
                            </span>
                            <span class="text-[11px] text-news-muted uppercase tracking-wider font-semibold">Dashboard Penulis</span>
                        </div>
                    </a>
                    <button type="button" class="lg:hidden p-2 text-news-muted hover:text-news-ink hover:bg-news-paper touch-target" onclick="toggleSidebar()" aria-label="Tutup menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto">
                <div class="px-3 py-4 space-y-0.5">
                    <a href="{{ route('penulis.dashboard') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.dashboard') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-4 text-center text-xs"></i>
                        Dashboard
                    </a>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Artikel</h3>

                        <a href="{{ route('penulis.articles.index') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.articles.index') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-newspaper mr-3 w-4 text-center text-xs"></i>
                            Artikel Saya
                        </a>

                        <a href="{{ route('penulis.articles.create') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.articles.create') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-plus-circle mr-3 w-4 text-center text-xs"></i>
                            Buat Artikel Baru
                        </a>
                    </div>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Tools</h3>

                        <a href="{{ route('penulis.analytics') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.analytics') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-chart-line mr-3 w-4 text-center text-xs"></i>
                            Analytics
                        </a>

                        <a href="{{ route('penulis.media.index') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.media.*') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-images mr-3 w-4 text-center text-xs"></i>
                            Media Library
                        </a>

                        <a href="{{ route('penulis.comments.advanced') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.comments.*') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-comments mr-3 w-4 text-center text-xs"></i>
                            Komentar
                        </a>

                        <a href="{{ route('penulis.seo.index') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.seo.*') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-search mr-3 w-4 text-center text-xs"></i>
                            SEO Tools
                        </a>
                    </div>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Profil</h3>

                        <a href="{{ route('penulis.profile') }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.profile') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                            <i class="fas fa-user mr-3 w-4 text-center text-xs"></i>
                            Profil Saya
                        </a>

                        @if(auth()->user()->isPenulis() && !auth()->user()->isVerified())
                            @if(auth()->user()->canRequestVerification())
                                <a href="{{ route('penulis.verification.request') }}"
                                   class="flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target {{ request()->routeIs('penulis.verification.*') ? 'bg-news-paper text-news-accent border-l-2 border-news-accent' : 'text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent' }}">
                                    <i class="fas fa-check-circle mr-3 w-4 text-center text-xs"></i>
                                    Ajukan Verifikasi
                                </a>
                            @elseif(auth()->user()->hasPendingVerificationRequest())
                                <div class="flex items-center px-3 py-2.5 text-sm font-medium text-news-muted bg-news-paper border-l-2 border-news-ink">
                                    <i class="fas fa-clock mr-3 w-4 text-center text-xs"></i>
                                    Verifikasi Pending
                                </div>
                            @endif
                        @elseif(auth()->user()->isVerified())
                            <div class="flex items-center px-3 py-2.5 text-sm font-medium text-news-ink bg-news-paper border-l-2 border-news-ink">
                                <i class="fas fa-check-circle mr-3 w-4 text-center text-xs"></i>
                                Terverifikasi
                            </div>
                        @endif
                    </div>
                </div>
            </nav>

            {{-- A: chip ringkas + menu naik (flow di atas chip, bukan absolute) --}}
            <div class="border-t border-news-line flex-shrink-0 p-2" x-data="{ open: false }" @click.away="open = false">
                <div x-show="open"
                     x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="mb-1.5 bg-white border border-news-line py-1"
                     role="menu">
                    <a href="{{ route('home') }}"
                       role="menuitem"
                       class="flex items-center gap-2.5 px-3 py-2 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent transition-colors">
                        <i class="fas fa-newspaper text-xs text-news-muted w-4 text-center"></i>
                        Ke Portal
                    </a>
                    @if(\App\Services\ImpersonationManager::isImpersonating())
                        <div class="border-t border-news-line my-0.5"></div>
                        <form method="POST" action="{{ route('impersonation.leave') }}">
                            @csrf
                            <button type="submit"
                                    role="menuitem"
                                    class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent transition-colors text-left">
                                <i class="fas fa-undo text-xs text-news-muted w-4 text-center"></i>
                                Kembali ke Admin
                            </button>
                        </form>
                    @else
                        <div class="border-t border-news-line my-0.5"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    role="menuitem"
                                    class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent transition-colors text-left">
                                <i class="fas fa-sign-out-alt text-xs text-news-muted w-4 text-center"></i>
                                Logout
                            </button>
                        </form>
                    @endif
                </div>

                <button type="button"
                        @click="open = !open"
                        class="w-full flex items-center gap-2 px-2 py-1.5 border border-news-line bg-white hover:border-news-ink transition-colors text-left"
                        aria-haspopup="true"
                        :aria-expanded="open.toString()"
                        aria-label="Menu akun">
                    @if(Auth::user()->profile && Auth::user()->profile->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}"
                             alt=""
                             class="w-6 h-6 rounded-full object-cover border border-news-line shrink-0">
                    @else
                        <div class="w-6 h-6 bg-news-ink rounded-full flex items-center justify-center text-white text-[10px] font-semibold shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="flex-1 min-w-0 text-xs font-semibold text-news-ink truncate">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-up text-[9px] text-news-muted shrink-0 transition-transform duration-150"
                       :class="open && 'rotate-180'"></i>
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col lg:ml-64 min-w-0">
            <header class="bg-white border-b-2 border-news-ink sticky top-0 z-30">
                <div class="h-14 sm:h-16 flex items-center justify-between px-3 sm:px-4 lg:px-6 gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button" class="lg:hidden p-2 text-news-muted hover:text-news-ink hover:bg-news-paper touch-target" onclick="toggleSidebar()" aria-label="Buka menu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="min-w-0 leading-tight">
                            <h2 class="font-display text-base font-bold text-news-ink truncate">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-[11px] text-news-muted truncate">@yield('page-subtitle', 'Kelola artikel dan profil Anda')</p>
                        </div>
                    </div>

                    <a href="{{ route('home') }}"
                       class="inline-flex items-center text-sm font-medium text-news-muted hover:text-news-accent px-2 py-1.5 transition-colors shrink-0">
                        <i class="fas fa-newspaper mr-1.5 text-xs"></i>
                        <span class="hidden sm:inline">Ke Portal</span>
                        <span class="sm:hidden">Portal</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 p-3 sm:p-4 lg:p-6 overflow-y-auto overflow-x-hidden min-w-0">
                @auth
                    @if(auth()->user()->isPublishRestricted())
                        <div class="mb-5 border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                            <p class="font-semibold"><i class="fas fa-ban mr-1"></i> Publish langsung dibatasi</p>
                            <p class="mt-1 text-amber-900/90">
                                Artikel baru akan masuk review hingga
                                {{ auth()->user()->publish_restricted_until->format('d M Y, H:i') }} WIB
                                (peringatan: {{ auth()->user()->content_warning_count }}).
                            </p>
                        </div>
                    @elseif((int) auth()->user()->content_warning_count > 0)
                        <div class="mb-5 border border-news-line bg-white px-4 py-3 text-sm text-news-ink">
                            <p class="font-semibold"><i class="fas fa-exclamation-triangle mr-1 text-amber-600"></i> Peringatan konten</p>
                            <p class="mt-1 text-news-muted">
                                Anda memiliki {{ auth()->user()->content_warning_count }} peringatan terkait keakuratan konten.
                                Pastikan fakta artikel sudah diverifikasi sebelum diterbitkan.
                            </p>
                        </div>
                    @endif
                @endauth

                @if(session('success'))
                    <div class="notification-toast mb-5 animate-slide-down">
                        <div class="bg-white border-l-4 border-news-ink border border-news-line p-4 flex items-start gap-3">
                            <i class="fas fa-check-circle text-news-ink mt-0.5"></i>
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
        </div>
    </div>

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

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.getElementById('mobile-overlay').addEventListener('click', function () {
            toggleSidebar();
        });

        window.addEventListener('resize', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        document.querySelectorAll('aside nav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });
    </script>

    <x-confirm-dialog />

    @stack('scripts')
</body>
</html>
