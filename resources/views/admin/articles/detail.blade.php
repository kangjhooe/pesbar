@extends('layouts.admin-simple')

@section('title', 'Review Artikel')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Review Artikel</h1>
                    <p class="text-gray-600 mt-2">Tinjau dan evaluasi artikel sebelum dipublikasikan</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                    <!-- Article Header -->
                    <div class="px-6 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $article->title }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ $article->author->name ?? 'Unknown' }}</span>
                                @if($article->author)
                                    <x-user-role-badge :user="$article->author" size="xs" class="ml-2" />
                                @endif
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $article->created_at ? $article->created_at->format('d-m-Y H:i') : '-' }} WIB
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $article->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    @if($article->featured_image)
                    <div class="px-6 py-4">
                        <img src="{{ asset('storage/' . $article->featured_image) }}" 
                             alt="{{ $article->title }}" 
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                    @endif

                    <!-- Excerpt -->
                    @if($article->excerpt)
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan</h3>
                        <p class="text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $article->excerpt }}</p>
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="px-6 py-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Konten Artikel</h3>
                        <div class="prose max-w-none">
                            {!! $article->content !!}
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($article->tags->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Article Meta -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Artikel</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Tipe</span>
                            <span class="text-sm font-medium capitalize">{{ $article->type }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Views</span>
                            <span class="text-sm font-medium">{{ number_format($article->views) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Menunggu Review
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Fitur</span>
                            <div class="flex space-x-1">
                                @if($article->is_featured)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Featured
                                    </span>
                                @endif
                                @if($article->is_breaking)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Breaking
                                    </span>
                                @endif
                                @if(!$article->is_featured && !$article->is_breaking)
                                    <span class="text-gray-500 text-sm">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Author Info -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Penulis</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($article->author->profile && $article->author->profile->avatar)
                                    <img class="h-16 w-16 rounded-full object-cover" 
                                         src="{{ asset('storage/' . $article->author->profile->avatar) }}" 
                                         alt="{{ $article->author->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-xl font-medium text-gray-700">{{ substr($article->author->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <div class="text-lg font-medium text-gray-900">
                                        @if($article->author && $article->author->isPenulis() && $article->author->username)
                                            <a href="{{ route('penulis.public-profile', $article->author->username) }}" class="text-blue-600 hover:text-blue-800 font-medium" target="_blank">
                                                {{ $article->author->name ?? 'Unknown' }}
                                            </a>
                                        @else
                                            {{ $article->author->name ?? 'Unknown' }}
                                        @endif
                                    </div>
                                    @if($article->author)
                                        <x-user-role-badge :user="$article->author" size="sm" />
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">{{ $article->author->email ?? '-' }}</div>
                                @if($article->author->profile && $article->author->profile->bio)
                                    <div class="text-sm text-gray-600 mt-2">{{ $article->author->profile->bio }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-2">
                                    Bergabung: {{ $article->author->created_at ? $article->author->created_at->format('d-m-Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Aksi Review</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <form action="{{ route('admin.articles.approve', $article) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui Artikel
                            </button>
                        </form>
                        
                        <button type="button" onclick="showRejectModal()" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tolak Artikel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tolak Artikel</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="rejectForm" action="{{ route('admin.articles.reject', $article) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan
                        </label>
                        <textarea id="rejectReason" 
                                  name="reason" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Berikan alasan mengapa artikel ini ditolak..."
                                  required></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" 
                            onclick="closeRejectModal()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fas fa-times mr-2"></i>
                        Tolak Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectReason').value = '';
}
</script>
@endsection
