<section>
    <header class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Informasi Profil
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Perbarui informasi profil dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Avatar Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    @if($user->profile && $user->profile->avatar)
                        <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                    @else
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                            <span class="text-white text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Profil
                    </label>
                    <div class="flex items-center space-x-3">
                        <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Pilih Foto
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </label>
                        <span class="text-xs text-gray-500">JPG, PNG, GIF. Maks 2MB</span>
                    </div>
                    <div id="avatar-preview" class="mt-2 hidden">
                        <img id="avatar-preview-img" src="" alt="Preview" class="w-20 h-20 rounded-full object-cover border-2 border-blue-300">
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-medium" />
                <div class="mt-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <x-text-input id="name" name="name" type="text" class="pl-10 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="username" :value="__('Username')" class="text-gray-700 font-medium" />
                <div class="mt-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-400 text-sm">@</span>
                    </div>
                    <x-text-input id="username" name="username" type="text" class="pl-8 block w-full lowercase" :value="old('username', $user->username)" required autocomplete="username" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '')" />
                </div>
                <p class="mt-1 text-xs text-gray-500">Hanya huruf kecil, angka, dan tanda hubung (-_)</p>
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
            <div class="mt-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <x-text-input id="email" name="email" type="email" class="pl-10 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-yellow-700 hover:text-yellow-900 font-medium">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Bio -->
        <div>
            <x-input-label for="bio" :value="__('Biografi')" class="text-gray-700 font-medium" />
            <div class="mt-2">
                <textarea id="bio" name="bio" rows="4" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none" placeholder="Ceritakan tentang diri Anda...">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
            </div>
            <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- Location & Website -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="location" :value="__('Lokasi')" class="text-gray-700 font-medium" />
                <div class="mt-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <x-text-input id="location" name="location" type="text" class="pl-10 block w-full" :value="old('location', $user->profile->location ?? '')" placeholder="Kabupaten Pesisir Barat, Lampung" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('location')" />
            </div>

            <div>
                <x-input-label for="website" :value="__('Website/Blog')" class="text-gray-700 font-medium" />
                <div class="mt-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                    </div>
                    <x-text-input id="website" name="website" type="url" class="pl-10 block w-full" :value="old('website', $user->profile->website ?? '')" placeholder="https://example.com" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('website')" />
            </div>
        </div>

        <!-- Social Links -->
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <x-input-label :value="__('Media Sosial')" class="text-gray-700 font-medium mb-4" />
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <x-text-input name="social_links[facebook]" type="url" class="block w-full" :value="old('social_links.facebook', $user->profile->social_links['facebook'] ?? '')" placeholder="https://facebook.com/username" />
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </div>
                    <x-text-input name="social_links[twitter]" type="url" class="block w-full" :value="old('social_links.twitter', $user->profile->social_links['twitter'] ?? '')" placeholder="https://twitter.com/username" />
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-pink-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <x-text-input name="social_links[instagram]" type="url" class="block w-full" :value="old('social_links.instagram', $user->profile->social_links['instagram'] ?? '')" placeholder="https://instagram.com/username" />
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-800 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </div>
                    <x-text-input name="social_links[linkedin]" type="url" class="block w-full" :value="old('social_links.linkedin', $user->profile->social_links['linkedin'] ?? '')" placeholder="https://linkedin.com/in/username" />
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('social_links.*')" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center text-sm text-green-600 bg-green-50 px-4 py-2 rounded-lg border border-green-200"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Profil berhasil diperbarui!
                </div>
            @endif
        </div>
    </form>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    const previewImg = document.getElementById('avatar-preview-img');
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</section>
