@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Ajukan Permintaan Menjadi Penulis</h1>
                <p class="text-gray-600">Lengkapi informasi di bawah ini untuk mengajukan permintaan menjadi penulis</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('user.submit-upgrade-request') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Bio Section -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Biografi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="bio" 
                              id="bio" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bio') border-red-500 @enderror"
                              placeholder="Ceritakan tentang diri Anda, pengalaman menulis, dan mengapa Anda ingin menjadi penulis...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Avatar Section -->
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Profil
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-400 text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   name="avatar" 
                                   id="avatar" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('avatar') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Website Section -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website/Blog (Opsional)
                    </label>
                    <input type="url" 
                           name="website" 
                           id="website" 
                           value="{{ old('website') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('website') border-red-500 @enderror"
                           placeholder="https://example.com">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location Section -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi (Opsional)
                    </label>
                    <input type="text" 
                           name="location" 
                           id="location" 
                           value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                           placeholder="Kabupaten Pesisir Barat, Lampung">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Social Links Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Media Sosial (Opsional)
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fab fa-facebook-f text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[facebook]" 
                                   value="{{ old('social_links.facebook') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://facebook.com/username">
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
                                <i class="fab fa-twitter text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[twitter]" 
                                   value="{{ old('social_links.twitter') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://twitter.com/username">
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center">
                                <i class="fab fa-instagram text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[instagram]" 
                                   value="{{ old('social_links.instagram') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://instagram.com/username">
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-800 rounded-full flex items-center justify-center">
                                <i class="fab fa-linkedin text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[linkedin]" 
                                   value="{{ old('social_links.linkedin') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://linkedin.com/in/username">
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Syarat dan Ketentuan</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Anda harus menyetujui untuk menulis konten yang berkualitas dan sesuai dengan etika jurnalistik</li>
                        <li>• Semua artikel akan melalui proses review sebelum dipublikasikan</li>
                        <li>• Admin berhak menolak atau meminta revisi artikel yang tidak sesuai standar</li>
                        <li>• Anda bertanggung jawab atas keaslian dan keakuratan konten yang ditulis</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection