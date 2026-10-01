<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', \App\Helpers\SettingsHelper::siteName())</title>

        <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-news-ink antialiased bg-news-paper">
        <div class="relative min-h-screen flex flex-col justify-center items-center px-4 py-10 sm:py-14">
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="absolute -top-24 -right-16 h-72 w-72 rounded-full bg-primary-100/70 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-primary-50/80 blur-3xl"></div>
                <div class="absolute inset-x-0 top-0 h-1 bg-news-accent"></div>
            </div>

            <div class="relative w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex flex-col items-center group">
                    <img
                        src="{{ \App\Helpers\SettingsHelper::siteLogo() }}"
                        alt="{{ \App\Helpers\SettingsHelper::siteName() }}"
                        class="h-14 w-14 sm:h-16 sm:w-16 object-contain mb-3"
                        width="64"
                        height="64"
                    >
                    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink group-hover:text-news-accent transition-colors">
                        {{ \App\Helpers\SettingsHelper::siteName() }}
                    </h1>
                    <p class="mt-1 text-sm text-news-muted text-center max-w-xs">
                        {{ \App\Helpers\SettingsHelper::siteDescription() ?: 'Portal informasi Kabupaten Pesisir Barat' }}
                    </p>
                </a>

                <div class="bg-white border border-news-line shadow-sm px-5 py-6 sm:px-8 sm:py-8">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-center text-sm text-news-muted">
                    &copy; {{ date('Y') }} {{ \App\Helpers\SettingsHelper::siteName() }}. Hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </body>
</html>
