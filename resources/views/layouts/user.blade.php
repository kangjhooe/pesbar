<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard User - ' . \App\Helpers\SettingsHelper::siteName())</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">
    
    <!-- Google Search Console Verification -->
    @if(\App\Helpers\SettingsHelper::googleSearchConsole())
    <meta name="google-site-verification" content="{{ \App\Helpers\SettingsHelper::googleSearchConsole() }}" />
    @endif
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback Tailwind CSS CDN for development -->
    @if(app()->environment('local'))
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    @endif
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Logo Consistency Styles -->
    <style>
        .logo-image {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
            max-width: 32px !important;
            max-height: 32px !important;
            object-fit: contain !important;
            object-position: center !important;
            flex-shrink: 0 !important;
            display: block !important;
        }
        
        @media (min-width: 640px) {
            .logo-image {
                width: 36px !important;
                height: 36px !important;
                min-width: 36px !important;
                min-height: 36px !important;
                max-width: 36px !important;
                max-height: 36px !important;
            }
        }
        
        /* Notification Toast Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }
        
        .notification-toast {
            position: relative;
            z-index: 50;
        }
        
        .notification-toast button {
            transition: all 0.2s ease;
        }
        
        .notification-toast button:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Site Name -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 hover:opacity-80 transition-opacity">
                    <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="logo-image">
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-gray-800">Dashboard User</h1>
                        <p class="text-xs text-gray-600 hidden sm:block">{{ \App\Helpers\SettingsHelper::siteName() }}</p>
                    </div>
                </a>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 px-2 sm:px-3 py-2 rounded-md text-sm font-medium touch-target">
                            @if(Auth::user()->profile && Auth::user()->profile->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-primary-200">
                            @else
                                <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                            <x-user-role-badge :user="Auth::user()" size="xs" />
                            <i class="fas fa-chevron-down text-xs hidden sm:inline"></i>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                             style="display: none;">
                            @if(auth()->user()->role !== 'user')
                                <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>
                                Profil Saya
                            </a>
                            <a href="{{ route('user.bookmarks') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-bookmark mr-2"></i>
                                Bookmark Saya
                            </a>
                            <a href="{{ route('user.reading-history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-history mr-2"></i>
                                Riwayat Membaca
                            </a>
                            <a href="{{ route('user.following') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-plus mr-2"></i>
                                Penulis yang Diikuti
                            </a>
                            @if(auth()->user()->role === 'user')
                                <a href="{{ route('user.upgrade-request') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-arrow-up mr-2"></i>
                                    Ajukan Menjadi Penulis
                                </a>
                            @endif
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Quick nav (mobile) -->
    <div class="bg-white border-b border-gray-200 sm:hidden overflow-x-auto nav-scroll">
        <div class="flex gap-1 px-3 py-2 min-w-max">
            <a href="{{ route('user.dashboard') }}" class="px-3 py-2 text-xs font-semibold rounded-lg whitespace-nowrap {{ request()->routeIs('user.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600' }}">Dashboard</a>
            <a href="{{ route('user.bookmarks') }}" class="px-3 py-2 text-xs font-semibold rounded-lg whitespace-nowrap {{ request()->routeIs('user.bookmarks') ? 'bg-primary-50 text-primary-700' : 'text-gray-600' }}">Bookmark</a>
            <a href="{{ route('user.reading-history') }}" class="px-3 py-2 text-xs font-semibold rounded-lg whitespace-nowrap {{ request()->routeIs('user.reading-history') ? 'bg-primary-50 text-primary-700' : 'text-gray-600' }}">Riwayat</a>
            <a href="{{ route('user.following') }}" class="px-3 py-2 text-xs font-semibold rounded-lg whitespace-nowrap {{ request()->routeIs('user.following') ? 'bg-primary-50 text-primary-700' : 'text-gray-600' }}">Following</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 min-w-0">
        @if(session('success'))
            <div class="notification-toast notification-success mb-6 animate-slide-down">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg shadow-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-green-900">Berhasil!</p>
                            <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.closest('.notification-toast').remove()" class="ml-4 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="notification-toast notification-error mb-6 animate-slide-down">
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-lg shadow-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-red-900">Error!</p>
                            <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.closest('.notification-toast').remove()" class="ml-4 text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} {{ \App\Helpers\SettingsHelper::siteName() }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
    
    @stack('scripts')
</body>
</html>

