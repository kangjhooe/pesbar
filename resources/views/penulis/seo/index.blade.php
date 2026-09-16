@extends('layouts.penulis')

@section('title', 'SEO Tools')
@section('page-title', 'SEO Tools')
@section('page-subtitle', 'Analisis dan optimasi SEO artikel Anda')

@section('content')
<div>
 <!-- Header -->
 <div class="mb-6">
 <div class="flex items-center justify-between flex-wrap gap-4">
 <div>
 <h1 class="text-3xl font-bold text-news-ink">SEO Tools</h1>
 <p class="text-news-muted mt-1">Analisis dan optimasi SEO untuk artikel Anda</p>
 </div>
 <div class="flex gap-2">
 <a href="{{ route('penulis.dashboard') }}" class="px-4 py-2 border border-news-line text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
 <i class="fas fa-arrow-left mr-2"></i>Kembali
 </a>
 </div>
 </div>
 </div>

 <!-- Article Selector -->
 <div class="bg-white border border-news-line p-6 mb-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Pilih Artikel untuk Dianalisis</h3>
 <form method="GET" action="{{ route('penulis.seo.index') }}" class="flex gap-4">
 <select name="article_id" onchange="this.form.submit()" class="flex-1 px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 <option value="">-- Pilih Artikel --</option>
 @foreach($articles as $article)
 <option value="{{ $article->id }}" {{ request('article_id') == $article->id ? 'selected' : '' }}>
 {{ $article->title }}
 </option>
 @endforeach
 </select>
 @if(request('article_id'))
 <a href="{{ route('penulis.seo.analyze', request('article_id')) }}" class="px-6 py-2 bg-news-ink hover:bg-news-accent text-white rounded-lg transition">
 <i class="fas fa-search mr-2"></i>Analisis SEO
 </a>
 @endif
 </form>
 </div>

 <!-- Info Cards -->
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-accent">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Meta Tags</p>
 <p class="text-lg font-semibold text-news-ink">Optimasi</p>
 </div>
 <i class="fas fa-tags text-news-accent text-3xl"></i>
 </div>
 <p class="text-xs text-news-muted mt-2">Pastikan meta title dan description sudah optimal</p>
 </div>
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Keyword Density</p>
 <p class="text-lg font-semibold text-news-ink">Analisis</p>
 </div>
 <i class="fas fa-key text-news-accent text-3xl"></i>
 </div>
 <p class="text-xs text-news-muted mt-2">Cek kepadatan kata kunci dalam konten</p>
 </div>
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Readability</p>
 <p class="text-lg font-semibold text-news-ink">Score</p>
 </div>
 <i class="fas fa-book-reader text-news-accent text-3xl"></i>
 </div>
 <p class="text-xs text-news-muted mt-2">Tingkat keterbacaan konten Anda</p>
 </div>
 </div>

 <!-- Tips -->
 <div class="bg-news-paper border-l-4 border-news-accent rounded-xl p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-3">
 <i class="fas fa-lightbulb mr-2"></i>Tips SEO
 </h3>
 <ul class="space-y-2 text-news-ink">
 <li class="flex items-start">
 <i class="fas fa-check-circle mr-2 mt-1 text-news-accent"></i>
 <span>Gunakan meta title 30-60 karakter untuk hasil terbaik</span>
 </li>
 <li class="flex items-start">
 <i class="fas fa-check-circle mr-2 mt-1 text-news-accent"></i>
 <span>Meta description ideal antara 120-160 karakter</span>
 </li>
 <li class="flex items-start">
 <i class="fas fa-check-circle mr-2 mt-1 text-news-accent"></i>
 <span>Gunakan heading H1 dan H2 untuk struktur konten yang baik</span>
 </li>
 <li class="flex items-start">
 <i class="fas fa-check-circle mr-2 mt-1 text-news-accent"></i>
 <span>Konten minimal 300 kata untuk SEO yang optimal</span>
 </li>
 <li class="flex items-start">
 <i class="fas fa-check-circle mr-2 mt-1 text-news-accent"></i>
 <span>Tambahkan gambar featured untuk meningkatkan engagement</span>
 </li>
 </ul>
 </div>
</div>
@endsection

