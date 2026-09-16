<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . \App\Helpers\SettingsHelper::siteName())</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">
    
    <!-- Google Search Console Verification -->
    @if(\App\Helpers\SettingsHelper::googleSearchConsole())
    <meta name="google-site-verification" content="{{ \App\Helpers\SettingsHelper::googleSearchConsole() }}" />
    @endif
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .tab-navigation {
            scrollbar-width: thin;
            scrollbar-color: #c4c4c4 #fafafa;
        }
        .tab-navigation::-webkit-scrollbar { height: 6px; }
        .tab-navigation::-webkit-scrollbar-track { background: #fafafa; }
        .tab-navigation::-webkit-scrollbar-thumb { background: #c4c4c4; }
        .tab-navigation::-webkit-scrollbar-thumb:hover { background: #5c5c5c; }
        @media (max-width: 768px) {
            .tab-item { min-height: 48px; padding: 0.875rem 1rem; }
            .tab-navigation { padding: 0.5rem 0; }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        .notification-toast { position: relative; z-index: 50; }
        .notification-toast button { transition: all 0.2s ease; }
        .notification-toast button:hover { transform: scale(1.1); }
    </style>
</head>
<body class="font-sans antialiased bg-news-paper text-news-ink">
    <x-impersonation-banner />
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Mobile Menu Overlay -->
        <div class="fixed inset-0 bg-black/40 z-40 lg:hidden hidden" id="mobile-overlay"></div>

        @php
            $isFullAdmin = Auth::user()->isAdmin();
            $nav = fn (bool $active) => $active
                ? 'flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target bg-news-paper text-news-accent border-l-2 border-news-accent'
                : 'flex items-center px-3 py-2.5 text-sm font-medium transition-colors touch-target text-news-ink hover:bg-news-paper hover:text-news-accent border-l-2 border-transparent';
        @endphp

        <!-- Sidebar -->
        <aside class="w-full lg:w-64 bg-white border-r border-news-line fixed lg:fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col" id="sidebar">
            <div class="h-14 sm:h-16 px-3 sm:px-4 border-b-2 border-news-ink flex-shrink-0 flex items-center">
                <div class="flex items-center justify-between gap-2 w-full min-w-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0 hover:opacity-90 transition-opacity">
                        <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="w-8 h-8 lg:w-9 lg:h-9 max-w-full object-contain">
                        <div class="min-w-0 leading-tight">
                            <span class="font-display text-base font-bold tracking-tight text-news-ink block truncate">{{ \App\Helpers\SettingsHelper::siteName() }}</span>
                            <span class="text-[11px] text-news-muted uppercase tracking-wider font-semibold">{{ $isFullAdmin ? 'Admin Panel' : 'Editor Panel' }}</span>
                        </div>
                    </a>
                    <button type="button" class="lg:hidden p-2 text-news-muted hover:text-news-ink hover:bg-news-paper touch-target" onclick="toggleSidebar()" aria-label="Tutup menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto">
                <div class="px-3 py-4 space-y-0.5">
                    <a href="{{ route('admin.dashboard') }}" class="{{ $nav(request()->routeIs('admin.dashboard')) }}">
                        <i class="fas fa-tachometer-alt mr-3 w-4 text-center text-xs"></i>
                        Dashboard
                    </a>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Konten</h3>

                        @if($isFullAdmin)
                        <a href="{{ route('admin.articles.index') }}" class="{{ $nav(request()->routeIs('admin.articles.index')) }}">
                            <i class="fas fa-newspaper mr-3 w-4 text-center text-xs"></i>
                            Artikel
                        </a>
                        @endif

                        <a href="{{ route('admin.articles.suspended') }}" class="{{ $nav(request()->routeIs('admin.articles.suspended') || (request()->routeIs('admin.articles.index') && request('status') === 'suspended')) }}">
                            <i class="fas fa-pause-circle mr-3 w-4 text-center text-xs"></i>
                            <span class="flex-1">Ditangguhkan</span>
                            @if(isset($suspendedArticlesCount) && $suspendedArticlesCount > 0)
                                <span class="ml-2 bg-orange-50 text-orange-800 text-[10px] font-semibold px-1.5 py-0.5">{{ $suspendedArticlesCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.articles.moderate') }}" class="{{ $nav(request()->routeIs('admin.articles.moderate')) }}">
                            <i class="fas fa-hand-paper mr-3 w-4 text-center text-xs"></i>
                            <span class="flex-1">Tangguhkan Artikel</span>
                        </a>

                        <a href="{{ route('admin.article-reports.index') }}" class="{{ $nav(request()->routeIs('admin.article-reports.*')) }}">
                            <i class="fas fa-flag mr-3 w-4 text-center text-xs"></i>
                            <span class="flex-1">Laporan Artikel</span>
                            @if(isset($openArticleReportsCount) && $openArticleReportsCount > 0)
                                <span class="ml-2 bg-news-accent text-white text-[10px] font-semibold px-1.5 py-0.5">{{ $openArticleReportsCount }}</span>
                            @endif
                        </a>

                        @if($isFullAdmin)

                        <a href="{{ route('admin.categories.index') }}" class="{{ $nav(request()->routeIs('admin.categories.*')) }}">
                            <i class="fas fa-tags mr-3 w-4 text-center text-xs"></i>
                            Kategori
                        </a>

                        <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="{{ $nav(request()->routeIs('admin.comments.*')) }}">
                            <i class="fas fa-comments mr-3 w-4 text-center text-xs"></i>
                            <span class="flex-1">Komentar</span>
                            @if(isset($pendingCommentsCount) && $pendingCommentsCount > 0)
                                <span class="ml-2 bg-red-50 text-news-accent text-[10px] font-semibold px-1.5 py-0.5">{{ $pendingCommentsCount }}</span>
                            @endif
                        </a>
                        @endif
                    </div>

                    @if($isFullAdmin)
                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Pengguna</h3>

                        <a href="{{ route('admin.users') }}" class="{{ $nav(request()->routeIs('admin.users')) }}">
                            <i class="fas fa-users mr-3 w-4 text-center text-xs"></i>
                            Daftar Pengguna
                        </a>

                        <a href="{{ route('admin.penulis.index') }}" class="{{ $nav(request()->routeIs('admin.penulis.*')) }}">
                            <i class="fas fa-user-edit mr-3 w-4 text-center text-xs"></i>
                            Penulis
                        </a>

                        <a href="{{ route('admin.verification-requests') }}" class="{{ $nav(request()->routeIs('admin.verification-requests*')) }}">
                            <i class="fas fa-user-check mr-3 w-4 text-center text-xs"></i>
                            <span class="flex-1">Upgrade Penulis</span>
                            @php
                                $pendingCount = \App\Models\User::where('role', 'user')
                                    ->where('verification_request_status', 'pending')
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="ml-2 bg-news-accent text-white text-[10px] font-semibold px-1.5 py-0.5">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.ban-appeals') }}" class="{{ $nav(request()->routeIs('admin.ban-appeals*')) }}">
                            <i class="fas fa-gavel mr-3 w-4 text-center text-xs"></i>
                            <span class="flex-1">Banding Ban</span>
                            @php
                                $banAppealCount = \App\Models\User::where('ban_appeal_status', 'pending')
                                    ->whereNotNull('banned_until')
                                    ->where('banned_until', '>', now())
                                    ->count();
                            @endphp
                            @if($banAppealCount > 0)
                                <span class="ml-2 bg-news-accent text-white text-[10px] font-semibold px-1.5 py-0.5">{{ $banAppealCount }}</span>
                            @endif
                        </a>
                    </div>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Media & Komunikasi</h3>

                        <a href="{{ route('admin.newsletter.index') }}" class="{{ $nav(request()->routeIs('admin.newsletter.*')) }}">
                            <i class="fas fa-envelope mr-3 w-4 text-center text-xs"></i>
                            Newsletter
                        </a>

                        <a href="{{ route('admin.event-popups.index') }}" class="{{ $nav(request()->routeIs('admin.event-popups.*')) }}">
                            <i class="fas fa-bell mr-3 w-4 text-center text-xs"></i>
                            Event Popup
                        </a>

                        <a href="{{ route('admin.media.index') }}" class="{{ $nav(request()->routeIs('admin.media.*')) }}">
                            <i class="fas fa-images mr-3 w-4 text-center text-xs"></i>
                            Media Library
                        </a>
                    </div>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Widget</h3>

                        <a href="{{ route('admin.contact-importants.index') }}" class="{{ $nav(request()->routeIs('admin.contact-importants.*')) }}">
                            <i class="fas fa-phone-alt mr-3 w-4 text-center text-xs"></i>
                            Kontak Penting
                        </a>

                        <a href="{{ route('admin.events.index') }}" class="{{ $nav(request()->routeIs('admin.events.*')) }}">
                            <i class="fas fa-calendar-alt mr-3 w-4 text-center text-xs"></i>
                            Event
                        </a>

                        <a href="{{ route('admin.polls.index') }}" class="{{ $nav(request()->routeIs('admin.polls.*')) }}">
                            <i class="fas fa-poll mr-3 w-4 text-center text-xs"></i>
                            Polling
                        </a>
                    </div>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Analitik</h3>

                        <a href="{{ route('admin.analytics.index') }}" class="{{ $nav(request()->routeIs('admin.analytics.*')) }}">
                            <i class="fas fa-chart-bar mr-3 w-4 text-center text-xs"></i>
                            Analitik
                        </a>

                        <a href="{{ route('admin.reports.index') }}" class="{{ $nav(request()->routeIs('admin.reports.*')) }}">
                            <i class="fas fa-file-alt mr-3 w-4 text-center text-xs"></i>
                            Laporan
                        </a>
                    </div>

                    <div class="mt-5">
                        <h3 class="px-3 text-[11px] font-bold text-news-muted uppercase tracking-wider mb-1.5">Sistem</h3>

                        <a href="{{ route('admin.settings.index') }}" class="{{ $nav(request()->routeIs('admin.settings.*')) }}">
                            <i class="fas fa-cog mr-3 w-4 text-center text-xs"></i>
                            Pengaturan
                        </a>

                        <a href="{{ route('admin.backup.index') }}" class="{{ $nav(request()->routeIs('admin.backup.*')) }}">
                            <i class="fas fa-database mr-3 w-4 text-center text-xs"></i>
                            Backup
                        </a>

                        <a href="{{ route('admin.logs.index') }}" class="{{ $nav(request()->routeIs('admin.logs.*')) }}">
                            <i class="fas fa-list-alt mr-3 w-4 text-center text-xs"></i>
                            Log Sistem
                        </a>
                    </div>
                    @endif
                </div>
            </nav>
            
            <div class="p-3 border-t border-news-line flex-shrink-0">
                <div class="flex items-center gap-2.5 px-1">
                    <div class="w-8 h-8 bg-news-ink flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-news-ink truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-news-muted uppercase tracking-wider">{{ $isFullAdmin ? 'Administrator' : 'Editor' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-3 py-2 text-sm text-news-ink hover:bg-news-paper hover:text-news-accent border border-news-line hover:border-news-ink transition-colors touch-target">
                        <i class="fas fa-sign-out-alt mr-2 text-xs"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64 min-w-0 dashboard-main">
            <!-- Top Bar -->
            <header class="bg-white border-b-2 border-news-ink sticky top-0 z-30 h-14 sm:h-16 flex items-center px-3 sm:px-4 lg:px-6 gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <button type="button" class="lg:hidden p-2 text-news-muted hover:text-news-ink hover:bg-news-paper touch-target" onclick="toggleSidebar()" aria-label="Buka menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="min-w-0 leading-tight">
                        <h2 class="font-display text-base font-bold text-news-ink truncate">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-[11px] text-news-muted truncate">@yield('page-subtitle', 'Selamat datang di admin panel')</p>
                    </div>
                </div>

                <a href="{{ route('home') }}"
                   class="inline-flex items-center text-sm font-medium text-news-muted hover:text-news-accent px-2 py-1.5 transition-colors shrink-0">
                    <i class="fas fa-external-link-alt mr-1.5 text-xs"></i>
                    <span class="hidden sm:inline">Lihat Website</span>
                    <span class="sm:hidden">Portal</span>
                </a>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-3 sm:p-4 lg:p-6 safe-bottom overflow-y-auto overflow-x-hidden min-w-0">
                @if(session('success'))
                    <div class="notification-toast notification-success mb-6 animate-slide-down">
                        <div class="bg-white border border-news-line border-l-4 border-l-news-ink p-4">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-news-ink mt-0.5"></i>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-semibold text-news-ink">Berhasil</p>
                                    <p class="text-sm text-news-muted mt-0.5">{{ session('success') }}</p>
                                </div>
                                <button type="button" onclick="this.closest('.notification-toast').remove()" class="ml-3 text-news-muted hover:text-news-ink">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="notification-toast notification-warning mb-6 animate-slide-down">
                        <div class="bg-white border border-news-line border-l-4 border-l-amber-500 p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-semibold text-news-ink">Perhatian</p>
                                    <p class="text-sm text-news-muted mt-0.5">{{ session('warning') }}</p>
                                </div>
                                <button type="button" onclick="this.closest('.notification-toast').remove()" class="ml-3 text-news-muted hover:text-news-ink">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="notification-toast notification-error mb-6 animate-slide-down">
                        <div class="bg-white border border-news-line border-l-4 border-l-news-accent p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-news-accent mt-0.5"></i>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-semibold text-news-ink">Error</p>
                                    <p class="text-sm text-news-muted mt-0.5">{{ session('error') }}</p>
                                </div>
                                <button type="button" onclick="this.closest('.notification-toast').remove()" class="ml-3 text-news-muted hover:text-news-ink">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="notification-toast notification-error mb-6 animate-slide-down">
                        <div class="bg-white border border-news-line border-l-4 border-l-news-accent p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-news-accent mt-0.5"></i>
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-news-ink">Validasi gagal</p>
                                    <ul class="mt-1 text-sm text-news-muted list-disc list-inside space-y-0.5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" onclick="this.closest('.notification-toast').remove()" class="ml-3 text-news-muted hover:text-news-ink">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Auto-hide notification toasts after 5 seconds with smooth animation
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-toast');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    notification.style.transition = 'all 0.5s ease-out';
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-20px)';
                    setTimeout(function() {
                        notification.remove();
                    }, 500);
                }, 5000);
            });
        });
        
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scrolling
            }
        }
        
        // Close sidebar when clicking overlay
        document.getElementById('mobile-overlay').addEventListener('click', function() {
            toggleSidebar();
        });
        
        // Handle window resize — open on desktop, force-close on smaller screens
        window.addEventListener('resize', function() {
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
        
        // Close sidebar when clicking on navigation links on mobile
        document.querySelectorAll('aside nav a').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });
    </script>
    
    <!-- Google Analytics -->
    @if(\App\Helpers\SettingsHelper::googleAnalytics())
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Helpers\SettingsHelper::googleAnalytics() }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ \App\Helpers\SettingsHelper::googleAnalytics() }}');
    </script>
    @endif

    <!-- Facebook Pixel -->
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
