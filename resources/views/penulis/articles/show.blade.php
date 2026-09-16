@extends('layouts.penulis')

@section('title', 'Detail Artikel')
@section('page-title', 'Detail Artikel')
@section('page-subtitle', 'Lihat detail dan kelola artikel Anda')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-news-ink mb-2">Detail Artikel</h1>
 <p class="text-news-muted">Lihat detail dan statistik artikel Anda</p>
 </div>
 <div class="flex gap-2">
 <a href="{{ route('penulis.articles.index') }}" class="bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg font-medium transition-colors">
 Kembali
 </a>
 <a href="{{ route('penulis.articles.edit', $article) }}" class="bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg font-medium transition-colors">
 Edit Artikel
 </a>
 </div>
 </div>
 </div>

 @if(session('success'))
 <div class="bg-news-paper border border-news-line text-news-ink px-4 py-3 rounded mb-6">
 {{ session('success') }}
 </div>
 @endif

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 <!-- Main Content -->
 <div class="lg:col-span-2 space-y-6">
 <!-- Article Header -->
 <div class="bg-white border border-news-line p-6">
 <div class="flex items-start justify-between mb-4">
 <div class="flex-1">
 <h2 class="text-2xl font-bold text-news-ink mb-2">{{ $article->title }}</h2>
 <div class="flex items-center gap-4 text-sm text-news-muted mb-4">
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
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-news-paper text-news-ink">
 Terbit
 </span>
 @elseif($article->status === 'pending_review')
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-news-paper text-news-ink">
 Menunggu Review
 </span>
 @elseif($article->status === 'rejected')
 <div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-news-ink">
 <i class="fas fa-times-circle mr-1"></i>
 Ditolak
 </span>
 @if($article->rejection_reason)
 <div class="mt-3 p-3 bg-red-50 border border-news-line rounded-lg">
 <p class="text-sm font-semibold text-news-ink mb-1">
 <i class="fas fa-exclamation-triangle mr-1"></i>
 Alasan Penolakan:
 </p>
 <p class="text-sm text-news-accent">{{ $article->rejection_reason }}</p>
 </div>
 @endif
 </div>
 @elseif($article->status === 'draft')
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-news-paper text-news-ink">
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
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
 {{ $tag->name }}
 </span>
 @endforeach
 </div>
 </div>
 @endif

 <div class="prose max-w-none article-content">
 {!! $article->content !!}
 </div>
 
 <!-- Article Content List Styling -->
 <style>
 /* Enhanced list styling for article content */
 .article-content ul,
 .article-content ol,
 .prose ul,
 .prose ol {
 margin-top: 1.25em !important;
 margin-bottom: 1.25em !important;
 padding-left: 2em !important;
 list-style-position: outside !important;
 }
 
 .article-content ul,
 .prose ul {
 list-style-type: disc !important;
 }
 
 .article-content ol,
 .prose ol {
 list-style-type: decimal !important;
 }
 
 .article-content li,
 .prose li {
 margin-top: 0.75em !important;
 margin-bottom: 0.75em !important;
 padding-left: 0.5em !important;
 line-height: 1.8 !important;
 display: list-item !important;
 }
 
 .article-content ul > li,
 .prose ul > li {
 list-style-type: disc !important;
 }
 
 .article-content ol > li,
 .prose ol > li {
 list-style-type: decimal !important;
 }
 
 /* Nested lists */
 .article-content ul ul,
 .prose ul ul {
 list-style-type: circle !important;
 margin-top: 0.5em !important;
 margin-bottom: 0.5em !important;
 }
 
 .article-content ul ul ul,
 .prose ul ul ul {
 list-style-type: square !important;
 }
 
 .article-content ol ol,
 .prose ol ol {
 list-style-type: lower-alpha !important;
 }
 
 .article-content ol ol ol,
 .prose ol ol ol {
 list-style-type: lower-roman !important;
 }
 
 /* Ensure list markers are visible */
 .article-content ul li::marker,
 .prose ul li::marker {
 color: #b91c1c !important;
 font-size: 1.2em !important;
 font-weight: normal !important;
 }
 
 .article-content ol li::marker,
 .prose ol li::marker {
 color: #b91c1c !important;
 font-weight: 600 !important;
 }
 
 /* Fix for Quill editor output */
 .article-content ul[class*="ql-"],
 .prose ul[class*="ql-"] {
 list-style-type: disc !important;
 padding-left: 2em !important;
 }
 
 .article-content ol[class*="ql-"],
 .prose ol[class*="ql-"] {
 list-style-type: decimal !important;
 padding-left: 2em !important;
 }
 </style>

 @if($article->status === 'published')
 <div class="mt-6 pt-6 border-t border-news-line">
 <a href="{{ $article->publicUrl() }}" target="_blank" class="text-news-accent hover:text-news-accent font-medium">
 Lihat di Website →
 </a>
 </div>
 @endif
 </div>

 <!-- Comments Section -->
 <div class="bg-white border border-news-line p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-lg font-semibold text-news-ink">Komentar</h3>
 <a href="{{ route('penulis.articles.comments', $article) }}" class="text-news-accent hover:text-news-accent text-sm font-medium">
 Kelola Semua Komentar →
 </a>
 </div>
 
 @if($article->comments->count() > 0)
 <div class="space-y-4">
 @foreach($article->comments->take(5) as $comment)
 <div class="border-l-4 {{ $comment->is_approved ? 'border-news-ink' : 'border-news-ink' }} pl-4 py-2">
 <div class="flex items-start justify-between">
 <div class="flex-1">
 <p class="font-medium text-news-ink">{{ $comment->name }}</p>
 <p class="text-sm text-news-muted">{{ $comment->email }}</p>
 <p class="text-news-ink mt-2">{{ $comment->comment }}</p>
 <p class="text-xs text-news-muted mt-1">{{ $comment->created_at->format('d M Y, H:i') }}</p>
 </div>
 <div>
 @if($comment->is_approved)
 <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-news-paper text-news-ink">
 Disetujui
 </span>
 @else
 <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-news-paper text-news-ink">
 Menunggu
 </span>
 @endif
 </div>
 </div>
 </div>
 @endforeach
 
 @if($article->comments->count() > 5)
 <p class="text-sm text-news-muted text-center">
 Menampilkan 5 dari {{ $article->comments->count() }} komentar
 </p>
 @endif
 </div>
 @else
 <p class="text-news-muted text-center py-4">Belum ada komentar</p>
 @endif
 </div>
 </div>

 <!-- Sidebar Stats -->
 <div class="space-y-6">
 <!-- Statistics -->
 <div class="bg-white border border-news-line p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Statistik</h3>
 <div class="space-y-4">
 <div class="flex items-center justify-between">
 <span class="text-sm text-news-muted">Total Views</span>
 <span class="text-lg font-semibold text-news-ink">{{ number_format($article->views) }}</span>
 </div>
 <div class="flex items-center justify-between">
 <span class="text-sm text-news-muted">Total Komentar</span>
 <span class="text-lg font-semibold text-news-ink">{{ $article->comments->count() }}</span>
 </div>
 <div class="flex items-center justify-between">
 <span class="text-sm text-news-muted">Komentar Disetujui</span>
 <span class="text-lg font-semibold text-news-accent">{{ $article->comments->where('is_approved', true)->count() }}</span>
 </div>
 <div class="flex items-center justify-between">
 <span class="text-sm text-news-muted">Dibuat</span>
 <span class="text-sm text-news-ink">{{ $article->created_at->format('d M Y') }}</span>
 </div>
 @if($article->published_at)
 <div class="flex items-center justify-between">
 <span class="text-sm text-news-muted">Diterbitkan</span>
 <span class="text-sm text-news-ink">{{ $article->published_at->format('d M Y') }}</span>
 </div>
 @endif
 </div>
 </div>

 <!-- Quick Actions -->
 <div class="bg-white border border-news-line p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Aksi Cepat</h3>
 <div class="space-y-2">
 <a href="{{ route('penulis.articles.edit', $article) }}" class="block w-full bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg text-center font-medium transition-colors">
 Edit Artikel
 </a>
 <a href="{{ route('penulis.articles.comments', $article) }}" class="block w-full bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg text-center font-medium transition-colors">
 Kelola Komentar
 </a>
 @if($article->status === 'published')
 <a href="{{ $article->publicUrl() }}" target="_blank" class="block w-full bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg text-center font-medium transition-colors">
 Lihat di Website
 </a>
 @endif
 </div>
 </div>
 </div>
 </div>
</div>
@endsection

