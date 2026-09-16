@extends('layouts.admin-simple')

@section('title', 'Detail Artikel - Admin Panel')
@section('page-title', 'Detail Artikel')
@section('page-subtitle', $article->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-news-line overflow-hidden">
        <!-- Article Header -->
        <div class="px-6 py-4 border-b border-news-line bg-news-paper">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-news-ink">{{ $article->title }}</h1>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-news-muted">
                        <span class="flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            <span class="font-medium text-news-ink">{{ $article->author->name ?? 'Sistem' }}</span>
                            @if($article->author)
                                <x-user-role-badge :user="$article->author" size="xs" class="ml-2" />
                            @endif
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $article->created_at->format('d M Y, H:i') }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-eye mr-1"></i>
                            {{ number_format($article->views) }} views
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @if($article->is_featured)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800">
                            <i class="fas fa-star mr-1"></i>Featured
                        </span>
                    @endif
                    @if($article->is_breaking)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-news-accent">
                            <i class="fas fa-bolt mr-1"></i>Breaking
                        </span>
                    @endif
                    @if($article->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            <i class="fas fa-check-circle mr-1"></i>Published
                        </span>
                    @elseif($article->status === 'archived')
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-news-paper text-news-ink">
                            <i class="fas fa-archive mr-1"></i>Archived
                        </span>
                    @elseif($article->status === 'suspended')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-800">
                            <i class="fas fa-pause-circle mr-1"></i>Ditangguhkan
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800">
                            <i class="fas fa-edit mr-1"></i>Draft
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Article Content -->
        <div class="p-6">
            <!-- Featured Image -->
            @if($article->featured_image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $article->featured_image) }}" 
                     alt="{{ $article->title }}" 
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif

            <!-- Article Meta -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-news-paper p-4 rounded-lg">
                    <h3 class="font-semibold text-news-ink mb-2">Kategori</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                        {{ $article->category->name ?? 'Tidak ada kategori' }}
                    </span>
                </div>
                <div class="bg-news-paper p-4 rounded-lg">
                    <h3 class="font-semibold text-news-ink mb-2">Tanggal Publikasi</h3>
                    <p class="text-sm text-news-muted">
                        {{ $article->published_at ? $article->published_at->format('d M Y, H:i') : 'Belum dipublikasi' }}
                    </p>
                </div>
            </div>

            <!-- Tags -->
            @if($article->tags->count() > 0)
            <div class="mb-6">
                <h3 class="font-semibold text-news-ink mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Excerpt -->
            <div class="mb-6">
                <h3 class="font-semibold text-news-ink mb-2">Ringkasan</h3>
                <p class="text-news-ink leading-relaxed">{{ $article->excerpt }}</p>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <h3 class="font-semibold text-news-ink mb-2">Konten</h3>
                <div class="prose max-w-none article-content">
                    {!! $article->content !!}
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
            </div>

            <!-- Comments -->
            @if($article->comments->count() > 0)
            <div class="mb-6">
                <h3 class="font-semibold text-news-ink mb-4">Komentar ({{ $article->comments->count() }})</h3>
                <div class="space-y-4">
                    @foreach($article->comments->take(5) as $comment)
                    <div class="bg-news-paper p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-news-ink">{{ $comment->name }}</h4>
                                <p class="text-sm text-news-muted">{{ $comment->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            @if($comment->is_approved)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-800">
                                    Pending
                                </span>
                            @endif
                        </div>
                        <p class="text-news-ink">{{ $comment->content }}</p>
                    </div>
                    @endforeach
                    @if($article->comments->count() > 5)
                        <p class="text-sm text-news-muted text-center">
                            Dan {{ $article->comments->count() - 5 }} komentar lainnya...
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-news-line bg-news-paper">
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    @can('update', $article)
                    <form action="{{ route('admin.articles.toggle-featured', $article) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $article->is_featured ? 'bg-amber-50 text-amber-800 hover:bg-yellow-200' : 'bg-news-paper text-news-ink hover:bg-news-paper' }}">
                            <i class="fas fa-star mr-1"></i>
                            {{ $article->is_featured ? 'Hapus Featured' : 'Tandai Featured' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $article->is_breaking ? 'bg-red-50 text-news-accent hover:bg-red-200' : 'bg-news-paper text-news-ink hover:bg-news-paper' }}">
                            <i class="fas fa-bolt mr-1"></i>
                            {{ $article->is_breaking ? 'Hapus Breaking' : 'Tandai Breaking' }}
                        </button>
                    </form>
                    @else
                    <p class="text-sm text-news-muted self-center">
                        Artikel penulis — admin tidak mengubah konten; gunakan tangguhkan penayangan atau antrean review (jika pending).
                    </p>
                    @endcan
                </div>
                <div class="flex space-x-2 flex-wrap gap-2 justify-end">
                    @can('update', $article)
                    <a href="{{ route('admin.articles.edit', $article) }}" 
                       class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    @if($article->status !== 'archived')
                    <form action="{{ route('admin.articles.archive', $article) }}" method="POST" class="inline" 
                          onsubmit="return window.pesbarConfirmForm(event, 'Apakah Anda yakin ingin mengarsipkan artikel ini?')">
                        @csrf
                        <button type="submit" 
                                class="bg-news-ink text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors">
                            <i class="fas fa-archive mr-1"></i>Arsipkan
                        </button>
                    </form>
                    @endif
                    @endcan
                    @can('delete', $article)
                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" 
                          onsubmit="return window.pesbarConfirmForm(event, 'Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-news-accent text-white px-4 py-2 hover:bg-news-ink transition-colors">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>
                    </form>
                    @endcan
                    @can('suspend', $article)
                        @if(in_array($article->status, ['published', 'archived'], true))
                            <button type="button" onclick="document.getElementById('suspendModalShow').classList.remove('hidden')"
                                    class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                                <i class="fas fa-pause-circle mr-1"></i>Tangguhkan
                            </button>
                        @elseif($article->status === 'suspended')
                            <form action="{{ route('admin.articles.unsuspend', $article) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Pulihkan penayangan artikel ini?')">
                                @csrf
                                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">
                                    <i class="fas fa-play-circle mr-1"></i>Pulihkan
                                </button>
                            </form>
                        @endif
                    @endcan
                    <a href="{{ $article->publicUrl() }}" 
                       class="btn-secondary px-4 py-2" target="_blank">
                        <i class="fas fa-external-link-alt mr-1"></i>Lihat
                    </a>
                    <a href="{{ route('admin.articles.index') }}" 
                       class="bg-news-ink text-white px-4 py-2 hover:bg-news-accent transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@can('suspend', $article)
@if(in_array($article->status, ['published', 'archived'], true))
<div id="suspendModalShow" class="fixed inset-0 bg-news-ink/50 hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 bg-white shadow-lg">
        <h3 class="text-lg font-medium text-news-ink mb-4">Tangguhkan Penayangan</h3>
        <form action="{{ route('admin.articles.suspend', $article) }}" method="POST">
            @csrf
            <textarea name="reason" rows="4" required class="w-full border border-news-line px-3 py-2 mb-4"
                      placeholder="Alasan penangguhan..."></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('suspendModalShow').classList.add('hidden')" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Tangguhkan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endcan
@endsection
