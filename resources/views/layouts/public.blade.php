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
    
    <!-- Open Graph Meta Tags -->
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
    
    <!-- Twitter Card Meta Tags -->
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
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Popup Animation Styles */
        #popupContent {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(0.95);
            opacity: 0;
        }
        
        /* Button hover effects */
        .popup-button {
            transition: all 0.2s ease;
        }
        
        .popup-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Pulse animation for bell icon */
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .bell-pulse {
            animation: pulse 2s infinite;
        }
        
        /* Gradient text effect */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        /* Input Styles */
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Container Styles */
        .container-responsive {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        @media (min-width: 640px) {
            .container-responsive {
                padding: 0 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .container-responsive {
                padding: 0 2rem;
            }
        }
        
        /* Grid Styles */
        .grid-responsive {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .grid-responsive {
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            }
        }
        
        /* Heading Styles */
        .heading-responsive {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        @media (min-width: 768px) {
            .heading-responsive {
                font-size: 1.875rem;
            }
        }
        
        /* Widget Styles */
        .widget {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .widget:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        /* Trending Number Styles */
        .trending-number {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Category Badge Styles */
        .category-badge {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Breaking News Badge */
        .breaking-badge {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        /* Image Hover Effects */
        .image-hover {
            transition: transform 0.3s ease;
        }
        
        .card:hover .image-hover {
            transform: scale(1.05);
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #f3f4f6;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <div class="relative">
                            <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="h-10 w-auto">
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-800">{{ \App\Helpers\SettingsHelper::siteName() }}</h1>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                {{-- <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('articles.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('articles.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Berita
                    </a>
                    <a href="{{ route('articles.artikel') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('articles.artikel*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Artikel
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('about') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Tentang
                    </a>
                </div> --}}

                <!-- Right Side - Search and Auth -->
                <div class="flex items-center space-x-4">
                    <!-- Search Box -->
                    <div class="hidden lg:block">
                        <form action="{{ route('search.index') }}" method="GET" class="relative">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Cari berita..." 
                                   value="{{ request('q') }}"
                                   class="w-48 pl-4 pr-10 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Auth Links -->
                    @auth
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profil
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Links -->
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Daftar
                        </a>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-blue-600 p-2 rounded-md hover:bg-gray-100 transition-colors">
                            <i class="fas fa-bars text-lg transition-transform duration-300" :class="{'rotate-90': mobileMenuOpen}"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <!-- Mobile Search -->
                <div class="px-3 py-2">
                    <form action="{{ route('search.index') }}" method="GET" class="flex items-center">
                        <input type="text" 
                               name="q" 
                               placeholder="Cari berita..." 
                               value="{{ request('q') }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Navigation Links -->
                {{-- <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('articles.index') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium {{ request()->routeIs('articles.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Berita
                </a>
                <a href="{{ route('articles.artikel') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium {{ request()->routeIs('articles.artikel*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Artikel
                </a>
                <a href="{{ route('about') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium {{ request()->routeIs('about') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Tentang
                </a> --}}
                
                @auth
                    <hr class="my-2">
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" @click="mobileMenuOpen = false" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                            Logout
                        </button>
                    </form>
                @else
                    <hr class="my-2">
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <!-- Logo & Site Name -->
                <div class="flex justify-center items-center space-x-3 mb-4">
                    <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="h-10 w-auto">
                    <h3 class="text-lg font-bold">{{ \App\Helpers\SettingsHelper::siteName() }}</h3>
                </div>
                
                <!-- Quick Links -->
                <div class="flex justify-center space-x-6 mb-6">
                    <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors text-sm">Beranda</a>
                    <a href="{{ route('articles.index') }}" class="text-gray-300 hover:text-white transition-colors text-sm">Berita</a>
                    {{-- <a href="{{ route('about') }}" class="text-gray-300 hover:text-white transition-colors text-sm">Tentang Kami</a> --}}
                </div>

                <!-- Social Media & Copyright -->
                <div class="flex justify-center space-x-4 mb-4">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-facebook text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fab fa-youtube text-lg"></i>
                    </a>
                </div>
                
                <p class="text-gray-300 text-sm">
                    &copy; {{ date('Y') }} Portal Berita Kabupaten Pesisir Barat. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <!-- Event Popup Modal -->
    @if(isset($eventPopup) && $eventPopup)
    <div id="eventPopup" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-y-auto" id="popupContent">
            <!-- Header dengan gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        <div class="bg-white bg-opacity-20 p-2 rounded-full bell-pulse flex-shrink-0">
                            <i class="fas fa-bell text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base sm:text-lg font-bold truncate">{{ $eventPopup->title }}</h2>
                            <p class="text-blue-100 text-xs sm:text-sm">Pemberitahuan Penting</p>
                        </div>
                    </div>
                    <button id="closePopup" class="text-white hover:text-blue-200 transition-colors p-1 flex-shrink-0 ml-2">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-4 sm:px-6 py-6">
                <div class="mb-6">
                    <p class="text-gray-700 leading-relaxed text-sm sm:text-base">{{ $eventPopup->message }}</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <button id="closePopupBtn" class="popup-button flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center space-x-2">
                        <i class="fas fa-check"></i>
                        <span>Mengerti</span>
                    </button>
                    <button id="closePopupBtn2" class="popup-button px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        <span class="sm:hidden">Tutup</span>
                    </button>
                </div>
            </div>
            
            <!-- Footer dengan info tanggal -->
            <div class="bg-gray-50 px-4 sm:px-6 py-3 border-t">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0 text-xs sm:text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $eventPopup->start_date->format('d M Y') }} - {{ $eventPopup->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock"></i>
                        <span>Berlaku {{ $eventPopup->start_date->diffInDays($eventPopup->end_date) + 1 }} hari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Event Popup JavaScript -->
    @if(isset($eventPopup) && $eventPopup)
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const popup = document.getElementById("eventPopup");
        const popupContent = document.getElementById("popupContent");
        const closeBtn = document.getElementById("closePopup");
        const closeBtn2 = document.getElementById("closePopupBtn");
        const closeBtn3 = document.getElementById("closePopupBtn2");
        const popupId = {{ $eventPopup->id }};
        const storageKey = "eventPopup_" + popupId;

        // Function to show popup with animation
        function showPopup() {
            popup.classList.remove("hidden");
            // Trigger animation after a small delay
            setTimeout(() => {
                popupContent.style.transform = "scale(1)";
                popupContent.style.opacity = "1";
            }, 10);
        }

        // Function to hide popup with animation
        function hidePopup() {
            popupContent.style.transform = "scale(0.95)";
            popupContent.style.opacity = "0";
            setTimeout(() => {
                popup.classList.add("hidden");
            }, 300);
        }

        // Check if popup has been shown before
        if (popup && !localStorage.getItem(storageKey)) {
            // Show popup after a short delay for better UX
            setTimeout(showPopup, 1500);
            
            // Mark as shown in localStorage
            localStorage.setItem(storageKey, "shown");
        }

        // Close popup functionality for all close buttons
        [closeBtn, closeBtn2, closeBtn3].forEach(btn => {
            if (btn) {
                btn.addEventListener("click", hidePopup);
            }
        });

        // Close popup when clicking outside
        if (popup) {
            popup.addEventListener("click", function(e) {
                if (e.target === popup) {
                    hidePopup();
                }
            });
        }

        // Close popup with Escape key
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape" && popup && !popup.classList.contains("hidden")) {
                hidePopup();
            }
        });

        // Add some interactive effects
        if (popupContent) {
            // Add hover effect to buttons
            const buttons = popupContent.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        }
    });
    </script>
    @endif
    
    <!-- Structured Data -->
    @yield('structured-data')
</body>
</html>
