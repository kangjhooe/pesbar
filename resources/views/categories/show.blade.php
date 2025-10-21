@extends('layouts.public')

@section('title', $category->name . ' - Portal Berita Kabupaten Pesisir Barat')
@section('description', 'Baca berita terkini dari kategori ' . $category->name . '. Informasi terbaru dan terpercaya dari Kabupaten Pesisir Barat.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('articles.index') }}" class="hover:text-primary-600 transition-colors">Berita</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-800">{{ $category->name }}</span>
    </nav>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            @if($category->icon)
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <i class="{{ $category->icon }} text-primary-600 text-xl"></i>
            </div>
            @endif
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $category->name }}</h1>
                @if($category->description)
                <p class="text-gray-600 text-lg mt-2">{{ $category->description }}</p>
                @endif
            </div>
        </div>
        <p class="text-gray-500">{{ $articles->total() }} artikel ditemukan</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($articles->count() > 0)
                <!-- Articles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @foreach($articles as $article)
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <!-- Featured Image -->
                        @if($article->featured_image)
                        <div class="aspect-video bg-gray-200">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                 alt="{{ $article->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="aspect-video bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-4xl"></i>
                        </div>
                        @endif

                        <!-- Article Content -->
                        <div class="p-6">
                            <!-- Category Badge -->
                            <div class="mb-3">
                                <span class="inline-block bg-primary-100 text-primary-800 text-xs font-medium px-2 py-1 rounded-full">
                                    {{ $article->category->name }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h2 class="text-xl font-bold text-gray-900 leading-tight mb-3">
                                <a href="{{ route('articles.show', $article) }}" 
                                   class="hover:text-primary-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            <!-- Excerpt -->
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>

                            <!-- Meta Information -->
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $article->published_at ? $article->published_at->format('d-m-Y') : 'Belum dipublikasi' }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye"></i>
                                        <span>{{ number_format($article->views) }}</span>
                                    </span>
                                </div>
                                <a href="{{ route('articles.show', $article) }}" 
                                   class="text-primary-600 hover:text-primary-700 font-medium">
                                    Baca Selengkapnya →
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $articles->links() }}
                </div>
            @else
                <!-- No Articles Found -->
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-newspaper text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Berita</h3>
                    <p class="text-gray-500">Saat ini belum ada berita dalam kategori {{ $category->name }}. Silakan kembali lagi nanti.</p>
                    <div class="mt-6">
                        <a href="{{ route('articles.index') }}" 
                           class="inline-flex items-center space-x-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            <span>Lihat Semua Berita</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- All Categories -->
            @php
                $allCategories = \App\Models\Category::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            @endphp
            
            @if($allCategories->count() > 0)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Semua Kategori</h3>
                <ul class="space-y-2">
                    @foreach($allCategories as $cat)
                    <li>
                        <a href="{{ route('categories.show', $cat) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-primary-600 transition-colors py-2 {{ $cat->id === $category->id ? 'text-primary-600 font-semibold' : '' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                {{ $cat->publishedBeritaArticles()->count() }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Popular Articles -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Berita Populer</h3>
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

            <!-- Newsletter Subscription -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-lg font-bold mb-2">Berlangganan Newsletter</h3>
                <p class="text-primary-100 text-sm mb-4">
                    Dapatkan berita terbaru langsung di email Anda
                </p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Masukkan email Anda" 
                           required
                           class="w-full px-3 py-2 rounded text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" 
                            class="w-full bg-white text-primary-600 font-semibold py-2 rounded hover:bg-gray-100 transition-colors text-sm">
                        Berlangganan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
