@extends('layouts.penulis')

@section('title', 'Analisis SEO - ' . $article->title)
@section('page-title', 'Analisis SEO')
@section('page-subtitle', 'Detail analisis SEO artikel')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Analisis SEO</h1>
                <p class="text-gray-600 mt-1">{{ Str::limit($article->title, 60) }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('penulis.seo.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('penulis.articles.edit', $article) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>Edit Artikel
                </a>
            </div>
        </div>
    </div>

    <!-- SEO Score -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">SEO Score</h3>
            <div class="text-right">
                <div class="text-4xl font-bold {{ $seoAnalysis['score'] >= 70 ? 'text-green-600' : ($seoAnalysis['score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $seoAnalysis['score'] }}/{{ $seoAnalysis['max_score'] }}
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    @if($seoAnalysis['score'] >= 70)
                    <span class="text-green-600">Sangat Baik</span>
                    @elseif($seoAnalysis['score'] >= 50)
                    <span class="text-yellow-600">Cukup Baik</span>
                    @else
                    <span class="text-red-600">Perlu Perbaikan</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="h-4 rounded-full {{ $seoAnalysis['score'] >= 70 ? 'bg-green-600' : ($seoAnalysis['score'] >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ ($seoAnalysis['score'] / $seoAnalysis['max_score']) * 100 }}%"></div>
        </div>
    </div>

    <!-- Analysis Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Meta Title -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Meta Title</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['meta_title']['optimal'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $seoAnalysis['meta_title']['score'] }}/15
                </span>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Judul:</p>
                <p class="text-gray-900 font-medium">{{ $seoAnalysis['meta_title']['value'] }}</p>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Panjang: {{ $seoAnalysis['meta_title']['length'] }} karakter</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $seoAnalysis['meta_title']['optimal'] ? 'bg-green-600' : ($seoAnalysis['meta_title']['length'] > 0 ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ min(100, ($seoAnalysis['meta_title']['length'] / 60) * 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Optimal: 30-60 karakter</p>
            </div>
            @if(!$seoAnalysis['meta_title']['optimal'])
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    @if($seoAnalysis['meta_title']['length'] < 30)
                    Judul terlalu pendek. Tambahkan lebih banyak informasi.
                    @elseif($seoAnalysis['meta_title']['length'] > 60)
                    Judul terlalu panjang. Pertimbangkan untuk mempersingkat.
                    @endif
                </p>
            </div>
            @endif
        </div>

        <!-- Meta Description -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Meta Description</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['meta_description']['optimal'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $seoAnalysis['meta_description']['score'] }}/15
                </span>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Deskripsi:</p>
                <p class="text-gray-900">{{ Str::limit($seoAnalysis['meta_description']['value'], 160) }}</p>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Panjang: {{ $seoAnalysis['meta_description']['length'] }} karakter</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $seoAnalysis['meta_description']['optimal'] ? 'bg-green-600' : ($seoAnalysis['meta_description']['length'] > 0 ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ min(100, ($seoAnalysis['meta_description']['length'] / 160) * 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Optimal: 120-160 karakter</p>
            </div>
            @if(!$seoAnalysis['meta_description']['optimal'])
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    @if($seoAnalysis['meta_description']['length'] < 120)
                    Deskripsi terlalu pendek. Tambahkan lebih banyak informasi.
                    @elseif($seoAnalysis['meta_description']['length'] > 160)
                    Deskripsi terlalu panjang dan mungkin terpotong di hasil pencarian.
                    @endif
                </p>
            </div>
            @endif
        </div>

        <!-- Meta Keywords -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Meta Keywords</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['meta_keywords']['has_keywords'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $seoAnalysis['meta_keywords']['score'] }}/10
                </span>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Keywords:</p>
                <p class="text-gray-900">{{ $seoAnalysis['meta_keywords']['value'] ?: 'Belum diisi' }}</p>
            </div>
            @if(!$seoAnalysis['meta_keywords']['has_keywords'])
            <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded">
                <p class="text-sm text-red-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Tambahkan kata kunci untuk meningkatkan SEO.
                </p>
            </div>
            @endif
        </div>

        <!-- Slug -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">URL Slug</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['slug']['optimal'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $seoAnalysis['slug']['score'] }}/10
                </span>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Slug:</p>
                <p class="text-gray-900 font-mono text-sm">{{ $seoAnalysis['slug']['value'] }}</p>
            </div>
            @if(!$seoAnalysis['slug']['optimal'])
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Pastikan slug hanya menggunakan huruf kecil, angka, dan tanda hubung.
                </p>
            </div>
            @endif
        </div>

        <!-- Featured Image -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Featured Image</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['featured_image']['has_image'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $seoAnalysis['featured_image']['score'] }}/10
                </span>
            </div>
            @if($seoAnalysis['featured_image']['has_image'])
            <div class="mb-2">
                <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover rounded-lg">
            </div>
            @else
            <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded">
                <p class="text-sm text-red-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Tambahkan featured image untuk meningkatkan engagement dan SEO.
                </p>
            </div>
            @endif
        </div>

        <!-- Content -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Konten</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['content']['optimal'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $seoAnalysis['content']['score'] }}/15
                </span>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600 mb-1">Jumlah Kata: {{ number_format($seoAnalysis['content']['word_count']) }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $seoAnalysis['content']['optimal'] ? 'bg-green-600' : ($seoAnalysis['content']['word_count'] >= 150 ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ min(100, ($seoAnalysis['content']['word_count'] / 300) * 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Optimal: Minimal 300 kata</p>
            </div>
            @if(!$seoAnalysis['content']['optimal'])
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Konten terlalu pendek. Tambahkan lebih banyak konten untuk SEO yang lebih baik.
                </p>
            </div>
            @endif
        </div>

        <!-- Headings -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Struktur Heading</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['headings']['has_h1'] && $seoAnalysis['headings']['has_h2'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $seoAnalysis['headings']['score'] }}/10
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">H1:</span>
                    <span class="text-sm font-medium {{ $seoAnalysis['headings']['has_h1'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $seoAnalysis['headings']['h1_count'] }} ditemukan
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">H2:</span>
                    <span class="text-sm font-medium {{ $seoAnalysis['headings']['has_h2'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $seoAnalysis['headings']['h2_count'] }} ditemukan
                    </span>
                </div>
            </div>
            @if(!$seoAnalysis['headings']['has_h1'] || !$seoAnalysis['headings']['has_h2'])
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded mt-4">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Gunakan heading H1 dan H2 untuk struktur konten yang lebih baik.
                </p>
            </div>
            @endif
        </div>

        <!-- Links -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Internal Links</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $seoAnalysis['links']['has_links'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $seoAnalysis['links']['score'] }}/5
                </span>
            </div>
            <div class="mb-2">
                <p class="text-sm text-gray-600">Jumlah Link: {{ $seoAnalysis['links']['internal_links'] }}</p>
            </div>
            @if(!$seoAnalysis['links']['has_links'])
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Tambahkan link internal untuk meningkatkan SEO.
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Readability Score -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Readability Score</h3>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold text-gray-900">{{ $seoAnalysis['readability']['score'] }}/100</p>
                <p class="text-sm text-gray-600 mt-1">{{ $seoAnalysis['readability']['level'] }}</p>
            </div>
            <div class="w-24 h-24 rounded-full border-8 {{ $seoAnalysis['readability']['score'] >= 70 ? 'border-green-500' : ($seoAnalysis['readability']['score'] >= 50 ? 'border-yellow-500' : 'border-red-500') }} flex items-center justify-center">
                <span class="text-2xl font-bold {{ $seoAnalysis['readability']['score'] >= 70 ? 'text-green-600' : ($seoAnalysis['readability']['score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ round($seoAnalysis['readability']['score']) }}
                </span>
            </div>
        </div>
        <p class="text-sm text-gray-600">{{ $seoAnalysis['readability']['description'] }}</p>
    </div>

    <!-- Preview -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Hasil Pencarian</h3>
        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="mb-2">
                <p class="text-sm text-blue-600">{{ url('/') }}</p>
            </div>
            <div class="mb-2">
                <h3 class="text-xl text-blue-600 hover:underline">{{ $seoAnalysis['meta_title']['value'] }}</h3>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ Str::limit($seoAnalysis['meta_description']['value'], 160) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

