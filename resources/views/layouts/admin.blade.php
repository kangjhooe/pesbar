<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - Portal Berita Kabupaten Pesisir Barat')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">
    
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
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Mobile Menu Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" id="mobile-overlay"></div>
        
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg fixed lg:fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col" id="sidebar">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                        <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="w-10 h-10 max-w-full object-contain">
                        <div>
                            <h1 class="text-lg font-bold text-gray-800">Admin Panel</h1>
                            <p class="text-xs text-gray-600">{{ \App\Helpers\SettingsHelper::siteName() }}</p>
                        </div>
                    </a>
                    <!-- Mobile close button -->
                    <button class="lg:hidden p-2 rounded-lg hover:bg-gray-100" onclick="toggleSidebar()">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto">
                <div class="px-3 py-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-600' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors">
                        <i class="fas fa-newspaper mr-3"></i>
                        <span>Artikel</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors">
                        <i class="fas fa-folder mr-3"></i>
                        <span>Kategori</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors">
                        <i class="fas fa-comments mr-3"></i>
                        <span>Komentar</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors">
                        <i class="fas fa-envelope mr-3"></i>
                        <span>Newsletter</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors">
                        <i class="fas fa-cog mr-3"></i>
                        <span>Pengaturan</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 lg:px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <button class="lg:hidden p-2 rounded-lg hover:bg-gray-100" onclick="toggleSidebar()">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <div>
                            <h2 class="text-lg lg:text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs lg:text-sm text-gray-600">@yield('page-subtitle', 'Selamat datang di admin panel')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        <!-- User Menu -->
                        <div class="relative">
                            <button class="flex items-center space-x-2 lg:space-x-3 text-gray-700 hover:text-primary-600 transition-colors" onclick="toggleUserMenu()">
                                @if(Auth::user()->profile && Auth::user()->profile->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-primary-200">
                                @else
                                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                <x-user-role-badge :user="Auth::user()" size="xs" />
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 hidden" id="user-menu">
                                <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-home mr-2"></i>
                                    Lihat Website
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
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
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
        
        // Toggle user menu
        function toggleUserMenu() {
            const userMenu = document.getElementById('user-menu');
            userMenu.classList.toggle('hidden');
        }
        
        // Close sidebar when clicking overlay
        document.getElementById('mobile-overlay').addEventListener('click', function() {
            toggleSidebar();
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (!userMenu.classList.contains('hidden') && !userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
