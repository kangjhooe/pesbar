@extends('layouts.admin-simple')

@section('title', 'Tentang Kami - Pesisir Barat Hub')
@section('page-title', 'Tentang Kami')
@section('page-subtitle', 'Informasi tentang Pesisir Barat Hub')

@section('content')
<div class="p-4 lg:p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white border border-news-line p-6 lg:p-8 mb-6">
            <div class="flex flex-col lg:flex-row items-center lg:items-start space-y-4 lg:space-y-0 lg:space-x-6">
                <div class="flex-shrink-0">
                    <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="w-20 h-20 lg:w-24 lg:h-24 max-w-full object-contain">
                </div>
                <div class="flex-1 text-center lg:text-left">
                    <h1 class="text-2xl lg:text-3xl font-bold text-news-ink mb-2">{{ \App\Helpers\SettingsHelper::siteName() }}</h1>
                    <p class="text-news-muted text-lg">Platform informasi Kabupaten Pesisir Barat</p>
                </div>
            </div>
        </div>

        <!-- About Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Visi Misi -->
            <div class="bg-white border border-news-line p-6">
                <h2 class="text-xl font-semibold text-news-ink mb-4 flex items-center">
                    <i class="fas fa-eye text-news-accent mr-3"></i>
                    Visi & Misi
                </h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-news-ink mb-2">Visi</h3>
                        <p class="text-news-muted text-sm leading-relaxed">
                            Menjadi portal berita terdepan yang menyediakan informasi akurat, terkini, dan terpercaya 
                            untuk masyarakat Kabupaten Pesisir Barat.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink mb-2">Misi</h3>
                        <ul class="text-news-muted text-sm space-y-1">
                            <li>• Menyediakan informasi publik yang transparan</li>
                            <li>• Meningkatkan partisipasi masyarakat dalam pembangunan</li>
                            <li>• Menjadi jembatan komunikasi antara pemerintah dan masyarakat</li>
                            <li>• Menyebarluaskan kebijakan dan program pemerintah</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="bg-white border border-news-line p-6">
                <h2 class="text-xl font-semibold text-news-ink mb-4 flex items-center">
                    <i class="fas fa-phone text-news-accent mr-3"></i>
                    Kontak Kami
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-news-muted w-5"></i>
                        <span class="ml-3 text-news-muted text-sm">
                            Jl. Raya Krui - Liwa, Kabupaten Pesisir Barat, Lampung
                        </span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-news-muted w-5"></i>
                        <span class="ml-3 text-news-muted text-sm">(0728) 123456</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-news-muted w-5"></i>
                        <span class="ml-3 text-news-muted text-sm">info@pesisirbarat.go.id</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-globe text-news-muted w-5"></i>
                        <span class="ml-3 text-news-muted text-sm">www.pesisirbarat.go.id</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white border border-news-line p-6 mb-6">
            <h2 class="text-xl font-semibold text-news-ink mb-4 flex items-center">
                <i class="fas fa-star text-news-accent mr-3"></i>
                Fitur Portal
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-newspaper text-news-accent text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink text-sm">Berita Terkini</h3>
                        <p class="text-news-muted text-xs">Informasi terbaru dari berbagai sektor</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-search text-news-accent text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink text-sm">Pencarian Cepat</h3>
                        <p class="text-news-muted text-xs">Temukan informasi dengan mudah</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-mobile-alt text-news-accent text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink text-sm">Responsif</h3>
                        <p class="text-news-muted text-xs">Akses dari berbagai perangkat</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-news-accent text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink text-sm">Interaktif</h3>
                        <p class="text-news-muted text-xs">Berikan komentar dan feedback</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-news-accent text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink text-sm">Newsletter</h3>
                        <p class="text-news-muted text-xs">Dapatkan update via email</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-share-alt text-news-accent text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-news-ink text-sm">Berbagi</h3>
                        <p class="text-news-muted text-xs">Bagikan ke media sosial</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white border border-news-line p-6">
            <h2 class="text-xl font-semibold text-news-ink mb-4 flex items-center">
                <i class="fas fa-chart-bar text-news-accent mr-3"></i>
                Statistik Portal
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-news-accent">{{ \App\Models\Article::count() }}</div>
                    <div class="text-sm text-news-muted">Total Artikel</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-news-accent">{{ \App\Models\Category::count() }}</div>
                    <div class="text-sm text-news-muted">Kategori</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-news-accent">{{ \App\Models\Comment::count() }}</div>
                    <div class="text-sm text-news-muted">Komentar</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-news-accent">{{ \App\Models\NewsletterSubscriber::count() }}</div>
                    <div class="text-sm text-news-muted">Subscriber</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
