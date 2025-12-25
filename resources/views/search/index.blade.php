@extends('layouts.public')

@section('title', 'Hasil Pencarian' . (!empty($query) ? ': ' . $query : '') . ' - Portal Berita Kabupaten Pesisir Barat')
@section('description', 'Hasil pencarian' . (!empty($query) ? ' untuk: ' . $query : '') . '. Temukan berita terkini dan terpercaya dari Kabupaten Pesisir Barat.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors flex items-center">
            <i class="fas fa-home mr-1"></i>
            <span>Beranda</span>
        </a>
        <i class="fas fa-chevron-right text-xs text-gray-400"></i>
        <span class="text-gray-800 font-medium">Hasil Pencarian</span>
    </nav>

    <!-- Search Header -->
    <div class="mb-8 bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl p-6 md:p-8 border border-primary-100">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-search text-primary-600 mr-3"></i>
                    <span>Hasil Pencarian</span>
                </h1>
                @if(!empty($query))
                <p class="text-gray-700 text-lg mb-2">
                    Menampilkan hasil untuk: <span class="font-bold text-primary-600 bg-primary-100 px-3 py-1 rounded-full">"{{ $query }}"</span>
                </p>
                @endif
                <div class="flex items-center space-x-4 mt-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-file-alt text-primary-500"></i>
                        <span class="font-semibold">{{ $articles->total() }}</span>
                        <span>artikel ditemukan</span>
                    </div>
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
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group">
                        <!-- Featured Image -->
                        @if($article->featured_image)
                        <div class="aspect-video bg-gray-200 relative overflow-hidden">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                 alt="{{ $article->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent"></div>
                            <!-- Category Badge on Image -->
                            <div class="absolute top-3 left-3 z-10">
                                <a href="{{ route('categories.show', $article->category) }}" 
                                   class="inline-block bg-white/90 backdrop-blur-sm text-primary-800 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-white transition-colors shadow-md">
                                    {{ $article->category->name }}
                                </a>
                            </div>
                            <!-- Type Badge -->
                            <div class="absolute top-3 right-3 z-10">
                                <span class="inline-block text-white text-xs font-semibold px-2.5 py-1 rounded-full shadow-md {{ $article->type === 'berita' ? 'bg-blue-600' : 'bg-green-600' }}">
                                    {{ ucfirst($article->type) }}
                                </span>
                            </div>
                        </div>
                        @else
                        <div class="aspect-video bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            <i class="fas fa-newspaper text-white text-4xl relative z-10"></i>
                            <!-- Category Badge on Image -->
                            <div class="absolute top-3 left-3 z-10">
                                <a href="{{ route('categories.show', $article->category) }}" 
                                   class="inline-block bg-white/90 backdrop-blur-sm text-primary-800 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-white transition-colors shadow-md">
                                    {{ $article->category->name }}
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Article Content -->
                        <div class="p-6">
                            <!-- Title -->
                            <h2 class="text-lg font-bold text-gray-900 leading-tight mb-3 line-clamp-2 min-h-[3.5rem]">
                                <a href="{{ route('articles.show', $article) }}" 
                                   class="hover:text-primary-600 transition-colors group-hover:text-primary-600">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            <!-- Excerpt -->
                            <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3 min-h-[4rem]">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>

                            <!-- Meta Information -->
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-100">
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
                                   class="text-primary-600 hover:text-primary-700 font-semibold flex items-center space-x-1 group/link">
                                    <span>Baca</span>
                                    <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    <div class="pagination-wrapper">
                        {{ $articles->appends(['q' => $query ?? ''])->links() }}
                    </div>
                </div>
            @else
                <!-- No Results Found -->
                <div class="bg-white rounded-xl shadow-lg p-12 md:p-16 text-center border border-gray-100">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                            <i class="fas fa-search text-4xl text-gray-400"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Tidak Ada Hasil Ditemukan</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Maaf, tidak ada artikel yang ditemukan
                        @if(!empty($query))
                            untuk pencarian <span class="font-bold text-primary-600">"{{ $query }}"</span>
                        @endif
                        .
                    </p>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-500">Coba dengan kata kunci lain atau:</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('articles.index') }}" 
                               class="inline-flex items-center justify-center space-x-2 bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <i class="fas fa-newspaper"></i>
                                <span>Lihat Semua Berita</span>
                            </a>
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center justify-center space-x-2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <i class="fas fa-home"></i>
                                <span>Kembali ke Beranda</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Search Box -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-search text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Cari Berita</h3>
                </div>
                <form action="{{ route('search.index') }}" method="GET" class="space-y-3">
                    <div class="relative">
                        <input type="text" 
                               name="q" 
                               placeholder="Masukkan kata kunci..." 
                               value="{{ $query ?? '' }}"
                               required
                               class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-search mr-2"></i>
                        Cari
                    </button>
                </form>
            </div>

            <!-- Categories -->
            @if($categories->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-folder text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Kategori Berita</h3>
                </div>
                <ul class="space-y-2">
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200 py-2.5 px-3 rounded-lg group">
                            <span class="font-medium group-hover:font-semibold">{{ $category->name }}</span>
                            <span class="text-xs bg-gray-100 group-hover:bg-primary-100 text-gray-600 group-hover:text-primary-700 px-2.5 py-1 rounded-full font-semibold transition-colors">
                                {{ $category->publishedBeritaArticles()->count() }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Popular Articles -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-fire text-orange-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Berita Populer</h3>
                </div>
                <div class="space-y-4">
                    @php
                        $popularArticles = \App\Models\Article::published()
                            ->popular()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($popularArticles->count() > 0)
                        @foreach($popularArticles as $index => $popularArticle)
                        <article class="flex space-x-3 group hover:bg-gray-50 p-2 rounded-lg transition-all duration-200">
                            @if($popularArticle->featured_image)
                            <div class="flex-shrink-0 relative overflow-hidden rounded-lg">
                                <img src="{{ asset('storage/' . $popularArticle->featured_image) }}" 
                                     alt="{{ $popularArticle->title }}" 
                                     class="w-20 h-20 object-cover group-hover:scale-110 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            </div>
                            @else
                            <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex items-center justify-center shadow-md border border-primary-200">
                                <i class="fas fa-newspaper text-primary-600 text-sm"></i>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 leading-tight mb-1.5 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                    <a href="{{ route('articles.show', $popularArticle) }}">
                                        {{ Str::limit($popularArticle->title, 60) }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar text-primary-500"></i>
                                        <span>{{ $popularArticle->published_at ? $popularArticle->published_at->format('d M') : 'N/A' }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-eye text-primary-500"></i>
                                        <span>{{ number_format($popularArticle->views) }}</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-newspaper text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada berita populer</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Berlangganan Newsletter</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">
                    Dapatkan berita terbaru langsung di email Anda
                </p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               placeholder="Masukkan email Anda" 
                               required
                               class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Berlangganan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
