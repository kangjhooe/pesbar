@extends('layouts.public')

@section('title', 'Kesalahan Server - Portal Berita Kabupaten Pesisir Barat')
@section('description', 'Terjadi kesalahan pada server, silakan coba lagi nanti')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Error Code -->
        <div class="text-9xl font-bold text-orange-500 mb-4">
            500
        </div>
        
        <!-- Error Icon -->
        <div class="mx-auto h-24 w-24 text-orange-500 mb-6">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        
        <!-- Error Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Kesalahan Server
        </h1>
        
        <!-- Error Description -->
        <p class="text-lg text-gray-600 mb-8">
            Maaf, terjadi kesalahan pada server kami. 
            Tim teknis telah diberitahu dan sedang memperbaiki masalah ini.
        </p>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>
            
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Coba Lagi
            </button>
        </div>
        
        <!-- Retry Info -->
        <div class="mt-8 p-4 bg-orange-50 border border-orange-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-800">
                        <strong>Tips:</strong> Silakan coba lagi dalam beberapa menit. Jika masalah berlanjut, 
                        hubungi tim support kami.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="mt-6">
            <p class="text-sm text-gray-500">
                Jika masalah berlanjut, silakan hubungi kami di 
                <a href="mailto:support@pesisirbarat.go.id" class="text-primary-600 hover:text-primary-500">
                    support@pesisirbarat.go.id
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
