@extends('layouts.penulis')

@section('title', 'SEO Tools')
@section('page-title', 'SEO Tools')
@section('page-subtitle', 'Analisis dan optimasi SEO artikel Anda')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">SEO Tools</h1>
                <p class="text-gray-600 mt-1">Analisis dan optimasi SEO untuk artikel Anda</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('penulis.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Article Selector -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Artikel untuk Dianalisis</h3>
        <form method="GET" action="{{ route('penulis.seo.index') }}" class="flex gap-4">
            <select name="article_id" onchange="this.form.submit()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih Artikel --</option>
                @foreach($articles as $article)
                <option value="{{ $article->id }}" {{ request('article_id') == $article->id ? 'selected' : '' }}>
                    {{ $article->title }}
                </option>
                @endforeach
            </select>
            @if(request('article_id'))
            <a href="{{ route('penulis.seo.analyze', request('article_id')) }}" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Analisis SEO
            </a>
            @endif
        </form>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Meta Tags</p>
                    <p class="text-lg font-semibold text-gray-900">Optimasi</p>
                </div>
                <i class="fas fa-tags text-blue-600 text-3xl"></i>
            </div>
            <p class="text-xs text-gray-500 mt-2">Pastikan meta title dan description sudah optimal</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Keyword Density</p>
                    <p class="text-lg font-semibold text-gray-900">Analisis</p>
                </div>
                <i class="fas fa-key text-green-600 text-3xl"></i>
            </div>
            <p class="text-xs text-gray-500 mt-2">Cek kepadatan kata kunci dalam konten</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Readability</p>
                    <p class="text-lg font-semibold text-gray-900">Score</p>
                </div>
                <i class="fas fa-book-reader text-purple-600 text-3xl"></i>
            </div>
            <p class="text-xs text-gray-500 mt-2">Tingkat keterbacaan konten Anda</p>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">
            <i class="fas fa-lightbulb mr-2"></i>Tips SEO
        </h3>
        <ul class="space-y-2 text-blue-800">
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-1 text-blue-600"></i>
                <span>Gunakan meta title 30-60 karakter untuk hasil terbaik</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-1 text-blue-600"></i>
                <span>Meta description ideal antara 120-160 karakter</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-1 text-blue-600"></i>
                <span>Gunakan heading H1 dan H2 untuk struktur konten yang baik</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-1 text-blue-600"></i>
                <span>Konten minimal 300 kata untuk SEO yang optimal</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-1 text-blue-600"></i>
                <span>Tambahkan gambar featured untuk meningkatkan engagement</span>
            </li>
        </ul>
    </div>
</div>
@endsection

