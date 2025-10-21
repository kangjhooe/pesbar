@extends('layouts.admin-simple')

@section('title', 'Pengaturan - Admin Panel')
@section('page-title', 'Pengaturan Website')
@section('page-subtitle', 'Kelola pengaturan umum website')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('general')" id="general-tab" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <i class="fas fa-cog mr-2"></i>Umum
                </button>
                <button onclick="showTab('logo')" id="logo-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-image mr-2"></i>Logo
                </button>
                <button onclick="showTab('about')" id="about-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-info-circle mr-2"></i>Tentang
                </button>
                <button onclick="showTab('seo')" id="seo-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-search mr-2"></i>SEO
                </button>
                <button onclick="showTab('editorial')" id="editorial-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-users mr-2"></i>Tim Redaksi
                </button>
                <button onclick="showTab('system')" id="system-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-server mr-2"></i>Sistem
                </button>
            </nav>
        </div>

        <!-- General Settings Tab -->
        <div id="general-content" class="tab-content p-6">
            <form action="{{ route('admin.settings.general') }}" method="POST">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Umum</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Website</label>
                        <input type="text" id="site_name" name="site_name" value="{{ $settings['site_name'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Website</label>
                        <textarea id="site_description" name="site_description" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>{{ $settings['site_description'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label for="site_keywords" class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                        <input type="text" id="site_keywords" name="site_keywords" value="{{ $settings['site_keywords'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="keyword1, keyword2, keyword3">
                    </div>
                    
                    <div>
                        <label for="site_author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                        <input type="text" id="site_author" name="site_author" value="{{ $settings['site_author'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email Kontak</label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Telepon Kontak</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea id="contact_address" name="contact_address" rows="2" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings['contact_address'] ?? '' }}</textarea>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">Media Sosial</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                            <input type="url" id="facebook_url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                            <input type="url" id="twitter_url" name="twitter_url" value="{{ $settings['twitter_url'] ?? '' }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                            <input type="url" id="instagram_url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                            <input type="url" id="youtube_url" name="youtube_url" value="{{ $settings['youtube_url'] ?? '' }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Pengaturan Umum
                    </button>
                </div>
            </form>
        </div>

        <!-- Logo Settings Tab -->
        <div id="logo-content" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Logo</h3>
                
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-2">Logo Website</label>
                        <div class="file-input-container">
                            <input type="file" id="site_logo" name="site_logo" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('site_logo') border-red-500 @enderror"
                                   onchange="previewImage(this, 'logo-preview')">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF, SVG, WebP. Maksimal 2MB</p>
                        @error('site_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-2">Logo saat ini:</p>
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Current Logo" 
                                     class="max-w-xs h-20 object-contain border rounded">
                            </div>
                        @endif
                        <div id="logo-preview" class="mt-2 hidden">
                            <p class="text-sm text-gray-600 mb-2">Preview:</p>
                            <img id="logo-preview-img" src="" alt="Logo Preview" class="max-w-xs h-20 object-contain border rounded">
                        </div>
                    </div>
                    
                    <div>
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                        <div class="file-input-container">
                            <input type="file" id="site_favicon" name="site_favicon" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('site_favicon') border-red-500 @enderror"
                                   onchange="previewImage(this, 'favicon-preview')">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF, ICO, SVG. Maksimal 512KB</p>
                        @error('site_favicon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-2">Favicon saat ini:</p>
                                <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Current Favicon" 
                                     class="w-8 h-8 object-contain border rounded">
                            </div>
                        @endif
                        <div id="favicon-preview" class="mt-2 hidden">
                            <p class="text-sm text-gray-600 mb-2">Preview:</p>
                            <img id="favicon-preview-img" src="" alt="Favicon Preview" class="w-8 h-8 object-contain border rounded">
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Logo
                    </button>
                </div>
            </form>
        </div>

        <!-- About Settings Tab -->
        <div id="about-content" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.about') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Halaman Tentang</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="about_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Halaman Tentang</label>
                        <input type="text" id="about_title" name="about_title" value="{{ $settings['about_title'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="about_image" class="block text-sm font-medium text-gray-700 mb-2">Gambar Halaman Tentang</label>
                        <div class="file-input-container">
                            <input type="file" id="about_image" name="about_image" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="previewImage(this, 'about-preview')">
                        </div>
                        @if(isset($settings['about_image']) && $settings['about_image'])
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                                <img src="{{ asset('storage/' . $settings['about_image']) }}" alt="Current About Image" 
                                     class="max-w-md h-48 object-cover border rounded">
                            </div>
                        @endif
                        <div id="about-preview" class="mt-2 hidden">
                            <p class="text-sm text-gray-600 mb-2">Preview:</p>
                            <img id="about-preview-img" src="" alt="About Preview" class="max-w-md h-48 object-cover border rounded">
                        </div>
                    </div>
                    
                    <div>
                        <label for="about_content" class="block text-sm font-medium text-gray-700 mb-2">Konten Halaman Tentang</label>
                        <textarea id="about_content" name="about_content" rows="8" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>{{ $settings['about_content'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mission_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Misi</label>
                            <input type="text" id="mission_title" name="mission_title" value="{{ $settings['mission_title'] ?? '' }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="vision_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Visi</label>
                            <input type="text" id="vision_title" name="vision_title" value="{{ $settings['vision_title'] ?? '' }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="mission_content" class="block text-sm font-medium text-gray-700 mb-2">Konten Misi</label>
                            <textarea id="mission_content" name="mission_content" rows="4" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings['mission_content'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label for="vision_content" class="block text-sm font-medium text-gray-700 mb-2">Konten Visi</label>
                            <textarea id="vision_content" name="vision_content" rows="4" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings['vision_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Halaman Tentang
                    </button>
                </div>
            </form>
        </div>

        <!-- SEO Settings Tab -->
        <div id="seo-content" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.seo') }}" method="POST">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan SEO</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ $settings['meta_title'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               maxlength="60">
                        <p class="mt-1 text-sm text-gray-500">Maksimal 60 karakter</p>
                    </div>
                    
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  maxlength="160">{{ $settings['meta_description'] ?? '' }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Maksimal 160 karakter</p>
                    </div>
                    
                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input type="text" id="meta_keywords" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="keyword1, keyword2, keyword3">
                    </div>
                    
                    <div>
                        <label for="google_analytics" class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                        <input type="text" id="google_analytics" name="google_analytics" value="{{ $settings['google_analytics'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="GA-XXXXXXXXX-X">
                    </div>
                    
                    <div>
                        <label for="google_search_console" class="block text-sm font-medium text-gray-700 mb-2">Google Search Console</label>
                        <input type="text" id="google_search_console" name="google_search_console" value="{{ $settings['google_search_console'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Verification code">
                    </div>
                    
                    <div>
                        <label for="facebook_pixel" class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                        <input type="text" id="facebook_pixel" name="facebook_pixel" value="{{ $settings['facebook_pixel'] ?? '' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Pixel ID">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Pengaturan SEO
                    </button>
                </div>
            </form>
        </div>

        <!-- Editorial Team Settings Tab -->
        <div id="editorial-content" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.editorial') }}" method="POST">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tim Redaksi</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="editorial_team_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Tim Redaksi</label>
                        <input type="text" id="editorial_team_title" name="editorial_team_title" value="{{ $settings['editorial_team_title'] ?? 'Tim Redaksi' }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Anggota Tim Redaksi</label>
                        <div id="editorial-team-members" class="space-y-4">
                            @php
                                $teamMembers = json_decode($settings['editorial_team_content'] ?? '[]', true) ?: [];
                                if (empty($teamMembers)) {
                                    $teamMembers = [
                                        ['name' => '', 'position' => '', 'description' => '']
                                    ];
                                }
                            @endphp
                            
                            @foreach($teamMembers as $index => $member)
                            <div class="editorial-member border border-gray-200 rounded-lg p-4" data-index="{{ $index }}">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-sm font-medium text-gray-700">Anggota {{ $index + 1 }}</h4>
                                    <button type="button" onclick="removeEditorialMember(this)" class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
                                        <input type="text" name="editorial_team[{{ $index }}][name]" value="{{ $member['name'] ?? '' }}" 
                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Nama lengkap">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Posisi</label>
                                        <input type="text" name="editorial_team[{{ $index }}][position]" value="{{ $member['position'] ?? '' }}" 
                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Jabatan">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                                        <input type="text" name="editorial_team[{{ $index }}][description]" value="{{ $member['description'] ?? '' }}" 
                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Deskripsi singkat">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" onclick="addEditorialMember()" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>Tambah Anggota
                        </button>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Tim Redaksi
                    </button>
                </div>
            </form>
        </div>

        <!-- System Settings Tab -->
        <div id="system-content" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.system') }}" method="POST">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Sistem</h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="articles_per_page" class="block text-sm font-medium text-gray-700 mb-2">Artikel per Halaman</label>
                            <input type="number" id="articles_per_page" name="articles_per_page" 
                                   value="{{ $settings['articles_per_page'] ?? 10 }}" min="1" max="100"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        
                        <div>
                            <label for="comments_per_page" class="block text-sm font-medium text-gray-700 mb-2">Komentar per Halaman</label>
                            <input type="number" id="comments_per_page" name="comments_per_page" 
                                   value="{{ $settings['comments_per_page'] ?? 10 }}" min="1" max="100"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900">Opsi Komentar</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="auto_approve_comments" value="1" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ ($settings['auto_approve_comments'] ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Otomatis setujui komentar</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="require_comment_approval" value="1" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ ($settings['require_comment_approval'] ?? true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Perlu persetujuan komentar</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900">Opsi Umum</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="enable_registration" value="1" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ ($settings['enable_registration'] ?? true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Aktifkan registrasi pengguna</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="enable_newsletter" value="1" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ ($settings['enable_newsletter'] ?? true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Aktifkan newsletter</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="maintenance_mode" value="1" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Mode maintenance</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between">
                    <a href="{{ route('admin.settings.clear-cache') }}" 
                       class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                       onclick="return confirm('Apakah Anda yakin ingin membersihkan cache?')">
                        <i class="fas fa-broom mr-2"></i>Bersihkan Cache
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan Pengaturan Sistem
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* File input improvements */
input[type="file"] {
    position: relative;
    z-index: 10;
    pointer-events: auto !important;
    cursor: pointer !important;
}

input[type="file"]::-webkit-file-upload-button {
    cursor: pointer;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 600;
    color: #1d4ed8;
    transition: all 0.2s;
}

input[type="file"]::-webkit-file-upload-button:hover {
    background: #dbeafe;
}

/* Ensure file inputs are not blocked */
.file-input-container {
    position: relative;
    z-index: 1;
}

.file-input-container input[type="file"] {
    position: relative;
    z-index: 2;
}
</style>
@endpush

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImg = document.getElementById(previewId + '-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}

// Ensure file inputs are clickable
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        // Remove any potential blocking styles
        input.style.pointerEvents = 'auto';
        input.style.cursor = 'pointer';
        input.style.opacity = '1';
        
        // Add click event listener as backup
        input.addEventListener('click', function(e) {
            console.log('File input clicked');
        });
    });
});

// Editorial team management functions
let editorialMemberIndex = {{ count(json_decode($settings['editorial_team_content'] ?? '[]', true) ?: []) }};

function addEditorialMember() {
    const container = document.getElementById('editorial-team-members');
    const memberHtml = `
        <div class="editorial-member border border-gray-200 rounded-lg p-4" data-index="${editorialMemberIndex}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-sm font-medium text-gray-700">Anggota ${editorialMemberIndex + 1}</h4>
                <button type="button" onclick="removeEditorialMember(this)" class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
                    <input type="text" name="editorial_team[${editorialMemberIndex}][name]" value="" 
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Nama lengkap">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Posisi</label>
                    <input type="text" name="editorial_team[${editorialMemberIndex}][position]" value="" 
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Jabatan">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                    <input type="text" name="editorial_team[${editorialMemberIndex}][description]" value="" 
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Deskripsi singkat">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', memberHtml);
    editorialMemberIndex++;
}

function removeEditorialMember(button) {
    const member = button.closest('.editorial-member');
    member.remove();
    
    // Update numbering
    updateEditorialMemberNumbers();
}

function updateEditorialMemberNumbers() {
    const members = document.querySelectorAll('.editorial-member');
    members.forEach((member, index) => {
        const title = member.querySelector('h4');
        title.textContent = `Anggota ${index + 1}`;
    });
}
</script>
@endpush
@endsection
