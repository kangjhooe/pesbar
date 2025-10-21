<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100">
            <!-- Logo Section -->
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center">
                    <x-application-logo class="h-24 w-auto mb-4" />
                    <h1 class="text-2xl font-bold text-gray-800">Pesisir Barat</h1>
                    <p class="text-sm text-gray-600 mt-1">Portal Informasi Kabupaten</p>
                </a>
            </div>

            <!-- Login Card -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl rounded-2xl border border-gray-100" style="position: relative; z-index: 10;">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Pesisir Barat. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
