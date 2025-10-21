@extends('layouts.public')

@section('title', 'Berita Terkini - Portal Berita Kabupaten Pesisir Barat')
@section('description', 'Baca berita terkini dan terpercaya dari Kabupaten Pesisir Barat. Informasi terbaru tentang pemerintahan, pembangunan, dan kehidupan masyarakat.')

@section('content')
<div class="container-responsive py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg p-8 text-white">
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-newspaper text-2xl"></i>
                </div>
                <div>
                    <h1 class="heading-responsive font-bold">Berita Terkini</h1>
                    <p class="text-primary-100 text-responsive">Informasi terbaru dan terpercaya dari Kabupaten Pesisir Barat</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($articles->count() > 0)
                <!-- Articles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($articles as $article)
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Featured Image -->
                        @if($article->featured_image)
                        <div class="aspect-video bg-gray-200 relative overflow-hidden">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                 alt="{{ $article->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>
                        @else
                        <div class="aspect-video bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            <i class="fas fa-newspaper text-white text-4xl relative z-10"></i>
                        </div>
                        @endif

                        <!-- Article Content -->
                        <div class="p-6">
                            <!-- Category Badge -->
                            <div class="mb-3">
                                <a href="{{ route('categories.show', $article->category) }}" 
                                   class="inline-block bg-primary-100 text-primary-800 text-xs font-semibold px-3 py-1 rounded-full hover:bg-primary-200 transition-colors">
                                    {{ $article->category->name }}
                                </a>
                            </div>

                            <!-- Title -->
                            <h2 class="text-lg font-bold text-gray-900 leading-tight mb-3 line-clamp-2">
                                <a href="{{ route('articles.show', $article) }}" 
                                   class="hover:text-primary-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            <!-- Excerpt -->
                            <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>

                            <!-- Meta Information -->
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar text-primary-500"></i>
                                        <span>{{ $article->published_at ? $article->published_at->format('d M Y') : 'Belum dipublikasi' }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye text-primary-500"></i>
                                        <span>{{ number_format($article->views) }}</span>
                                    </span>
                                </div>
                                <a href="{{ route('articles.show', $article) }}" 
                                   class="text-primary-600 hover:text-primary-700 font-semibold flex items-center space-x-1 group">
                                    <span>Baca</span>
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
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
                    <p class="text-gray-500">Saat ini belum ada berita yang tersedia. Silakan kembali lagi nanti.</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Categories -->
            @if($categories->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="bg-primary-100 p-2 rounded-lg">
                        <i class="fas fa-tags text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Kategori Berita</h3>
                </div>
                <ul class="space-y-2">
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-primary-600 transition-colors py-3 px-3 rounded-lg hover:bg-gray-50">
                            <span class="font-medium">{{ $category->name }}</span>
                            <span class="text-xs bg-primary-100 text-primary-600 px-2 py-1 rounded-full font-semibold">
                                {{ $category->publishedBeritaArticles()->count() }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Popular Articles -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="bg-primary-100 p-2 rounded-lg">
                        <i class="fas fa-fire text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Berita Populer</h3>
                </div>
                <div class="space-y-4">
                    @php
                        $popularArticles = \App\Models\Article::published()
                            ->berita()
                            ->popular()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($popularArticles->count() > 0)
                        @foreach($popularArticles as $index => $popularArticle)
                        <article class="flex space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 bg-primary-100 text-primary-600 rounded-full text-sm font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 leading-tight mb-1 line-clamp-2">
                                    <a href="{{ route('articles.show', $popularArticle) }}" 
                                       class="hover:text-primary-600 transition-colors">
                                        {{ $popularArticle->title }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye text-primary-500"></i>
                                        <span>{{ number_format($popularArticle->views) }}</span>
                                    </span>
                                    <span>•</span>
                                    <span>{{ $popularArticle->published_at ? $popularArticle->published_at->format('d M Y') : 'Belum dipublikasi' }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-newspaper text-gray-300 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada berita populer</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center space-x-2 mb-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <i class="fas fa-envelope text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold">Berlangganan Newsletter</h3>
                </div>
                <p class="text-primary-100 text-sm mb-4">
                    Dapatkan berita terbaru langsung di email Anda
                </p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Masukkan email Anda" 
                           required
                           class="w-full px-3 py-2 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                    <button type="submit" 
                            class="w-full bg-white text-primary-600 font-semibold py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm">
                        Berlangganan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
