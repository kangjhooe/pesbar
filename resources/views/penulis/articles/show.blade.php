@extends('layouts.penulis')

@section('title', 'Detail Artikel')
@section('page-title', 'Detail Artikel')
@section('page-subtitle', 'Lihat detail dan kelola artikel Anda')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail Artikel</h1>
                <p class="text-gray-600">Lihat detail dan statistik artikel Anda</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('penulis.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Kembali
                </a>
                <a href="{{ route('penulis.articles.edit', $article) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Edit Artikel
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Article Header -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $article->title }}</h2>
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $article->category->name ?? 'Tidak ada kategori' }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $article->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @if($article->status === 'published')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Terbit
                            </span>
                        @elseif($article->status === 'pending_review')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Menunggu Review
                            </span>
                        @elseif($article->status === 'rejected')
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Ditolak
                                </span>
                                @if($article->rejection_reason)
                                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-sm font-semibold text-red-900 mb-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Alasan Penolakan:
                                        </p>
                                        <p class="text-sm text-red-700">{{ $article->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @elseif($article->status === 'draft')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                Draft
                            </span>
                        @endif
                    </div>
                </div>

                @if($article->featured_image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                @if($article->tags->count() > 0)
                    <div class="mb-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="prose max-w-none">
                    {!! $article->content !!}
                </div>

                @if($article->status === 'published')
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('articles.show', $article) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                            Lihat di Website →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Komentar</h3>
                    <a href="{{ route('penulis.articles.comments', $article) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Kelola Semua Komentar →
                    </a>
                </div>
                
                @if($article->comments->count() > 0)
                    <div class="space-y-4">
                        @foreach($article->comments->take(5) as $comment)
                            <div class="border-l-4 {{ $comment->is_approved ? 'border-green-500' : 'border-yellow-500' }} pl-4 py-2">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $comment->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $comment->email }}</p>
                                        <p class="text-gray-700 mt-2">{{ $comment->comment }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <div>
                                        @if($comment->is_approved)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Menunggu
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($article->comments->count() > 5)
                            <p class="text-sm text-gray-600 text-center">
                                Menampilkan 5 dari {{ $article->comments->count() }} komentar
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada komentar</p>
                @endif
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Views</span>
                        <span class="text-lg font-semibold text-gray-900">{{ number_format($article->views) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Komentar</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $article->comments->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Komentar Disetujui</span>
                        <span class="text-lg font-semibold text-green-600">{{ $article->comments->where('is_approved', true)->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Dibuat</span>
                        <span class="text-sm text-gray-900">{{ $article->created_at->format('d M Y') }}</span>
                    </div>
                    @if($article->published_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Diterbitkan</span>
                            <span class="text-sm text-gray-900">{{ $article->published_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('penulis.articles.edit', $article) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center font-medium transition-colors">
                        Edit Artikel
                    </a>
                    <a href="{{ route('penulis.articles.comments', $article) }}" class="block w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-center font-medium transition-colors">
                        Kelola Komentar
                    </a>
                    @if($article->status === 'published')
                        <a href="{{ route('articles.show', $article) }}" target="_blank" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center font-medium transition-colors">
                            Lihat di Website
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

