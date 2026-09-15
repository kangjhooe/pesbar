<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login - ' . \App\Helpers\SettingsHelper::siteName())</title>

    <link rel="icon" type="image/x-icon" href="{{ \App\Helpers\SettingsHelper::siteFavicon() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-news-ink antialiased bg-news-paper">
    <div class="relative min-h-screen flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 -right-16 h-72 w-72 rounded-full bg-primary-100/70 blur-3xl"></div>
            <div class="absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-primary-50/80 blur-3xl"></div>
            <div class="absolute inset-x-0 top-0 h-1 bg-news-accent"></div>
        </div>

        <div class="relative max-w-md w-full space-y-6 sm:space-y-8">
            <div class="text-center">
                <div class="flex justify-center mb-4 sm:mb-6">
                    <img src="{{ \App\Helpers\SettingsHelper::siteLogo() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }}" class="w-14 h-14 sm:w-16 sm:h-16 max-w-full object-contain">
                </div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold text-news-ink mb-2">
                    @yield('page-title', \App\Helpers\SettingsHelper::siteName())
                </h2>
                <p class="text-news-muted text-sm sm:text-base px-2">
                    @yield('page-subtitle', \App\Helpers\SettingsHelper::siteDescription())
                </p>
            </div>

            <div class="bg-white border border-news-line shadow-sm p-5 sm:p-8">
                @yield('content')
            </div>

            <div class="text-center">
                <p class="text-sm text-news-muted">
                    &copy; {{ date('Y') }} {{ \App\Helpers\SettingsHelper::siteName() }}
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
