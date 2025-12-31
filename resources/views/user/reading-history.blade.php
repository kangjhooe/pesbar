@extends('layouts.user')

@section('title', 'Riwayat Membaca')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Riwayat Membaca</h1>
        <p class="text-gray-600">Artikel yang telah Anda baca</p>
    </div>

    @if($history->count() > 0)
        <!-- Reading History Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($history as $item)
            <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <!-- Featured Image -->
                @if($item->article->featured_image)
                <div class="aspect-video bg-gray-200 relative overflow-hidden">
                    <img src="{{ asset('storage/' . $item->article->featured_image) }}" 
                         alt="{{ $item->article->title }}" 
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
                    <div class="mb-3">
                        @if($item->article->category)
                        <a href="{{ route('categories.show', $item->article->category) }}" 
                           class="inline-block bg-primary-100 text-primary-800 text-xs font-semibold px-3 py-1 rounded-full hover:bg-primary-200 transition-colors">
                            {{ $item->article->category->name }}
                        </a>
                        @endif
                    </div>

                    <!-- Title -->
                    <h2 class="text-lg font-bold text-gray-900 leading-tight mb-3 line-clamp-2">
                        <a href="{{ route('articles.show', $item->article) }}" 
                           class="hover:text-primary-600 transition-colors">
                            {{ $item->article->title }}
                        </a>
                    </h2>

                    <!-- Excerpt -->
                    <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                        {{ Str::limit(strip_tags($item->article->excerpt ?? $item->article->content), 120) }}
                    </p>

                    <!-- Meta Information -->
                    <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
                        <div class="flex items-center space-x-3">
                            @if($item->article->author)
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-user text-primary-500"></i>
                                <span>{{ $item->article->author->name }}</span>
                            </span>
                            @endif
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-eye text-primary-500"></i>
                                <span>{{ number_format($item->article->views) }}</span>
                            </span>
                        </div>
                        <a href="{{ route('articles.show', $item->article) }}" 
                           class="text-primary-600 hover:text-primary-700 font-semibold flex items-center space-x-1 group">
                            <span>Baca Lagi</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <!-- Read Date -->
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">
                            <i class="fas fa-clock text-primary-500"></i>
                            Dibaca pada {{ $item->read_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $history->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="mb-4">
                <i class="fas fa-history text-gray-300 text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Riwayat Membaca</h3>
            <p class="text-gray-600 mb-6">Mulai membaca artikel untuk melihat riwayat di sini</p>
            <a href="{{ route('home') }}" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    @endif
</div>
@endsection

