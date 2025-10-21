@extends('layouts.public')

@section('title', 'Tentang Kami - ' . \App\Helpers\SettingsHelper::siteName())
@section('description', \App\Helpers\SettingsHelper::siteDescription())

@section('content')
<div class="container-responsive py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ \App\Helpers\SettingsHelper::aboutTitle() }}</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
            {{ \App\Helpers\SettingsHelper::siteDescription() }}
        </p>
        
        @if(\App\Helpers\SettingsHelper::aboutImage())
            <div class="max-w-4xl mx-auto">
                <img src="{{ \App\Helpers\SettingsHelper::aboutImage() }}" 
                     alt="{{ \App\Helpers\SettingsHelper::aboutTitle() }}" 
                     class="w-full h-64 md:h-80 object-cover rounded-lg shadow-lg">
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Visi Misi -->
            <div class="card mb-8">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-bullseye text-blue-600 mr-3"></i>
                        Visi & Misi
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-blue-800 mb-3">{{ \App\Helpers\SettingsHelper::visionTitle() }}</h3>
                            <p class="text-gray-700 leading-relaxed">
                                {{ \App\Helpers\SettingsHelper::visionContent() }}
                            </p>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-green-800 mb-3">{{ \App\Helpers\SettingsHelper::missionTitle() }}</h3>
                            <div class="text-gray-700 leading-relaxed">
                                {{ \App\Helpers\SettingsHelper::missionContent() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sejarah -->
            <div class="card mb-8">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-history text-blue-600 mr-3"></i>
                        Sejarah
                    </h2>
                    <div class="prose max-w-none">
                        @if(\App\Helpers\SettingsHelper::aboutContent())
                            {!! nl2br(e(\App\Helpers\SettingsHelper::aboutContent())) !!}
                        @else
                            <p class="text-gray-700 leading-relaxed mb-4">
                                Portal Berita Kabupaten Pesisir Barat didirikan sebagai inisiatif 
                                untuk meningkatkan akses informasi publik dan transparansi pemerintahan di Kabupaten Pesisir Barat, Lampung.
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                Sejak awal berdirinya, portal ini berkomitmen untuk menyajikan berita yang akurat, 
                                objektif, dan berimbang tentang berbagai aspek kehidupan masyarakat Pesisir Barat, 
                                mulai dari politik, ekonomi, sosial, budaya, hingga olahraga.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                Dengan dukungan teknologi modern dan tim jurnalistik yang profesional, 
                                kami terus berupaya menjadi sumber informasi terpercaya bagi masyarakat 
                                Kabupaten Pesisir Barat dan sekitarnya.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tim Redaksi -->
            <div class="card mb-8">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        {{ \App\Helpers\SettingsHelper::editorialTeamTitle() }}
                    </h2>
                    
                    @php
                        $teamMembers = \App\Helpers\SettingsHelper::editorialTeamContent();
                    @endphp
                    
                    @if(!empty($teamMembers))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($teamMembers as $member)
                                <div class="bg-gray-50 p-6 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $member['position'] ?? '' }}</h3>
                                    <p class="text-gray-600">{{ $member['name'] ?? '' }}</p>
                                    @if(!empty($member['description']))
                                        <p class="text-sm text-gray-500 mt-1">{{ $member['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tim redaksi belum dikonfigurasi. Silakan hubungi administrator.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Kontak -->
            <div class="widget mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-phone text-blue-600 mr-2"></i>
                        Kontak Kami
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-blue-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Alamat</p>
                                <p class="text-sm text-gray-600">
                                    {{ \App\Helpers\SettingsHelper::contactAddress() }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-phone text-blue-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Telepon</p>
                                <p class="text-sm text-gray-600">{{ \App\Helpers\SettingsHelper::contactPhone() }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-blue-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Email</p>
                                <p class="text-sm text-gray-600">{{ \App\Helpers\SettingsHelper::contactEmail() }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-globe text-blue-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-800">Website</p>
                                <p class="text-sm text-gray-600">www.pesisirbarat.go.id</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jam Kerja -->
            <div class="widget mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        Jam Kerja
                    </h3>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Senin - Kamis</span>
                            <span class="font-medium">08:00 - 16:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumat</span>
                            <span class="font-medium">08:00 - 11:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sabtu - Minggu</span>
                            <span class="font-medium text-red-600">Tutup</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Sosial -->
            <div class="widget">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-share-alt text-blue-600 mr-2"></i>
                        Media Sosial
                    </h3>
                    
                    <div class="space-y-3">
                        @if(\App\Helpers\SettingsHelper::facebookUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::facebookUrl() }}" target="_blank" class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-facebook text-blue-600 text-lg"></i>
                            <span class="text-gray-700">Facebook</span>
                        </a>
                        @endif
                        
                        @if(\App\Helpers\SettingsHelper::twitterUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::twitterUrl() }}" target="_blank" class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-twitter text-blue-400 text-lg"></i>
                            <span class="text-gray-700">Twitter</span>
                        </a>
                        @endif
                        
                        @if(\App\Helpers\SettingsHelper::instagramUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::instagramUrl() }}" target="_blank" class="flex items-center space-x-3 p-3 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                            <i class="fab fa-instagram text-pink-600 text-lg"></i>
                            <span class="text-gray-700">Instagram</span>
                        </a>
                        @endif
                        
                        @if(\App\Helpers\SettingsHelper::youtubeUrl())
                        <a href="{{ \App\Helpers\SettingsHelper::youtubeUrl() }}" target="_blank" class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fab fa-youtube text-red-600 text-lg"></i>
                            <span class="text-gray-700">YouTube</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="mt-12">
        <div class="card">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Pencapaian Kami</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-2">500+</div>
                        <div class="text-gray-600">Artikel Diterbitkan</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 mb-2">10K+</div>
                        <div class="text-gray-600">Pembaca Setia</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600 mb-2">50+</div>
                        <div class="text-gray-600">Kategori Berita</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
                        <div class="text-gray-600">Update Berita</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
