@extends('layouts.admin-simple')

@section('title', 'Pengaturan - Admin Panel')
@section('page-title', 'Pengaturan Website')
@section('page-subtitle', 'Kelola konfigurasi dan pengaturan website')

@section('content')
<div class="space-y-6">
    <!-- Settings Form -->
    <form method="POST" action="#" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- General Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <h3 class="text-lg font-semibold text-news-ink mb-4 flex items-center">
                <i class="fas fa-cog mr-2 text-news-accent"></i>
                Pengaturan Umum
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-news-ink mb-2">
                        Nama Website
                    </label>
                    <input type="text" 
                           id="site_name" 
                           name="site_name" 
                           value="{{ $settings['site_name'] ?? 'Pesisir Barat Hub' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="site_description" class="block text-sm font-medium text-news-ink mb-2">
                        Deskripsi Website
                    </label>
                    <input type="text" 
                           id="site_description" 
                           name="site_description" 
                           value="{{ $settings['site_description'] ?? 'Portal berita resmi Kabupaten Pesisir Barat' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="site_keywords" class="block text-sm font-medium text-news-ink mb-2">
                        Keywords
                    </label>
                    <input type="text" 
                           id="site_keywords" 
                           name="site_keywords" 
                           value="{{ $settings['site_keywords'] ?? 'berita, pesisir barat, lampung, kabupaten' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="site_author" class="block text-sm font-medium text-news-ink mb-2">
                        Penulis Website
                    </label>
                    <input type="text" 
                           id="site_author" 
                           name="site_author" 
                           value="{{ $settings['site_author'] ?? 'Pemerintah Kabupaten Pesisir Barat' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
            </div>
        </div>

        <!-- Contact Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <h3 class="text-lg font-semibold text-news-ink mb-4 flex items-center">
                <i class="fas fa-phone mr-2 text-news-accent"></i>
                Informasi Kontak
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-news-ink mb-2">
                        Nomor Telepon
                    </label>
                    <input type="text" 
                           id="contact_phone" 
                           name="contact_phone" 
                           value="{{ $settings['contact_phone'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-news-ink mb-2">
                        Email Kontak
                    </label>
                    <input type="email" 
                           id="contact_email" 
                           name="contact_email" 
                           value="{{ $settings['contact_email'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="contact_address" class="block text-sm font-medium text-news-ink mb-2">
                        Alamat
                    </label>
                    <textarea id="contact_address" 
                              name="contact_address" 
                              rows="3"
                              class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">{{ $settings['contact_address'] ?? '' }}</textarea>
                </div>
                
                <div>
                    <label for="contact_website" class="block text-sm font-medium text-news-ink mb-2">
                        Website Resmi
                    </label>
                    <input type="url" 
                           id="contact_website" 
                           name="contact_website" 
                           value="{{ $settings['contact_website'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
            </div>
        </div>

        <!-- Social Media Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <h3 class="text-lg font-semibold text-news-ink mb-4 flex items-center">
                <i class="fas fa-share-alt mr-2 text-news-accent"></i>
                Media Sosial
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="social_facebook" class="block text-sm font-medium text-news-ink mb-2">
                        <i class="fab fa-facebook mr-1"></i>
                        Facebook
                    </label>
                    <input type="url" 
                           id="social_facebook" 
                           name="social_facebook" 
                           value="{{ $settings['social_facebook'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="social_twitter" class="block text-sm font-medium text-news-ink mb-2">
                        <i class="fab fa-twitter mr-1"></i>
                        Twitter
                    </label>
                    <input type="url" 
                           id="social_twitter" 
                           name="social_twitter" 
                           value="{{ $settings['social_twitter'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="social_instagram" class="block text-sm font-medium text-news-ink mb-2">
                        <i class="fab fa-instagram mr-1"></i>
                        Instagram
                    </label>
                    <input type="url" 
                           id="social_instagram" 
                           name="social_instagram" 
                           value="{{ $settings['social_instagram'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="social_youtube" class="block text-sm font-medium text-news-ink mb-2">
                        <i class="fab fa-youtube mr-1"></i>
                        YouTube
                    </label>
                    <input type="url" 
                           id="social_youtube" 
                           name="social_youtube" 
                           value="{{ $settings['social_youtube'] ?? '' }}"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <h3 class="text-lg font-semibold text-news-ink mb-4 flex items-center">
                <i class="fas fa-server mr-2 text-news-accent"></i>
                Pengaturan Sistem
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="posts_per_page" class="block text-sm font-medium text-news-ink mb-2">
                        Artikel per Halaman
                    </label>
                    <input type="number" 
                           id="posts_per_page" 
                           name="posts_per_page" 
                           value="{{ $settings['posts_per_page'] ?? 10 }}"
                           min="1" max="50"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div>
                    <label for="comments_per_page" class="block text-sm font-medium text-news-ink mb-2">
                        Komentar per Halaman
                    </label>
                    <input type="number" 
                           id="comments_per_page" 
                           name="comments_per_page" 
                           value="{{ $settings['comments_per_page'] ?? 10 }}"
                           min="1" max="50"
                           class="w-full px-3 py-2 border border-news-line rounded-lg focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="auto_approve_comments" 
                               name="auto_approve_comments" 
                               value="1"
                               {{ ($settings['auto_approve_comments'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                        <label for="auto_approve_comments" class="ml-2 block text-sm text-news-ink">
                            Otomatis setujui komentar baru
                        </label>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="maintenance_mode" 
                               name="maintenance_mode" 
                               value="1"
                               {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                        <label for="maintenance_mode" class="ml-2 block text-sm text-news-ink">
                            Mode maintenance (website tidak dapat diakses)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="bg-news-accent text-white px-6 py-3 rounded-lg hover:bg-news-ink transition-colors flex items-center">
                <i class="fas fa-save mr-2"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
