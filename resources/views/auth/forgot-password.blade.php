<x-guest-layout>
    <div class="text-center mb-6 sm:mb-8">
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-news-ink mb-1">Lupa Password</h2>
        <p class="text-sm text-news-muted">Masukkan email untuk menerima link reset password</p>
    </div>

    <p class="mb-5 text-sm text-news-muted leading-relaxed">
        Tidak masalah. Kirimkan alamat email Anda, lalu kami akan mengirimkan link untuk mengatur ulang kata sandi.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-news-ink mb-1.5" />
            <x-text-input
                id="email"
                class="block w-full px-3 py-3 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent {{ $errors->has('email') ? 'border-news-accent focus:border-news-accent focus:ring-news-accent' : '' }}"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                placeholder="Masukkan email Anda"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-news-ink text-white text-sm font-bold hover:bg-news-accent focus:outline-none focus:ring-2 focus:ring-news-accent focus:ring-offset-2 transition-colors">
            Kirim Link Reset Password
        </button>

        <p class="text-center text-sm text-news-muted">
            <a href="{{ route('login') }}" class="font-semibold text-news-accent hover:text-news-ink transition-colors">
                ← Kembali ke halaman login
            </a>
        </p>
    </form>
</x-guest-layout>
