@extends('layouts.public')

@section('title', $siteTitle)
@section('description', $siteDescription)

@section('content')
<div class="container-responsive py-8">
    @if($breakingNews)
    <!-- Breaking News -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 rounded-lg mb-8">
        <div class="flex items-center space-x-4">
            <div class="bg-red-500 px-4 py-2 rounded-full text-sm font-semibold flex items-center space-x-2">
                <i class="fas fa-bolt"></i>
                <span>BERITA TERKINI</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">
                    <a href="{{ route('articles.show', $breakingNews) }}" class="hover:underline">
                        {{ $breakingNews->title }}
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Layout with Left and Right Sidebars -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-12">
        <!-- Left Sidebar -->
        <div class="lg:col-span-1 space-y-4 order-2 lg:order-1">
            <!-- Trending News Widget -->
            <div class="widget">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-fire text-red-500 mr-2 text-sm"></i>Konten Trending
                        </h3>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $popularArticles->count() }} konten</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($popularArticles as $index => $article)
                        <article class="flex space-x-3 group">
                            <div class="trending-number">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors text-sm">
                                    <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $article->title }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar text-blue-600"></i>
                                        <span>{{ $article->formatted_date }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye text-blue-600"></i>
                                        <span>{{ number_format($article->views) }}</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-chart-line text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-sm">Belum ada data trending</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Contact Important Widget -->
            @include('widgets.contact-important')

            <!-- Popular News Widget -->
            <div class="widget">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-line text-green-500 mr-2 text-sm"></i>Berita Populer
                        </h3>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $popularArticles->count() }} berita</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($popularArticles->take(5) as $article)
                        <article class="flex space-x-3 group">
                            <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 shadow-md">
                                <img src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}" 
                                     alt="{{ $article->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors text-xs">
                                    <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $article->title }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar text-blue-600"></i>
                                        <span>{{ $article->formatted_date }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye text-blue-600"></i>
                                        <span>{{ number_format($article->views) }}</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                        @empty
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada data populer</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2 order-1 lg:order-2">
            <!-- Featured Articles Slider -->
            @if($featuredArticles->count() > 0)
                <div class="relative">
                    <!-- Slider Container -->
                    <div class="slider-container relative overflow-hidden rounded-lg shadow-lg">
                        <div class="slider-wrapper flex transition-transform duration-500 ease-in-out" id="featuredSlider">
                            @foreach($featuredArticles as $index => $featured)
                            <div class="slider-slide w-full flex-shrink-0">
                                <article class="card group">
                                    <div class="relative h-80 overflow-hidden">
                                        <img src="{{ $featured->featured_image ? asset('storage/' . $featured->featured_image) : asset('images/default-news.jpg') }}" 
                                             alt="{{ $featured->title }}" 
                                             class="w-full h-full object-cover image-hover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute top-4 left-4">
                                            <span class="category-badge {{ $featured->type === 'berita' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $featured->category->name }}
                                            </span>
                                        </div>
                                        <div class="absolute top-4 right-4">
                                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $featured->type === 'berita' ? 'bg-blue-600 text-white' : 'bg-green-600 text-white' }}">
                                                {{ ucfirst($featured->type) }}
                                            </span>
                                        </div>
                                        @if($featured->is_breaking)
                                        <div class="absolute bottom-4 left-4">
                                            <span class="breaking-badge">
                                                <i class="fas fa-bolt mr-1"></i>BREAKING
                                            </span>
                                        </div>
                                        @endif
                                        <div class="absolute bottom-4 left-4 right-4">
                                            <h2 class="text-white text-xl font-bold mb-2 line-clamp-2">
                                                <a href="{{ route('articles.show', $featured) }}" class="hover:text-yellow-300 transition-colors">
                                                    {{ $featured->title }}
                                                </a>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <p class="text-gray-600 mb-4 line-clamp-3">
                                            {{ $featured->excerpt }}
                                        </p>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <div class="flex items-center space-x-4">
                                                <span class="flex items-center space-x-1">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                    @if($featured->author && $featured->author->isPenulis() && $featured->author->username)
                                                        <a href="{{ route('penulis.public-profile', $featured->author->username) }}" class="hover:text-blue-700 font-medium">
                                                            {{ $featured->author->name ?? 'Admin' }}
                                                        </a>
                                                    @else
                                                        <span>{{ $featured->author->name ?? 'Admin' }}</span>
                                                    @endif
                                                    @if($featured->author)
                                                        <x-user-role-badge :user="$featured->author" size="xs" />
                                                    @endif
                                                </span>
                                                <span class="flex items-center space-x-1">
                                                    <i class="fas fa-calendar text-blue-600"></i>
                                                    <span>{{ $featured->formatted_date }}</span>
                                                </span>
                                                <span class="flex items-center space-x-1">
                                                    <i class="fas fa-eye text-blue-600"></i>
                                                    <span>{{ number_format($featured->views) }}</span>
                                                </span>
                                            </div>
                                            <a href="{{ route('articles.show', $featured) }}" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center space-x-1">
                                                <span>Baca Selengkapnya</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Navigation Arrows -->
                        <button class="slider-nav slider-prev absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition-all duration-300 z-10" onclick="changeSlide(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="slider-nav slider-next absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition-all duration-300 z-10" onclick="changeSlide(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        
                        <!-- Dots Indicator -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                            @foreach($featuredArticles as $index => $featured)
                            <button class="slider-dot w-3 h-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }} transition-all duration-300" 
                                    onclick="currentSlide({{ $index + 1 }})" 
                                    data-slide="{{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        
                        <!-- Auto-play indicator -->
                        <div class="absolute top-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-xs flex items-center space-x-2 z-10">
                            <i class="fas fa-play text-green-400"></i>
                            <span>Auto Play</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Default featured article if none available -->
                <div class="card">
                    <div class="p-8 text-center">
                        <i class="fas fa-newspaper text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Konten Unggulan</h3>
                        <p class="text-gray-500">Konten unggulan akan ditampilkan di sini</p>
                    </div>
                </div>
            @endif

            <!-- News Grid - Below featured article in center column -->
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($latestArticles as $article)
                    <article class="card group">
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}" 
                                 alt="{{ $article->title }}" 
                                 class="w-full h-full object-cover image-hover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            <div class="absolute top-2 left-2">
                                <span class="category-badge text-xs px-2 py-1 {{ $article->type === 'berita' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $article->category->name }}
                                </span>
                            </div>
                            <div class="absolute top-2 right-2">
                                <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full {{ $article->type === 'berita' ? 'bg-blue-600 text-white' : 'bg-green-600 text-white' }}">
                                    {{ ucfirst($article->type) }}
                                </span>
                            </div>
                            @if($article->is_breaking)
                            <div class="absolute bottom-2 left-2">
                                <span class="breaking-badge text-xs px-1.5 py-0.5">
                                    <i class="fas fa-bolt mr-1"></i>BREAKING
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-gray-800 mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors text-sm">
                                <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-xs mb-2 line-clamp-2">
                                {{ $article->excerpt }}
                            </p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <div class="flex items-center space-x-2">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar text-blue-600 text-xs"></i>
                                        <span>{{ $article->formatted_date }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye text-blue-600 text-xs"></i>
                                        <span>{{ number_format($article->views) }}</span>
                                    </span>
                                </div>
                                <a href="{{ route('articles.show', $article) }}" class="text-blue-600 hover:text-blue-700 font-medium text-xs">
                                    Baca →
                                </a>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="col-span-full">
                        <div class="card">
                            <div class="p-6 text-center">
                                <i class="fas fa-newspaper text-4xl text-gray-300 mb-3"></i>
                                <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Konten</h3>
                                <p class="text-gray-500 text-sm">Berita dan artikel terbaru akan ditampilkan di sini</p>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($latestArticles->hasPages())
                <div class="mt-8 flex justify-center">
                    <div class="pagination-wrapper">
                        {{ $latestArticles->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-1 space-y-4 order-3 lg:order-3">
            <!-- Weather Widget -->
            <div class="widget">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-bold text-gray-800 flex items-center">
                            <i class="home-weather-icon {{ $weatherData['icon'] ?? 'fas fa-cloud-sun' }} text-yellow-500 mr-2 text-sm"></i>Cuaca Hari Ini
                        </h3>
                        <span class="text-xs text-gray-500 bg-green-100 text-green-600 px-2 py-1 rounded-full font-semibold">Live</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl text-yellow-500">
                            <i class="home-weather-icon-large {{ $weatherData['icon'] ?? 'fas fa-sun' }}"></i>
                        </div>
                        <div>
                            <div class="home-weather-temp text-xl font-bold text-gray-800">{{ $weatherData['temperature'] ?? 28 }}°C</div>
                            <div class="home-weather-condition text-gray-600">{{ $weatherData['condition'] ?? 'Cerah' }}</div>
                            <div class="text-sm text-gray-500">
                                <span class="home-weather-location">{{ $weatherData['location'] ?? 'Pesisir Barat' }}</span>
                                @if(isset($weatherData['updated_at']))
                                    <br><span class="home-weather-update text-xs">Update: {{ $weatherData['updated_at'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="grid grid-cols-3 gap-1 text-center">
                            <div class="bg-gray-50 p-1 rounded">
                                <div class="text-xs text-gray-500">Kelembaban</div>
                                <div class="text-xs font-semibold text-blue-600">{{ $weatherData['humidity'] ?? 75 }}%</div>
                            </div>
                            <div class="bg-gray-50 p-1 rounded">
                                <div class="text-xs text-gray-500">Angin</div>
                                <div class="text-xs font-semibold text-green-600">12 km/h</div>
                            </div>
                            <div class="bg-gray-50 p-1 rounded">
                                <div class="text-xs text-gray-500">UV Index</div>
                                <div class="text-xs font-semibold text-orange-600">8</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prayer Times Widget -->
            <div class="widget">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-bold text-gray-800 flex items-center">
                            <i class="fas fa-mosque text-green-500 mr-2 text-sm"></i>Waktu Sholat
                        </h3>
                        <span class="text-xs text-gray-500 bg-green-100 text-green-600 px-2 py-1 rounded-full font-semibold">Hari Ini</span>
                    </div>
                    <div class="text-center mb-3 p-2 bg-green-50 rounded-lg">
                        <div class="home-prayer-location text-xs font-semibold text-green-800">{{ $prayerData['location'] ?? 'Pesisir Barat' }}</div>
                        <div class="home-prayer-date text-xs text-green-600">{{ $prayerData['date'] ? \Carbon\Carbon::parse($prayerData['date'])->format('d-m-Y') : date('d-m-Y') }}</div>
                    </div>
                    <div class="space-y-1">
                        @php
                            $prayers = [
                                'fajr' => ['name' => 'Subuh', 'icon' => 'fas fa-sun', 'color' => 'text-yellow-600'],
                                'dhuhr' => ['name' => 'Dzuhur', 'icon' => 'fas fa-sun', 'color' => 'text-orange-600'],
                                'asr' => ['name' => 'Ashar', 'icon' => 'fas fa-sun', 'color' => 'text-yellow-600'],
                                'maghrib' => ['name' => 'Maghrib', 'icon' => 'fas fa-sun', 'color' => 'text-orange-600'],
                                'isha' => ['name' => 'Isya', 'icon' => 'fas fa-moon', 'color' => 'text-blue-600']
                            ];
                        @endphp
                        
                        @foreach($prayers as $key => $prayer)
                        <div class="flex justify-between items-center p-1 hover:bg-gray-50 rounded">
                            <span class="flex items-center space-x-1">
                                <i class="{{ $prayer['icon'] }} {{ $prayer['color'] }} text-xs"></i>
                                <span class="text-gray-700 font-medium text-xs">{{ $prayer['name'] }}</span>
                            </span>
                            <span class="home-prayer-{{ $key }} font-bold text-gray-900 text-xs">
                                {{ $prayerData['prayers'][$key] ?? '--:--' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @if(isset($prayerData['updated_at']))
                    <div class="text-center mt-3 pt-3 border-t border-gray-100">
                        <div class="home-prayer-update text-xs text-gray-500">Update: {{ $prayerData['updated_at'] }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Events Widget -->
            @include('widgets.events')

            <!-- Poll Widget -->
            @include('widgets.poll')

            <!-- Newsletter -->
            <div class="widget bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200">
                <div class="p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Newsletter</h3>
                            <p class="text-gray-600 text-xs">Dapatkan berita terbaru</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-xs mb-3">Dapatkan berita terbaru langsung di email Anda</p>
                    <form id="newsletterForm" class="space-y-2">
                        @csrf
                        <div class="relative">
                            <input type="email" name="email" placeholder="Masukkan email Anda" 
                                   class="input-field pl-10" required>
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Berlangganan</span>
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Email Anda aman dan tidak akan dibagikan
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Articles Slider Script -->
<script>
// Featured Articles Slider Variables
let currentSlideIndex = 0;
let totalSlides = {{ $featuredArticles->count() }};
let autoPlayInterval;
let isAutoPlayActive = true;

// Initialize slider when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (totalSlides > 0) {
        initializeSlider();
        startAutoPlay();
        
        // Pause auto-play on hover
        const sliderContainer = document.querySelector('.slider-container');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', pauseAutoPlay);
            sliderContainer.addEventListener('mouseleave', startAutoPlay);
        }
    }
    
    // Newsletter Form
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalHTML = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            button.disabled = true;
            button.classList.add('loading');
            
            try {
                const response = await fetch('{{ route("newsletter.subscribe") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    showNotification(data.message, 'success');
                    this.reset();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                button.innerHTML = originalHTML;
                button.disabled = false;
                button.classList.remove('loading');
            }
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add fade-in animation to cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe all cards
    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
    
    // Enhanced image loading
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });
        
        img.addEventListener('error', function() {
            this.src = '{{ asset("images/default-news.jpg") }}';
        });
    });
});

// Slider Functions
function initializeSlider() {
    const slider = document.getElementById('featuredSlider');
    if (slider) {
        slider.style.transform = 'translateX(0)';
    }
    updateDots();
}

function changeSlide(direction) {
    if (totalSlides <= 1) return;
    
    currentSlideIndex += direction;
    
    // Loop back to first slide if at the end
    if (currentSlideIndex >= totalSlides) {
        currentSlideIndex = 0;
    }
    // Loop to last slide if going backwards from first
    else if (currentSlideIndex < 0) {
        currentSlideIndex = totalSlides - 1;
    }
    
    updateSliderPosition();
    updateDots();
}

function currentSlide(slideNumber) {
    if (totalSlides <= 1) return;
    
    currentSlideIndex = slideNumber - 1;
    updateSliderPosition();
    updateDots();
}

function updateSliderPosition() {
    const slider = document.getElementById('featuredSlider');
    if (slider) {
        const translateX = -currentSlideIndex * 100;
        slider.style.transform = `translateX(${translateX}%)`;
    }
}

function updateDots() {
    const dots = document.querySelectorAll('.slider-dot');
    dots.forEach((dot, index) => {
        if (index === currentSlideIndex) {
            dot.classList.remove('bg-white/50');
            dot.classList.add('bg-white');
        } else {
            dot.classList.remove('bg-white');
            dot.classList.add('bg-white/50');
        }
    });
}

function startAutoPlay() {
    if (totalSlides <= 1) return;
    
    pauseAutoPlay(); // Clear any existing interval
    autoPlayInterval = setInterval(() => {
        if (isAutoPlayActive) {
            changeSlide(1);
        }
    }, 2000); // Auto-play every 2 seconds
}

function pauseAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
}

// Touch/Swipe support for mobile
let startX = 0;
let endX = 0;

document.addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
});

document.addEventListener('touchend', function(e) {
    endX = e.changedTouches[0].clientX;
    handleSwipe();
});

function handleSwipe() {
    const threshold = 50; // Minimum swipe distance
    const diff = startX - endX;
    
    if (Math.abs(diff) > threshold) {
        if (diff > 0) {
            // Swipe left - next slide
            changeSlide(1);
        } else {
            // Swipe right - previous slide
            changeSlide(-1);
        }
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-white'
    };
    
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle',
        warning: 'fas fa-exclamation-triangle'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="${icons[type]}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Widget Auto-Refresh System
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh widget data every 30 minutes
    setInterval(function() {
        updateHomeWidgetData();
    }, 30 * 60 * 1000); // 30 minutes
    
    // Initial update
    updateHomeWidgetData();
    
    function updateHomeWidgetData() {
        // Update weather widget
        fetch('/api/widgets/weather')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateHomeWeatherWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Home weather update failed:', error);
            });
        
        // Update prayer times widget
        fetch('/api/widgets/prayer-times')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateHomePrayerTimesWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Home prayer times update failed:', error);
            });
    }
    
    function updateHomeWeatherWidget(weatherData) {
        const weatherIcon = document.querySelector('.home-weather-icon');
        const weatherIconLarge = document.querySelector('.home-weather-icon-large');
        const weatherTemp = document.querySelector('.home-weather-temp');
        const weatherCondition = document.querySelector('.home-weather-condition');
        const weatherLocation = document.querySelector('.home-weather-location');
        const weatherUpdate = document.querySelector('.home-weather-update');
        
        if (weatherIcon) weatherIcon.className = 'home-weather-icon ' + weatherData.icon + ' text-yellow-500 mr-2';
        if (weatherIconLarge) weatherIconLarge.className = 'home-weather-icon-large ' + weatherData.icon;
        if (weatherTemp) weatherTemp.textContent = weatherData.temperature + '°C';
        if (weatherCondition) weatherCondition.textContent = weatherData.condition;
        if (weatherLocation) weatherLocation.textContent = weatherData.location;
        if (weatherUpdate) weatherUpdate.textContent = 'Update: ' + weatherData.updated_at;
    }
    
    function updateHomePrayerTimesWidget(prayerData) {
        const prayerLocation = document.querySelector('.home-prayer-location');
        const prayerDate = document.querySelector('.home-prayer-date');
        const prayerUpdate = document.querySelector('.home-prayer-update');
        
        if (prayerLocation) prayerLocation.textContent = prayerData.location;
        if (prayerDate) prayerDate.textContent = new Date(prayerData.date).toLocaleDateString('id-ID');
        if (prayerUpdate) prayerUpdate.textContent = 'Update: ' + prayerData.updated_at;
        
        // Update prayer times
        const prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        prayers.forEach(prayer => {
            const element = document.querySelector(`.home-prayer-${prayer}`);
            if (element && prayerData.prayers[prayer]) {
                element.textContent = prayerData.prayers[prayer];
            }
        });
    }
});
</script>

<style>
.trending-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
    flex-shrink: 0;
}
</style>
@endsection
