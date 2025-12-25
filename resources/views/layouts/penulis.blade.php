<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Penulis - Portal Berita Kabupaten Pesisir Barat')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">
    
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
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Mobile Menu Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" id="mobile-overlay"></div>
        
        <!-- Sidebar -->
        <aside class="w-full lg:w-64 bg-white shadow-lg fixed lg:fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col" id="sidebar">
            <!-- Logo -->
            <div class="p-4 lg:p-6 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="w-8 h-8 lg:w-10 lg:h-10 object-contain">
                        <div>
                            <h1 class="text-base lg:text-lg font-bold text-gray-800">Dashboard Penulis</h1>
                            <p class="text-xs text-gray-600">{{ \App\Helpers\SettingsHelper::siteName() }}</p>
                        </div>
                    </div>
                    <!-- Mobile close button -->
                    <button class="lg:hidden p-2 rounded-lg hover:bg-gray-100 touch-target" onclick="toggleSidebar()" aria-label="Close sidebar">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto">
                <div class="px-3 py-4 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('penulis.dashboard') }}" class="flex items-center px-3 lg:px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors touch-target {{ request()->routeIs('penulis.dashboard') ? 'bg-primary-50 text-primary-600' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-sm lg:text-base"></i>
                        <span class="text-sm lg:text-base">Dashboard</span>
                    </a>

                    <!-- Artikel Management -->
                    <div class="mt-4">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Artikel</h3>
                        
                        <a href="{{ route('penulis.dashboard') }}#articles" class="flex items-center px-3 lg:px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors touch-target">
                            <i class="fas fa-newspaper mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">Artikel Saya</span>
                        </a>
                        
                        <a href="{{ route('penulis.articles.create') }}" class="flex items-center px-3 lg:px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors touch-target {{ request()->routeIs('penulis.articles.create') ? 'bg-primary-50 text-primary-600' : '' }}">
                            <i class="fas fa-plus-circle mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">Buat Artikel Baru</span>
                        </a>
                    </div>

                    <!-- Profil & Pengaturan -->
                    <div class="mt-4">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Profil</h3>
                        
                        <a href="{{ route('penulis.profile') }}" class="flex items-center px-3 lg:px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors touch-target {{ request()->routeIs('penulis.profile') ? 'bg-primary-50 text-primary-600' : '' }}">
                            <i class="fas fa-user mr-3 text-sm lg:text-base"></i>
                            <span class="text-sm lg:text-base">Profil Saya</span>
                        </a>
                        
                        @if(auth()->user()->isPenulis() && !auth()->user()->isVerified())
                            @if(auth()->user()->canRequestVerification())
                                <a href="{{ route('penulis.verification.request') }}" class="flex items-center px-3 lg:px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors touch-target {{ request()->routeIs('penulis.verification.*') ? 'bg-primary-50 text-primary-600' : '' }}">
                                    <i class="fas fa-check-circle mr-3 text-sm lg:text-base"></i>
                                    <span class="text-sm lg:text-base">Ajukan Verifikasi</span>
                                </a>
                            @elseif(auth()->user()->hasPendingVerificationRequest())
                                <div class="flex items-center px-3 lg:px-4 py-3 text-yellow-600 rounded-lg bg-yellow-50">
                                    <i class="fas fa-clock mr-3 text-sm lg:text-base"></i>
                                    <span class="text-sm lg:text-base">Verifikasi Pending</span>
                                </div>
                            @endif
                        @elseif(auth()->user()->isVerified())
                            <div class="flex items-center px-3 lg:px-4 py-3 text-green-600 rounded-lg bg-green-50">
                                <i class="fas fa-check-circle mr-3 text-sm lg:text-base"></i>
                                <span class="text-sm lg:text-base">Terverifikasi</span>
                            </div>
                        @endif
                    </div>
                </div>
            </nav>
            
            <!-- User Info -->
            <div class="p-4 border-t border-gray-200 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">Penulis</p>
                    </div>

                </div>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('home') }}" class="w-full flex items-center justify-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors touch-target">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Lihat Website
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors touch-target">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 safe-top">
                <div class="flex items-center justify-between px-4 lg:px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <button class="lg:hidden p-2 rounded-lg hover:bg-gray-100 touch-target" onclick="toggleSidebar()" aria-label="Open sidebar">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <div>
                            <h2 class="text-lg lg:text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs lg:text-sm text-gray-600">@yield('page-subtitle', 'Kelola artikel dan profil Anda')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        <!-- View Website Button -->
                        <a href="{{ route('home') }}" class="hidden sm:flex items-center px-3 py-2 text-sm text-gray-700 hover:text-primary-600 transition-colors touch-target">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            <span>Lihat Website</span>
                        </a>
                        
                        <!-- Mobile View Website Button -->
                        <a href="{{ route('home') }}" class="sm:hidden p-2 text-gray-700 hover:text-primary-600 transition-colors touch-target" aria-label="View website">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 safe-bottom overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
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
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scrolling
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>


