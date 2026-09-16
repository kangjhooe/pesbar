<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-news-ink mb-1">Daftar</h2>
        <p class="text-sm text-news-muted">Bergabunglah dengan komunitas Pesisir Barat</p>
    </div>

    {{-- Google OAuth disembunyikan sementara; tampilkan lagi setelah GOOGLE_* di .env siap --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-sm font-semibold text-news-ink" />
            <x-text-input id="name" class="block mt-1.5 w-full px-3 py-2.5 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" class="text-sm font-semibold text-news-ink" />
            <x-text-input id="username" class="block mt-1.5 w-full px-3 py-2.5 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent" type="text" name="username" :value="old('username')" required autocomplete="username" placeholder="contoh: johndoe" pattern="[a-zA-Z0-9_-]+" minlength="3" maxlength="30" />
            <p class="mt-1 text-xs text-news-muted">Hanya huruf, angka, dash (-), dan underscore (_). Minimal 3 karakter.</p>
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-semibold text-news-ink" />
            <x-text-input id="email" class="block mt-1.5 w-full px-3 py-2.5 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" class="text-sm font-semibold text-news-ink" />
            <x-text-input id="password" class="block mt-1.5 w-full px-3 py-2.5 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Minimal 8 karakter, campuran huruf & angka" />
            <p class="mt-1 text-xs text-news-muted">Minimal 8 karakter, huruf besar &amp; kecil, angka, dan simbol.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" class="text-sm font-semibold text-news-ink" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full px-3 py-2.5 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Ulangi kata sandi Anda" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-start gap-3">
            <input id="terms" name="terms" type="checkbox" class="mt-0.5 rounded-sm border-news-line text-news-accent shadow-none focus:ring-news-accent" required>
            <label for="terms" class="text-sm text-news-muted">
                Saya menyetujui
                <a href="{{ route('terms') }}" target="_blank" class="font-semibold text-news-accent hover:text-news-ink underline-offset-2 hover:underline">Syarat dan Ketentuan</a>
                dan
                <a href="{{ route('privacy') }}" target="_blank" class="font-semibold text-news-accent hover:text-news-ink underline-offset-2 hover:underline">Kebijakan Privasi</a>
            </label>
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-news-ink text-white text-sm font-bold hover:bg-news-accent focus:outline-none focus:ring-2 focus:ring-news-accent focus:ring-offset-2 transition-colors">
            {{ __('Daftar Sekarang') }}
        </button>

        <p class="text-center text-sm text-news-muted">
            Sudah punya akun?
            <a class="font-semibold text-news-accent hover:text-news-ink transition-colors" href="{{ route('login') }}">
                Masuk di sini
            </a>
        </p>
    </form>
</x-guest-layout>
