@extends('layouts.penulis')

@section('title', 'Profil Penulis')
@section('page-title', 'Profil Penulis')
@section('page-subtitle', 'Kelola informasi profil Anda')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Profil Penulis</h1>
        <p class="text-gray-600">Kelola informasi profil Anda</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('penulis.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">@</span>
                        </div>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ old('username', $user->username) }}"
                            required
                            class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent lowercase @error('username') border-red-500 @enderror"
                            oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '')"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Hanya huruf kecil, angka, dan tanda hubung (-_)</p>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                    Foto Profil
                </label>
                @if($user->profile && $user->profile->avatar)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="Current avatar" class="w-24 h-24 rounded-full object-cover">
                        <p class="text-sm text-gray-600 mt-1">Foto saat ini</p>
                    </div>
                @endif
                <input 
                    type="file" 
                    id="avatar" 
                    name="avatar" 
                    accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('avatar') border-red-500 @enderror"
                >
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
            </div>

            <div class="mb-6">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                    Bio
                </label>
                <textarea 
                    id="bio" 
                    name="bio" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bio') border-red-500 @enderror"
                    placeholder="Ceritakan tentang diri Anda, pengalaman menulis, dan minat Anda..."
                >{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                    Website/Blog
                </label>
                <input 
                    type="url" 
                    id="website" 
                    name="website" 
                    value="{{ old('website', $user->profile->website ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('website') border-red-500 @enderror"
                    placeholder="https://example.com"
                >
                @error('website')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Lokasi
                </label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    value="{{ old('location', $user->profile->location ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                    placeholder="Jakarta, Indonesia"
                >
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Media Sosial
                </label>
                <div class="space-y-3">
                    <div>
                        <label for="social_facebook" class="block text-xs text-gray-600 mb-1">Facebook</label>
                        <input 
                            type="url" 
                            id="social_facebook" 
                            name="social_links[facebook]" 
                            value="{{ old('social_links.facebook', $user->profile->social_links['facebook'] ?? '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="https://facebook.com/username"
                        >
                    </div>
                    <div>
                        <label for="social_twitter" class="block text-xs text-gray-600 mb-1">Twitter</label>
                        <input 
                            type="url" 
                            id="social_twitter" 
                            name="social_links[twitter]" 
                            value="{{ old('social_links.twitter', $user->profile->social_links['twitter'] ?? '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="https://twitter.com/username"
                        >
                    </div>
                    <div>
                        <label for="social_instagram" class="block text-xs text-gray-600 mb-1">Instagram</label>
                        <input 
                            type="url" 
                            id="social_instagram" 
                            name="social_links[instagram]" 
                            value="{{ old('social_links.instagram', $user->profile->social_links['instagram'] ?? '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="https://instagram.com/username"
                        >
                    </div>
                    <div>
                        <label for="social_linkedin" class="block text-xs text-gray-600 mb-1">LinkedIn</label>
                        <input 
                            type="url" 
                            id="social_linkedin" 
                            name="social_links[linkedin]" 
                            value="{{ old('social_links.linkedin', $user->profile->social_links['linkedin'] ?? '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="https://linkedin.com/in/username"
                        >
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('penulis.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
