@extends('layouts.admin-simple')

@section('title', 'Review Artikel')

@section('content')
<div class="min-h-screen bg-news-paper">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-news-ink">Review Artikel</h1>
                    <p class="text-news-muted mt-2">Tinjau dan evaluasi artikel sebelum dipublikasikan</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-news-ink hover:bg-news-ink text-white text-sm font-semibold rounded-lg transition-colors duration-200">
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
                    <div class="px-6 py-6 bg-news-paper border-b border-news-line">
                        <h2 class="text-2xl font-bold text-news-ink mb-4">{{ $article->title }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-news-muted">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium text-news-ink">{{ $article->author->name ?? 'Unknown' }}</span>
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
                    <div class="px-6 py-4 border-b border-news-line">
                        <h3 class="text-lg font-semibold text-news-ink mb-2">Ringkasan</h3>
                        <p class="text-news-muted bg-news-paper p-4 rounded-lg">{{ $article->excerpt }}</p>
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="px-6 py-6">
                        <h3 class="text-lg font-semibold text-news-ink mb-4">Konten Artikel</h3>
                        <div class="prose max-w-none article-content">
                            {!! $article->content !!}
                        </div>
                    </div>
                    
                    <!-- Article Content List Styling -->
                    <style>
                        /* Enhanced list styling for article content */
                        .article-content ul,
                        .article-content ol,
                        .prose ul,
                        .prose ol {
                            margin-top: 1.25em !important;
                            margin-bottom: 1.25em !important;
                            padding-left: 2em !important;
                            list-style-position: outside !important;
                        }
                        
                        .article-content ul,
                        .prose ul {
                            list-style-type: disc !important;
                        }
                        
                        .article-content ol,
                        .prose ol {
                            list-style-type: decimal !important;
                        }
                        
                        .article-content li,
                        .prose li {
                            margin-top: 0.75em !important;
                            margin-bottom: 0.75em !important;
                            padding-left: 0.5em !important;
                            line-height: 1.8 !important;
                            display: list-item !important;
                        }
                        
                        .article-content ul > li,
                        .prose ul > li {
                            list-style-type: disc !important;
                        }
                        
                        .article-content ol > li,
                        .prose ol > li {
                            list-style-type: decimal !important;
                        }
                        
                        /* Nested lists */
                        .article-content ul ul,
                        .prose ul ul {
                            list-style-type: circle !important;
                            margin-top: 0.5em !important;
                            margin-bottom: 0.5em !important;
                        }
                        
                        .article-content ul ul ul,
                        .prose ul ul ul {
                            list-style-type: square !important;
                        }
                        
                        .article-content ol ol,
                        .prose ol ol {
                            list-style-type: lower-alpha !important;
                        }
                        
                        .article-content ol ol ol,
                        .prose ol ol ol {
                            list-style-type: lower-roman !important;
                        }
                        
                        /* Ensure list markers are visible */
                        .article-content ul li::marker,
                        .prose ul li::marker {
                            color: #b91c1c !important;
                            font-size: 1.2em !important;
                            font-weight: normal !important;
                        }
                        
                        .article-content ol li::marker,
                        .prose ol li::marker {
                            color: #b91c1c !important;
                            font-weight: 600 !important;
                        }
                        
                        /* Fix for Quill editor output */
                        .article-content ul[class*="ql-"],
                        .prose ul[class*="ql-"] {
                            list-style-type: disc !important;
                            padding-left: 2em !important;
                        }
                        
                        .article-content ol[class*="ql-"],
                        .prose ol[class*="ql-"] {
                            list-style-type: decimal !important;
                            padding-left: 2em !important;
                        }
                    </style>

                    <!-- Tags -->
                    @if($article->tags->count() > 0)
                    <div class="px-6 py-4 border-t border-news-line">
                        <h3 class="text-sm font-medium text-news-ink mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-news-paper text-news-ink">
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
                    <div class="px-6 py-4 bg-gradient-to-r from-news-paper to-news-paper border-b border-news-line">
                        <h3 class="text-lg font-semibold text-news-ink">Informasi Artikel</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-news-muted">Views</span>
                            <span class="text-sm font-medium">{{ number_format($article->views) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-news-muted">Status</span>
                            @php
                                $statusBadge = match($article->status) {
                                    'published' => ['Terbit', 'bg-emerald-100 text-emerald-800'],
                                    'suspended' => ['Ditangguhkan', 'bg-orange-100 text-orange-800'],
                                    'draft' => ['Draft', 'bg-news-paper text-news-muted'],
                                    'archived' => ['Arsip', 'bg-news-paper text-news-muted'],
                                    default => [ucfirst(str_replace('_', ' ', $article->status)), 'bg-news-paper text-news-muted'],
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge[1] }}">
                                {{ $statusBadge[0] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-news-muted">Fitur</span>
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
                                    <span class="text-news-muted text-sm">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Author Info -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-news-paper to-news-paper border-b border-news-line">
                        <h3 class="text-lg font-semibold text-news-ink">Informasi Penulis</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($article->author->profile && $article->author->profile->avatar)
                                    <img class="h-16 w-16 rounded-full object-cover" 
                                         src="{{ asset('storage/' . $article->author->profile->avatar) }}" 
                                         alt="{{ $article->author->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-news-paper flex items-center justify-center">
                                        <span class="text-xl font-medium text-news-ink">{{ substr($article->author->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <div class="text-lg font-medium text-news-ink">
                                        @if($article->author && $article->author->isPenulis() && $article->author->username)
                                            <a href="{{ route('penulis.public-profile', $article->author->username) }}" class="text-news-accent hover:text-news-ink font-medium" target="_blank">
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
                                <div class="text-sm text-news-muted">{{ $article->author->email ?? '-' }}</div>
                                @if($article->author->profile && $article->author->profile->bio)
                                    <div class="text-sm text-news-muted mt-2">{{ $article->author->profile->bio }}</div>
                                @endif
                                <div class="text-xs text-news-muted mt-2">
                                    Bergabung: {{ $article->author->created_at ? $article->author->created_at->format('d-m-Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if(in_array($article->status, ['published', 'archived'], true))
                <div class="bg-white border border-news-line overflow-hidden">
                    <div class="px-6 py-4 border-b border-news-line bg-news-paper">
                        <h3 class="text-lg font-semibold text-news-ink">Moderasi Penayangan</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <p class="text-sm text-news-muted">Admin tidak mengubah konten penulis. Untuk konten bermasalah, tangguhkan penayangan.</p>
                        <button type="button" onclick="showSuspendModal()" class="w-full inline-flex items-center justify-center px-4 py-3 bg-news-accent text-white text-sm font-semibold hover:bg-news-ink transition-colors">
                            <i class="fas fa-pause-circle mr-2"></i>
                            Tangguhkan Penayangan
                        </button>
                    </div>
                </div>
                @elseif($article->status === 'suspended')
                <div class="bg-white border border-news-line overflow-hidden">
                    <div class="px-6 py-4 border-b border-news-line bg-news-paper">
                        <h3 class="text-lg font-semibold text-news-ink">Ditangguhkan</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($article->suspension_reason)
                            <p class="text-sm text-news-ink"><span class="font-medium">Alasan:</span> {{ $article->suspension_reason }}</p>
                        @endif
                        <form action="{{ route('admin.articles.unsuspend', $article) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full btn-primary py-3"
                                    onclick="return confirm('Pulihkan penayangan artikel ini?')">
                                <i class="fas fa-play-circle mr-2"></i>
                                Pulihkan Penayangan
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(in_array($article->status, ['published', 'archived'], true))
<div id="suspendModal" class="fixed inset-0 bg-news-ink/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-news-ink">Tangguhkan Penayangan</h3>
            <button type="button" onclick="closeSuspendModal()" class="text-news-muted hover:text-news-ink">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.articles.suspend', $article) }}" method="POST">
            @csrf
            <label for="suspendReason" class="block text-sm font-medium text-news-ink mb-2">Alasan</label>
            <textarea id="suspendReason" name="reason" rows="4" required
                      class="w-full px-3 py-2 border border-news-line rounded-md"
                      placeholder="Jelaskan alasan penangguhan..."></textarea>
            <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                <button type="button" onclick="closeSuspendModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Tangguhkan</button>
            </div>
        </form>
    </div>
</div>
<script>
function showSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}
function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
    document.getElementById('suspendReason').value = '';
}
</script>
@endif
@endsection
