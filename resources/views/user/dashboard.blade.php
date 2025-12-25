@extends('layouts.user')

@section('title', 'Dashboard User')

@section('content')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .animate-fade-in-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .stat-card .p-4 {
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover .p-4 {
        transform: scale(1.1) rotate(5deg);
    }
    
    table tbody tr {
        transition: all 0.2s ease;
    }
    
    table tbody tr:hover {
        background: linear-gradient(to right, rgba(59, 130, 246, 0.05), rgba(99, 102, 241, 0.05));
    }
    
    #modalContent {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    
    /* Smooth scroll */
    html {
        scroll-behavior: smooth;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #6366f1);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #4f46e5);
    }
</style>

<div class="mb-8 animate-fade-in-up">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                Dashboard Saya
            </h1>
            <p class="text-gray-600 text-lg">Selamat datang kembali, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>! 👋</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </div>
    </div>
</div>

@auth
    @if(auth()->user()->role === 'user')
        @if(auth()->user()->verification_request_status === 'pending')
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 rounded-lg shadow-md mb-6 p-5 animate-fade-in-up">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center animate-pulse">
                                <i class="fas fa-clock text-yellow-800 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800 text-lg">Permintaan upgrade Anda sedang dalam proses review</span>
                            @if(auth()->user()->verification_requested_at)
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    Dikirim pada: {{ auth()->user()->verification_requested_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_request_status === 'rejected')
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 rounded-lg shadow-md mb-6 p-5 animate-fade-in-up">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-400 rounded-full flex items-center justify-center">
                                <i class="fas fa-times-circle text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800 text-lg">Permintaan upgrade Anda ditolak</span>
                            <p class="text-sm text-gray-600 mt-1">Anda dapat mengajukan ulang dengan melengkapi informasi yang lebih lengkap.</p>
                        </div>
                    </div>
                    <a href="{{ route('user.upgrade-request') }}" class="bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-6 py-3 rounded-lg text-sm font-medium shadow-lg transform transition hover:scale-105">
                        <i class="fas fa-redo mr-2"></i>Ajukan Ulang
                    </a>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 rounded-lg shadow-md mb-6 p-5 animate-fade-in-up">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-info-circle text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800 text-lg">Ingin menjadi penulis?</span>
                            <p class="text-sm text-gray-600 mt-1">Ajukan permintaan upgrade untuk bisa menulis artikel!</p>
                        </div>
                    </div>
                    <a href="{{ route('user.upgrade-request') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-6 py-3 rounded-lg text-sm font-medium shadow-lg transform transition hover:scale-105">
                        <i class="fas fa-arrow-up mr-2"></i>Ajukan Upgrade
                    </a>
                </div>
            </div>
        @endif
    @endif
@endauth

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-t-4 border-blue-500 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Komentar</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_comments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-t-4 border-green-500 animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Disetujui</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['approved_comments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-t-4 border-yellow-500 animate-fade-in-up" style="animation-delay: 0.3s">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-4 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Menunggu</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pending_comments'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-4 mb-8 animate-fade-in-up" style="animation-delay: 0.4s">
    <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
        <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Edit Profil
    </a>
    @if(auth()->user()->role === 'user')
        <a href="{{ route('user.upgrade-request') }}" class="group bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
            <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Ajukan Menjadi Penulis
        </a>
    @endif
    <a href="{{ route('articles.index') }}" class="group bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
        <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
        </svg>
        Lihat Artikel
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100 animate-fade-in-up" style="animation-delay: 0.5s">
    <div class="flex items-center mb-4">
        <div class="p-2 bg-blue-100 rounded-lg mr-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Filter Komentar</h3>
    </div>
    <form method="GET" action="{{ route('user.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1 text-blue-500"></i>Status Komentar
            </label>
            <select 
                id="status" 
                name="status" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            >
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu Persetujuan</option>
            </select>
        </div>
        
        <div class="md:col-span-2 flex items-end gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
                <i class="fas fa-check mr-2"></i>Terapkan Filter
            </button>
            @if(request()->has('status'))
                <a href="{{ route('user.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow hover:shadow-md transform hover:scale-105 flex items-center">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Comments Table -->
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s">
    <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Komentar Saya</h2>
        </div>
        <span class="px-4 py-2 bg-white rounded-lg text-sm font-semibold text-gray-700 shadow-sm">
            <i class="fas fa-list mr-1 text-blue-500"></i>Total: {{ $comments->total() }} komentar
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Artikel</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Komentar</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($comments as $comment)
                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900 mb-1">
                            <a href="{{ route('articles.show', $comment->article) }}" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center group">
                                <i class="fas fa-newspaper mr-2 text-blue-400 group-hover:text-blue-600"></i>
                                {{ Str::limit($comment->article->title, 50) }}
                            </a>
                        </div>
                        @if($comment->article->category)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-tag mr-1 text-xs"></i>
                                    {{ $comment->article->category->name }}
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 leading-relaxed">
                            <i class="fas fa-quote-left text-gray-300 mr-1"></i>
                            {{ Str::limit($comment->comment, 100) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($comment->is_approved)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200 shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Disetujui
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200 shadow-sm">
                                <svg class="w-4 h-4 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                Menunggu
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">
                            <i class="far fa-calendar-alt mr-1 text-gray-400"></i>
                            {{ $comment->created_at->format('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            <i class="far fa-clock mr-1"></i>
                            {{ $comment->created_at->format('H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-3">
                            <button onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->comment) }}')" 
                                    class="p-2 text-blue-600 hover:text-white hover:bg-blue-600 rounded-lg transition-all duration-200 transform hover:scale-110" 
                                    title="Edit Komentar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <form action="{{ route('user.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all duration-200 transform hover:scale-110" title="Hapus Komentar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium mb-2">
                                @if(request()->has('status'))
                                    Tidak ada komentar yang sesuai dengan filter
                                @else
                                    Belum ada komentar
                                @endif
                            </p>
                            <p class="text-gray-400 text-sm mb-4">
                                @if(request()->has('status'))
                                    <a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                        <i class="fas fa-redo mr-1"></i>Reset filter
                                    </a>
                                @else
                                    <a href="{{ route('articles.index') }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                        <i class="fas fa-arrow-right mr-1"></i>Lihat artikel dan berikan komentar!
                                    </a>
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comments->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex justify-center">
            {{ $comments->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Recent Articles Section -->
@if(isset($recentArticles) && $recentArticles->count() > 0)
<div class="bg-white rounded-xl shadow-lg p-6 mt-8 border border-gray-100 animate-fade-in-up" style="animation-delay: 0.7s">
    <div class="flex items-center mb-6">
        <div class="p-2 bg-purple-100 rounded-lg mr-3">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900">Artikel Terbaru</h2>
    </div>
    <div class="space-y-3">
        @foreach($recentArticles as $article)
        <div class="group flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex-1">
                <a href="{{ route('articles.show', $article) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center">
                    <i class="fas fa-newspaper mr-2 text-blue-400 group-hover:text-blue-600"></i>
                    {{ Str::limit($article->title, 60) }}
                </a>
                <div class="flex items-center space-x-3 mt-2">
                    @if($article->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-tag mr-1 text-xs"></i>
                            {{ $article->category->name }}
                        </span>
                    @endif
                    @if($article->published_at)
                        <span class="text-xs text-gray-500">
                            <i class="far fa-calendar-alt mr-1"></i>
                            {{ $article->published_at->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center ml-4">
                <a href="{{ route('articles.show', $article) }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transform transition hover:scale-105 flex items-center">
                    Baca
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
</div>

<!-- Edit Comment Modal -->
<div id="editCommentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 backdrop-blur-sm transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-0 w-full max-w-md shadow-2xl rounded-2xl bg-white transform transition-all duration-300 scale-95" id="modalContent">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Komentar
                </h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label for="edit_comment_text" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1 text-blue-500"></i>Komentar
                    </label>
                    <textarea id="edit_comment_text" 
                              name="comment" 
                              rows="5" 
                              required
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                              maxlength="1000"
                              placeholder="Tulis komentar Anda di sini..."></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>Maksimal 1000 karakter
                        </p>
                        <p class="text-xs text-gray-500" id="charCount">0 / 1000</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeEditModal()" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium transition-all duration-200 transform hover:scale-105 shadow-sm">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editComment(commentId, commentText) {
    const modal = document.getElementById('editCommentModal');
    const form = document.getElementById('editCommentForm');
    const textarea = document.getElementById('edit_comment_text');
    const modalContent = document.getElementById('modalContent');
    
    form.action = `/user/comments/${commentId}`;
    textarea.value = commentText;
    updateCharCount();
    
    modal.classList.remove('hidden');
    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
}

function closeEditModal() {
    const modal = document.getElementById('editCommentModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function updateCharCount() {
    const textarea = document.getElementById('edit_comment_text');
    const charCount = document.getElementById('charCount');
    if (textarea && charCount) {
        const length = textarea.value.length;
        charCount.textContent = `${length} / 1000`;
        if (length > 900) {
            charCount.classList.add('text-red-500');
            charCount.classList.remove('text-gray-500');
        } else {
            charCount.classList.remove('text-red-500');
            charCount.classList.add('text-gray-500');
        }
    }
}

// Close modal when clicking outside
document.getElementById('editCommentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Character counter for textarea
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('edit_comment_text');
    if (textarea) {
        textarea.addEventListener('input', updateCharCount);
    }
    
    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add intersection observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.animate-fade-in-up').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endsection

