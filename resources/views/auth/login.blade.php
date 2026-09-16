<x-guest-layout>
    <div class="text-center mb-6 sm:mb-8">
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-news-ink mb-1">Masuk</h2>
        <p class="text-sm text-news-muted">Silakan masuk ke akun Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-6 p-4 bg-primary-50 border-l-4 border-news-accent rounded-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-news-accent" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-news-ink">Terjadi kesalahan saat login</h3>
                    <ul class="mt-2 text-sm text-news-muted list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Google OAuth disembunyikan sementara; tampilkan lagi setelah GOOGLE_* di .env siap --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                autocomplete="username"
                placeholder="Masukkan email Anda"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-news-ink mb-1.5" />
            <x-text-input
                id="password"
                class="block w-full px-3 py-3 border-news-line rounded-sm shadow-none placeholder:text-news-muted/70 focus:border-news-accent focus:ring-news-accent {{ $errors->has('password') ? 'border-news-accent focus:border-news-accent focus:ring-news-accent' : '' }}"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password Anda"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded-sm border-news-line text-news-accent shadow-none focus:ring-news-accent"
                    name="remember"
                >
                <span class="ml-2 text-sm text-news-muted">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-news-accent hover:text-news-ink transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-news-ink text-white text-sm font-bold hover:bg-news-accent focus:outline-none focus:ring-2 focus:ring-news-accent focus:ring-offset-2 transition-colors">
            {{ __('Log in') }}
        </button>
    </form>

    @if(\App\Helpers\SettingsHelper::enableRegistration())
    <p class="mt-6 text-center text-sm text-news-muted">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-news-accent hover:text-news-ink transition-colors">Daftar di sini</a>
    </p>
    @endif
</x-guest-layout>
