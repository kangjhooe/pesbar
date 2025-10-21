@extends('layouts.admin-simple')

@section('title', 'Detail Artikel - Admin Panel')
@section('page-title', 'Detail Artikel')
@section('page-subtitle', $article->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Article Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $article->title }}</h1>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                        <span class="flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            {{ $article->user->name ?? 'Sistem' }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $article->created_at->format('d M Y, H:i') }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-eye mr-1"></i>
                            {{ number_format($article->views) }} views
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @if($article->is_featured)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-star mr-1"></i>Featured
                        </span>
                    @endif
                    @if($article->is_breaking)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-bolt mr-1"></i>Breaking
                        </span>
                    @endif
                    @if($article->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-edit mr-1"></i>Draft
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Article Content -->
        <div class="p-6">
            <!-- Featured Image -->
            @if($article->featured_image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $article->featured_image) }}" 
                     alt="{{ $article->title }}" 
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif

            <!-- Article Meta -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2">Kategori</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $article->category->name ?? 'Tidak ada kategori' }}
                    </span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2">Tipe</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        {{ ucfirst($article->type) }}
                    </span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2">Tanggal Publikasi</h3>
                    <p class="text-sm text-gray-600">
                        {{ $article->published_at ? $article->published_at->format('d M Y, H:i') : 'Belum dipublikasi' }}
                    </p>
                </div>
            </div>

            <!-- Tags -->
            @if($article->tags->count() > 0)
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Excerpt -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-2">Ringkasan</h3>
                <p class="text-gray-700 leading-relaxed">{{ $article->excerpt }}</p>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-2">Konten</h3>
                <div class="prose max-w-none">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>

            <!-- Comments -->
            @if($article->comments->count() > 0)
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Komentar ({{ $article->comments->count() }})</h3>
                <div class="space-y-4">
                    @foreach($article->comments->take(5) as $comment)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $comment->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $comment->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            @if($comment->is_approved)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-700">{{ $comment->content }}</p>
                    </div>
                    @endforeach
                    @if($article->comments->count() > 5)
                        <p class="text-sm text-gray-600 text-center">
                            Dan {{ $article->comments->count() - 5 }} komentar lainnya...
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <form action="{{ route('admin.articles.toggle-featured', $article) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $article->is_featured ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                            <i class="fas fa-star mr-1"></i>
                            {{ $article->is_featured ? 'Hapus Featured' : 'Tandai Featured' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $article->is_breaking ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                            <i class="fas fa-bolt mr-1"></i>
                            {{ $article->is_breaking ? 'Hapus Breaking' : 'Tandai Breaking' }}
                        </button>
                    </form>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.articles.edit', $article) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <a href="{{ route('articles.show', $article) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors" target="_blank">
                        <i class="fas fa-external-link-alt mr-1"></i>Lihat
                    </a>
                    <a href="{{ route('admin.articles.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
