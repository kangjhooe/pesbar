@extends('layouts.public')

@section('title', \App\Helpers\SeoHelper::generateTitle($article->title))
@section('description', \App\Helpers\SeoHelper::generateDescription($article->excerpt, $article->content))
@section('keywords', \App\Helpers\SeoHelper::generateKeywords([$article->category->name, $article->type]))

@section('og:title', $article->title)
@section('og:description', \App\Helpers\SeoHelper::generateDescription($article->excerpt, $article->content))
@section('og:image', $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg'))
@section('og:url', route('articles.show', $article))

@section('twitter:title', $article->title)
@section('twitter:description', \App\Helpers\SeoHelper::generateDescription($article->excerpt, $article->content))
@section('twitter:image', $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg'))

@section('canonical', route('articles.show', $article))

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Terjadi kesalahan:</strong>
        </div>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Sidebar -->
        <div class="lg:col-span-3 order-3 lg:order-1">
            <!-- Popular Articles Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Berita Populer</h3>
                <div class="space-y-4">
                    @php
                        $popularArticles = \App\Models\Article::published()
                            ->popular()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($popularArticles->count() > 0)
                        @foreach($popularArticles as $popularArticle)
                        <article class="flex space-x-3">
                            @if($popularArticle->featured_image)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $popularArticle->featured_image) }}" 
                                     alt="{{ $popularArticle->title }}" 
                                     class="w-16 h-16 object-cover rounded">
                            </div>
                            @else
                            <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-sm"></i>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 leading-tight mb-1">
                                    <a href="{{ route('articles.show', $popularArticle) }}" 
                                       class="hover:text-primary-600 transition-colors">
                                        {{ Str::limit($popularArticle->title, 50) }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span>{{ $popularArticle->published_at ? $popularArticle->published_at->format('d-m-Y') : 'Belum dipublikasi' }}</span>
                                    <span>•</span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye"></i>
                                        <span>{{ number_format($popularArticle->views) }}</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">Belum ada berita populer</p>
                    @endif
                </div>
            </div>

            <!-- Categories Widget -->
            @php
                $allCategories = \App\Models\Category::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            @endphp
            
            @if($allCategories->count() > 0)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori</h3>
                <ul class="space-y-2">
                    @foreach($allCategories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-primary-600 transition-colors py-2">
                            <span>{{ $category->name }}</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                {{ $category->publishedArticles()->count() }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Main Content - Mobile First -->
        <div class="lg:col-span-6 lg:col-start-4 order-1 lg:order-2">
            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Article Header -->
                <div class="p-6 border-b border-gray-200">
                    <!-- Category Badge -->
                    <div class="mb-4">
                        <a href="{{ route('categories.show', $article->category) }}" 
                           class="inline-block bg-primary-100 text-primary-800 text-sm font-medium px-3 py-1 rounded-full hover:bg-primary-200 transition-colors">
                            {{ $article->category->name }}
                        </a>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-4">
                        {{ $article->title }}
                    </h1>
                    
                    <!-- Meta Information -->
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user text-primary-600"></i>
                            @if($article->author && $article->author->isPenulis() && $article->author->username)
                                <a href="{{ route('penulis.public-profile', $article->author->username) }}" class="hover:text-primary-700 font-medium">
                                    {{ $article->author->name ?? 'Admin' }}
                                </a>
                            @else
                                <span>{{ $article->author->name ?? 'Admin' }}</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-calendar text-primary-600"></i>
                            <span>{{ $article->published_at ? $article->published_at->format('d-m-Y') : 'Belum dipublikasi' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock text-primary-600"></i>
                            <span>{{ $article->published_at ? $article->published_at->format('H:i') . ' WIB' : 'Belum dipublikasi' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-eye text-primary-600"></i>
                            <span>{{ number_format($article->views) }} kali dilihat</span>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($article->featured_image)
                <div class="aspect-video bg-gray-200">
                    <img src="{{ asset('storage/' . $article->featured_image) }}" 
                         alt="{{ $article->title }}" 
                         class="w-full h-full object-cover">
                </div>
                @endif

                <!-- Article Content -->
                <div class="p-6">
                    <div class="prose prose-lg max-w-none">
                        {!! $article->content !!}
                    </div>
                </div>

                <!-- Article Footer -->
                <div class="p-6 border-t border-gray-200 bg-gray-50">
                <!-- Tags -->
                @if($article->tags && $article->tags->count() > 0)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Tag:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                        <span class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded-full">
                            #{{ $tag->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                    <!-- Share Buttons -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-semibold text-gray-700">Bagikan:</span>
                            <div class="flex space-x-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                                    <i class="fab fa-facebook-f text-xs"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors">
                                    <i class="fab fa-twitter text-xs"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->url()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center hover:bg-green-700 transition-colors">
                                    <i class="fab fa-whatsapp text-xs"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Back Button -->
                        <a href="{{ route('articles.index') }}" 
                           class="inline-flex items-center space-x-2 text-primary-600 hover:text-primary-700 font-medium transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali ke Berita</span>
                        </a>
                    </div>
                </div>
            </article>

            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-8" id="comments-section">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-comments mr-2 text-primary-600"></i>
                        Komentar (<span id="comments-count">{{ $article->approvedComments->where('parent_id', null)->count() }}</span>)
                    </h3>
                </div>
                
                <!-- Comment Form -->
                <div class="mb-8 border-b border-gray-200 pb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-edit mr-2 text-primary-600"></i>Tulis Komentar
                    </h4>
                    
                    @auth
                        <form id="comment-form" action="{{ route('comments.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="article_id" value="{{ $article->id }}">
                            <input type="hidden" name="parent_id" id="reply-to-id" value="">
                            
                            <div class="mb-4 p-3 bg-gradient-to-r from-blue-50 to-primary-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800 flex items-center">
                                    <i class="fas fa-user-circle mr-2"></i>
                                    Anda berkomentar sebagai <strong>{{ auth()->user()->name }}</strong>
                                </p>
                            </div>
                            
                            <div id="reply-indicator" class="hidden mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-yellow-800">
                                        <i class="fas fa-reply mr-2"></i>
                                        Membalas komentar dari <strong id="reply-to-name"></strong>
                                    </span>
                                    <button type="button" onclick="cancelReply()" class="text-yellow-600 hover:text-yellow-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Komentar <span class="text-red-500">*</span>
                                </label>
                                <textarea name="comment" 
                                          id="comment" 
                                          rows="5" 
                                          required
                                          maxlength="1000"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                          placeholder="Tulis komentar Anda di sini... (Maksimal 1000 karakter)"></textarea>
                                <div class="flex justify-end items-center mt-1">
                                    <span class="text-xs text-gray-500">
                                        <span id="char-count">0</span>/1000 karakter
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <button type="submit" 
                                        id="submit-comment-btn"
                                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-all font-medium shadow-md hover:shadow-lg transform hover:scale-105 flex items-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Kirim Komentar</span>
                                </button>
                                <button type="button" 
                                        id="cancel-reply-btn"
                                        onclick="cancelReply()"
                                        class="hidden px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                    Batal
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-6 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg text-center">
                            <i class="fas fa-lock text-yellow-600 text-4xl mb-3"></i>
                            <p class="text-gray-700 mb-4 font-medium">
                                Anda harus <strong>login</strong> terlebih dahulu untuk berkomentar.
                            </p>
                            <a href="{{ route('login') }}" 
                               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all font-medium shadow-md hover:shadow-lg transform hover:scale-105">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
                            </a>
                            <p class="text-sm text-gray-600 mt-3">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-800 font-medium underline">Daftar di sini</a>
                            </p>
                        </div>
                    @endauth
                </div>

                <!-- Comments List -->
                <div id="comments-list" class="space-y-6">
                    @if($article->approvedComments->where('parent_id', null)->count() > 0)
                        @foreach($article->approvedComments->where('parent_id', null) as $comment)
                            @include('comments.comment-item', ['comment' => $comment, 'level' => 0])
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-500 text-lg">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-3 order-2 lg:order-3">
            <!-- Related Articles -->
            @if($relatedArticles->count() > 0)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Berita Terkait</h3>
                <div class="space-y-4">
                    @foreach($relatedArticles as $relatedArticle)
                    <article class="flex space-x-3">
                        @if($relatedArticle->featured_image)
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $relatedArticle->featured_image) }}" 
                                 alt="{{ $relatedArticle->title }}" 
                                 class="w-16 h-16 object-cover rounded">
                        </div>
                        @else
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-sm"></i>
                        </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 leading-tight mb-1">
                                <a href="{{ route('articles.show', $relatedArticle) }}" 
                                   class="hover:text-primary-600 transition-colors">
                                    {{ Str::limit($relatedArticle->title, 50) }}
                                </a>
                            </h4>
                            <p class="text-xs text-gray-600">
                                {{ $relatedArticle->published_at ? $relatedArticle->published_at->format('d-m-Y') : 'Belum dipublikasi' }}
                            </p>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Contact Important Widget -->
            @include('widgets.contact-important')

            <!-- Events Widget -->
            @include('widgets.events')

            <!-- Weather Widget -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="weather-widget {{ $weatherData['icon'] ?? 'fas fa-cloud-sun' }} text-yellow-500 mr-2"></i>Cuaca Hari Ini
                    </h3>
                    <span class="text-xs text-gray-500 bg-green-100 text-green-600 px-2 py-1 rounded-full font-semibold">Live</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-4xl text-yellow-500">
                        <i class="weather-widget-large {{ $weatherData['icon'] ?? 'fas fa-sun' }}"></i>
                    </div>
                    <div>
                        <div class="weather-temp text-2xl font-bold text-gray-800">{{ $weatherData['temperature'] ?? 28 }}°C</div>
                        <div class="weather-condition text-gray-600">{{ $weatherData['condition'] ?? 'Cerah' }}</div>
                        <div class="text-sm text-gray-500">
                            <span class="weather-location">{{ $weatherData['location'] ?? 'Pesisir Barat' }}</span>
                            @if(isset($weatherData['updated_at']))
                                <br><span class="weather-update text-xs">Update: {{ $weatherData['updated_at'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <div class="text-xs text-gray-500">Kelembaban</div>
                            <div class="text-sm font-semibold text-blue-600">{{ $weatherData['humidity'] ?? 75 }}%</div>
                        </div>
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <div class="text-xs text-gray-500">Angin</div>
                            <div class="text-sm font-semibold text-green-600">12 km/h</div>
                        </div>
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <div class="text-xs text-gray-500">UV Index</div>
                            <div class="text-sm font-semibold text-orange-600">8</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prayer Times Widget -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 mb-8">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-mosque text-green-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Waktu Sholat</h3>
                </div>
                <div class="text-center mb-4 p-3 bg-green-50 rounded-lg">
                    <div class="prayer-location text-sm font-semibold text-green-800">{{ $prayerData['location'] ?? 'Pesisir Barat' }}</div>
                    <div class="prayer-date text-xs text-green-600">{{ $prayerData['date'] ? \Carbon\Carbon::parse($prayerData['date'])->format('d-m-Y') : date('d-m-Y') }}</div>
                </div>
                <div class="space-y-3">
                    @php
                        $prayers = [
                            'fajr' => ['name' => 'Subuh', 'icon' => 'fas fa-sun', 'bg' => 'bg-yellow-100', 'color' => 'text-yellow-600'],
                            'dhuhr' => ['name' => 'Dzuhur', 'icon' => 'fas fa-sun', 'bg' => 'bg-orange-100', 'color' => 'text-orange-600'],
                            'asr' => ['name' => 'Ashar', 'icon' => 'fas fa-sun', 'bg' => 'bg-yellow-100', 'color' => 'text-yellow-600'],
                            'maghrib' => ['name' => 'Maghrib', 'icon' => 'fas fa-sun', 'bg' => 'bg-orange-100', 'color' => 'text-orange-600'],
                            'isha' => ['name' => 'Isya', 'icon' => 'fas fa-moon', 'bg' => 'bg-blue-100', 'color' => 'text-blue-600']
                        ];
                    @endphp
                    
                    @foreach($prayers as $key => $prayer)
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <span class="flex items-center space-x-3">
                            <div class="w-8 h-8 {{ $prayer['bg'] }} rounded-full flex items-center justify-center">
                                <i class="{{ $prayer['icon'] }} {{ $prayer['color'] }} text-sm"></i>
                            </div>
                            <span class="text-gray-700 font-medium">{{ $prayer['name'] }}</span>
                        </span>
                        <span class="prayer-{{ $key }} font-bold text-gray-900">
                            {{ $prayerData['prayers'][$key] ?? '--:--' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @if(isset($prayerData['updated_at']))
                <div class="text-center mt-3 pt-3 border-t border-gray-100">
                    <div class="prayer-update text-xs text-gray-500">Update: {{ $prayerData['updated_at'] }}</div>
                </div>
                @endif
            </div>

            <!-- Poll Widget -->
            @include('widgets.poll')

            <!-- Newsletter Subscription -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6">
                <div class="flex items-center space-x-2 mb-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-envelope text-blue-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Berlangganan Newsletter</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">
                    Dapatkan berita terbaru langsung di email Anda
                </p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Masukkan email Anda" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Berlangganan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('structured-data')
<script type="application/ld+json">
{!! json_encode(\App\Helpers\SeoHelper::generateArticleStructuredData($article), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh widget data every 30 minutes
    setInterval(function() {
        updateWidgetData();
    }, 30 * 60 * 1000); // 30 minutes
    
    // Initial update
    updateWidgetData();
    
    function updateWidgetData() {
        // Update weather widget
        fetch('/api/widgets/weather')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateWeatherWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Weather update failed:', error);
            });
        
        // Update prayer times widget
        fetch('/api/widgets/prayer-times')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePrayerTimesWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Prayer times update failed:', error);
            });
    }
    
    function updateWeatherWidget(weatherData) {
        const weatherIcon = document.querySelector('.weather-widget');
        const weatherIconLarge = document.querySelector('.weather-widget-large');
        const weatherTemp = document.querySelector('.weather-temp');
        const weatherCondition = document.querySelector('.weather-condition');
        const weatherLocation = document.querySelector('.weather-location');
        const weatherUpdate = document.querySelector('.weather-update');
        
        if (weatherIcon) weatherIcon.className = 'weather-widget ' + weatherData.icon + ' text-yellow-500 mr-2';
        if (weatherIconLarge) weatherIconLarge.className = 'weather-widget-large ' + weatherData.icon;
        if (weatherTemp) weatherTemp.textContent = weatherData.temperature + '°C';
        if (weatherCondition) weatherCondition.textContent = weatherData.condition;
        if (weatherLocation) weatherLocation.textContent = weatherData.location;
        if (weatherUpdate) weatherUpdate.textContent = 'Update: ' + weatherData.updated_at;
    }
    
    function updatePrayerTimesWidget(prayerData) {
        const prayerLocation = document.querySelector('.prayer-location');
        const prayerDate = document.querySelector('.prayer-date');
        const prayerUpdate = document.querySelector('.prayer-update');
        
        if (prayerLocation) prayerLocation.textContent = prayerData.location;
        if (prayerDate) prayerDate.textContent = new Date(prayerData.date).toLocaleDateString('id-ID');
        if (prayerUpdate) prayerUpdate.textContent = 'Update: ' + prayerData.updated_at;
        
        // Update prayer times
        const prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        prayers.forEach(prayer => {
            const element = document.querySelector(`.prayer-${prayer}`);
            if (element && prayerData.prayers[prayer]) {
                element.textContent = prayerData.prayers[prayer];
            }
        });
    }

    // ========== COMMENT SYSTEM FUNCTIONALITY ==========
    
    // Character counter
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    
    if (commentTextarea && charCount) {
        commentTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 900) {
                charCount.classList.add('text-red-500', 'font-semibold');
            } else {
                charCount.classList.remove('text-red-500', 'font-semibold');
            }
        });
    }

    // AJAX Comment Submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-comment-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add comment to list immediately (semua komentar langsung disetujui)
                    addCommentToDOM(data.comment);
                    showNotification('Komentar berhasil dikirim!', 'success');
                    commentForm.reset();
                    document.getElementById('char-count').textContent = '0';
                    cancelReply();
                } else {
                    showNotification(data.error || 'Terjadi kesalahan saat mengirim komentar.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Reply to comment
    window.replyToComment = function(commentId, commenterName) {
        if (!commentId) {
            console.error('Comment ID is required');
            return;
        }
        
        const replyToId = document.getElementById('reply-to-id');
        const replyToName = document.getElementById('reply-to-name');
        const replyIndicator = document.getElementById('reply-indicator');
        const cancelBtn = document.getElementById('cancel-reply-btn');
        const commentForm = document.getElementById('comment-form');
        const commentTextarea = document.getElementById('comment');
        
        if (!replyToId || !replyToName || !replyIndicator || !cancelBtn || !commentForm) {
            console.error('Required elements not found');
            return;
        }
        
        replyToId.value = commentId;
        replyToName.textContent = commenterName || 'User';
        replyIndicator.classList.remove('hidden');
        cancelBtn.classList.remove('hidden');
        
        // Scroll to comment form
        commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (commentTextarea) {
            setTimeout(() => commentTextarea.focus(), 300);
        }
    };

    // Cancel reply
    window.cancelReply = function() {
        document.getElementById('reply-to-id').value = '';
        document.getElementById('reply-indicator').classList.add('hidden');
        document.getElementById('cancel-reply-btn').classList.add('hidden');
    };

    // Toggle like/dislike
    window.toggleLike = function(commentId, isLike) {
        if (!commentId) {
            console.error('Comment ID is required');
            return;
        }
        
        @guest
        window.location.href = '{{ route("login") }}';
        return;
        @endguest

        // Disable button during request
        const likeBtn = document.getElementById(`like-btn-${commentId}`);
        const dislikeBtn = document.getElementById(`dislike-btn-${commentId}`);
        if (likeBtn) likeBtn.disabled = true;
        if (dislikeBtn) dislikeBtn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        fetch(`/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ is_like: isLike })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const likesCountEl = document.getElementById(`likes-count-${commentId}`);
                const dislikesCountEl = document.getElementById(`dislikes-count-${commentId}`);
                
                if (likesCountEl) likesCountEl.textContent = data.likes_count || 0;
                if (dislikesCountEl) dislikesCountEl.textContent = data.dislikes_count || 0;
                
                // Update like button style
                if (likeBtn) {
                    if (data.is_liked) {
                        likeBtn.classList.add('bg-green-100', 'text-green-700');
                        likeBtn.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-green-50');
                    } else {
                        likeBtn.classList.remove('bg-green-100', 'text-green-700');
                        likeBtn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-green-50');
                    }
                }
                
                // Update dislike button style
                if (dislikeBtn) {
                    if (data.is_disliked) {
                        dislikeBtn.classList.add('bg-red-100', 'text-red-700');
                        dislikeBtn.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-red-50');
                    } else {
                        dislikeBtn.classList.remove('bg-red-100', 'text-red-700');
                        dislikeBtn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-red-50');
                    }
                }
            } else {
                showNotification(data.error || 'Terjadi kesalahan saat memproses like/dislike.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMsg = error.error || error.message || 'Terjadi kesalahan saat memproses like/dislike.';
            showNotification(errorMsg, 'error');
        })
        .finally(() => {
            if (likeBtn) likeBtn.disabled = false;
            if (dislikeBtn) dislikeBtn.disabled = false;
        });
    };

    // Edit comment
    window.editComment = function(commentId, commentText) {
        const modal = document.getElementById(`edit-comment-modal-${commentId}`);
        const textarea = document.getElementById(`edit-comment-text-${commentId}`);
        const charCount = document.getElementById(`edit-char-count-${commentId}`);
        
        if (modal && textarea) {
            textarea.value = commentText;
            charCount.textContent = commentText.length;
            modal.classList.remove('hidden');
            
            // Character counter for edit
            textarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
            
            // Handle form submission
            const form = document.getElementById(`edit-comment-form-${commentId}`);
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                    
                    fetch(form.action, {
                        method: 'PUT',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const commentTextEl = document.querySelector(`#comment-${commentId} .comment-text p`);
                            if (commentTextEl) {
                                commentTextEl.textContent = data.comment.comment;
                            }
                            closeEditModal(commentId);
                            showNotification('Komentar berhasil diperbarui.', 'success');
                        } else {
                            showNotification(data.error || 'Terjadi kesalahan saat memperbarui komentar.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan saat memperbarui komentar.', 'error');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        }
    };

    // Close edit modal
    window.closeEditModal = function(commentId) {
        const modal = document.getElementById(`edit-comment-modal-${commentId}`);
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Add comment to DOM
    function addCommentToDOM(commentData) {
        const commentsList = document.getElementById('comments-list');
        const commentsCount = document.getElementById('comments-count');
        
        if (!commentsList) return;
        
        // Escape HTML untuk keamanan
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };
        
        const commentName = escapeHtml(commentData.name || 'User');
        const commentText = escapeHtml(commentData.comment || '');
        const parentId = commentData.parent_id || null;
        
        // Create comment HTML
        const commentHTML = `
            <div class="comment-item border-l-4 ${parentId ? 'border-gray-300 ml-6' : 'border-primary-500'} pl-4 py-3 bg-gray-50 rounded-r-lg hover:bg-gray-100 transition-colors" 
                 data-comment-id="${commentData.id}" 
                 id="comment-${commentData.id}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold shadow-md">
                            ${(commentName.charAt(0) || 'U').toUpperCase()}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h5 class="font-semibold text-gray-900">${commentName}</h5>
                            <span class="text-xs text-gray-500">
                                <i class="far fa-clock mr-1"></i>Baru saja
                            </span>
                        </div>
                        <div class="comment-text mb-3">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">${commentText}</p>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <button onclick="toggleLike(${commentData.id}, true)" 
                                        class="flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-green-50 transition-all"
                                        id="like-btn-${commentData.id}">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span id="likes-count-${commentData.id}">${commentData.likes_count || 0}</span>
                                </button>
                                <button onclick="toggleLike(${commentData.id}, false)" 
                                        class="flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-red-50 transition-all"
                                        id="dislike-btn-${commentData.id}">
                                    <i class="fas fa-thumbs-down"></i>
                                    <span id="dislikes-count-${commentData.id}">${commentData.dislikes_count || 0}</span>
                                </button>
                            </div>
                            <button onclick="replyToComment(${commentData.id}, '${commentName.replace(/'/g, "\\'")}')" 
                                    class="text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1 transition-colors">
                                <i class="fas fa-reply"></i>
                                <span>Balas</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Jika ini adalah reply, tambahkan ke parent comment
        if (parentId) {
            const parentComment = document.getElementById(`comment-${parentId}`);
            if (parentComment) {
                // Cari atau buat container untuk replies
                let repliesContainer = parentComment.querySelector('.replies-container');
                if (!repliesContainer) {
                    repliesContainer = document.createElement('div');
                    repliesContainer.className = 'mt-4 space-y-3 replies-container';
                    const parentContent = parentComment.querySelector('.flex-1');
                    if (parentContent) {
                        parentContent.appendChild(repliesContainer);
                    }
                }
                repliesContainer.insertAdjacentHTML('beforeend', commentHTML);
            } else {
                // Jika parent tidak ditemukan, tambahkan ke list utama
                commentsList.insertAdjacentHTML('afterbegin', commentHTML);
            }
        } else {
            // Remove "no comments" message if exists
            const noCommentsMsg = commentsList.querySelector('.text-center');
            if (noCommentsMsg) {
                noCommentsMsg.remove();
            }
            
            // Add new comment at the top
            commentsList.insertAdjacentHTML('afterbegin', commentHTML);
            
            // Update count hanya untuk top-level comments
            if (commentsCount) {
                const currentCount = parseInt(commentsCount.textContent) || 0;
                commentsCount.textContent = currentCount + 1;
            }
        }
        
        // Scroll to new comment
        const newComment = document.getElementById(`comment-${commentData.id}`);
        if (newComment) {
            newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const colors = {
            success: 'bg-green-100 border-green-400 text-green-700',
            error: 'bg-red-100 border-red-400 text-red-700',
            info: 'bg-blue-100 border-blue-400 text-blue-700'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3 max-w-md animate-slide-in`;
        notification.innerHTML = `
            <i class="fas ${icons[type]} text-xl"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-slide-out');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Close modals on outside click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('bg-black')) {
            e.target.classList.add('hidden');
        }
    });
});
</script>

<style>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
    animation: slide-out 0.3s ease-out;
}
</style>
@endsection
@endsection
