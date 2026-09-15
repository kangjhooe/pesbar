@extends('layouts.public')

@section('title', \App\Helpers\SeoHelper::generateTitle($article->title))
@section('description', \App\Helpers\SeoHelper::generateDescription($article->excerpt, $article->content))
@section('keywords', \App\Helpers\SeoHelper::generateKeywords([$article->category->name ?? '']))

@section('og:title', $article->title)
@section('og:description', \App\Helpers\SeoHelper::generateDescription($article->excerpt, $article->content))
@section('og:image', $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg'))
@section('og:url', $article->publicUrl())

@section('twitter:title', $article->title)
@section('twitter:description', \App\Helpers\SeoHelper::generateDescription($article->excerpt, $article->content))
@section('twitter:image', $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg'))

@section('canonical', $article->publicUrl())

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-news-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-news-accent transition-colors">Beranda</a>
        @if($article->category)
        <span aria-hidden="true" class="text-news-line">/</span>
        <a href="{{ route('categories.show', $article->category) }}" class="hover:text-news-accent transition-colors break-words">{{ $article->category->name }}</a>
        @endif
        <span aria-hidden="true" class="text-news-line">/</span>
        <span class="text-news-ink font-medium break-words line-clamp-1">{{ $article->title }}</span>
    </nav>

    @if(session('success'))
    <div class="mb-6 border border-green-600 bg-green-50 text-green-800 px-4 py-3 text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 border border-news-accent bg-red-50 text-red-800 px-4 py-3 text-sm">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 border border-news-accent bg-red-50 text-red-800 px-4 py-3 text-sm">
        <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan:</p>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left sidebar --}}
        <aside class="lg:col-span-3 order-3 lg:order-1 space-y-8" aria-label="Sidebar kiri">
            @php
                $popularArticles = \App\Models\Article::published()->popular()->take(5)->get();
            @endphp
            <div class="border border-news-line">
                <div class="bg-news-ink text-white px-4 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Terpopuler</h2>
                </div>
                <ul class="divide-y divide-news-line">
                    @forelse($popularArticles as $popularArticle)
                    <li class="flex gap-3 px-4 py-3">
                        <a href="{{ $popularArticle->publicUrl() }}" class="w-16 h-16 shrink-0 overflow-hidden bg-news-line">
                            <img
                                src="{{ $popularArticle->featured_image ? asset('storage/' . $popularArticle->featured_image) : asset('images/default-news.jpg') }}"
                                alt=""
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        </a>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold leading-snug text-news-ink line-clamp-2">
                                <a href="{{ $popularArticle->publicUrl() }}" class="hover:text-news-accent transition-colors" title="{{ $popularArticle->title }}">
                                    {{ $popularArticle->title }}
                                </a>
                            </h3>
                            <p class="mt-1 text-[11px] text-news-muted">{{ number_format($popularArticle->views) }} views</p>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-6 text-sm text-news-muted">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>

            @php
                $allCategories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
            @endphp
            @if($allCategories->count() > 0)
            <div class="border border-news-line">
                <div class="bg-news-ink text-white px-4 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Kategori</h2>
                </div>
                <ul class="divide-y divide-news-line">
                    @foreach($allCategories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}"
                           class="flex items-center justify-between px-4 py-3 text-sm text-news-ink hover:text-news-accent transition-colors">
                            <span>{{ $category->name }}</span>
                            <span class="text-[11px] text-news-muted tabular-nums">{{ $category->publishedArticles()->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </aside>

        {{-- Main article --}}
        <div class="lg:col-span-6 order-1 lg:order-2 min-w-0">
            <article>
                <header class="border-b border-news-line pb-5 mb-5">
                    @if($article->category)
                    <a href="{{ route('categories.show', $article->category) }}"
                       class="text-[11px] font-bold uppercase tracking-widest text-news-accent hover:underline">
                        {{ $article->category->name }}
                    </a>
                    @endif

                    <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-news-ink leading-tight mt-2">
                        {{ $article->title }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-news-muted">
                        <span>
                            @if($article->author && $article->author->isPenulis() && $article->author->username)
                                <a href="{{ route('penulis.public-profile', $article->author->username) }}" class="font-semibold text-news-ink hover:text-news-accent">
                                    {{ $article->author->name ?? 'Admin' }}
                                </a>
                            @else
                                <span class="font-semibold text-news-ink">{{ $article->author->name ?? 'Admin' }}</span>
                            @endif
                            @if($article->author)
                                <x-user-role-badge :user="$article->author" size="xs" />
                            @endif
                        </span>
                        <span aria-hidden="true">·</span>
                        <time datetime="{{ optional($article->published_at)->toIso8601String() }}">
                            {{ $article->published_at ? $article->published_at->format('d M Y, H:i') . ' WIB' : 'Belum dipublikasi' }}
                        </time>
                        <span aria-hidden="true">·</span>
                        <span>Bacaan ± {{ $article->readingTimeMinutes() }} menit</span>
                        <span aria-hidden="true">·</span>
                        <span>{{ number_format($article->views) }} views</span>
                    </div>

                    @auth
                    <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-news-line">
                        <button onclick="toggleBookmark({{ $article->id }})"
                                id="bookmark-btn-{{ $article->id }}"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm border transition-colors {{ auth()->user()->hasBookmarked($article) ? 'bg-yellow-50 text-yellow-800 border-yellow-300' : 'bg-white text-news-ink border-news-line hover:border-news-ink' }}">
                            <i class="fas fa-bookmark"></i>
                            <span id="bookmark-text-{{ $article->id }}">
                                {{ auth()->user()->hasBookmarked($article) ? 'Bookmarked' : 'Bookmark' }}
                            </span>
                        </button>
                        @if($article->author && $article->author->id !== auth()->id())
                        <button onclick="toggleFollow({{ $article->author->id }})"
                                id="follow-btn-{{ $article->author->id }}"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm border transition-colors {{ auth()->user()->isFollowing($article->author) ? 'bg-news-ink text-white border-news-ink' : 'bg-white text-news-ink border-news-line hover:border-news-accent hover:text-news-accent' }}">
                            <i class="fas {{ auth()->user()->isFollowing($article->author) ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                            <span id="follow-text-{{ $article->author->id }}">
                                {{ auth()->user()->isFollowing($article->author) ? 'Mengikuti' : 'Ikuti Penulis' }}
                            </span>
                        </button>
                        @endif
                    </div>
                    @endauth
                </header>

                @if($article->featured_image)
                <div class="aspect-video bg-news-line mb-6 overflow-hidden">
                    <img src="{{ asset('storage/' . $article->featured_image) }}"
                         alt="{{ $article->title }}"
                         class="w-full h-full object-cover">
                </div>
                @endif

                <div class="prose prose-lg max-w-none article-content text-news-ink">
                    {!! $article->formattedContent() !!}
                </div>

                <footer class="mt-8 pt-6 border-t border-news-line">
                    @if($article->tags && $article->tags->count() > 0)
                    <div class="mb-5">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-news-muted mb-2">Tag</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                            <span class="text-xs text-news-ink border border-news-line px-2.5 py-1">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Bagikan</span>
                            <div class="flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                   target="_blank" rel="noopener"
                                   class="w-9 h-9 border border-news-line text-news-ink flex items-center justify-center hover:bg-news-ink hover:text-white transition-colors touch-target"
                                   aria-label="Bagikan ke Facebook">
                                    <i class="fab fa-facebook-f text-xs"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                                   target="_blank" rel="noopener"
                                   class="w-9 h-9 border border-news-line text-news-ink flex items-center justify-center hover:bg-news-ink hover:text-white transition-colors touch-target"
                                   aria-label="Bagikan ke X">
                                    <i class="fab fa-twitter text-xs"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->url()) }}"
                                   target="_blank" rel="noopener"
                                   class="w-9 h-9 border border-news-line text-news-ink flex items-center justify-center hover:bg-news-ink hover:text-white transition-colors touch-target"
                                   aria-label="Bagikan ke WhatsApp">
                                    <i class="fab fa-whatsapp text-xs"></i>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('articles.index') }}"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-news-ink hover:text-news-accent transition-colors">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Kembali ke Berita
                        </a>
                    </div>
                </footer>
            </article>

            <section class="mt-10 border-t-2 border-news-ink pt-6" id="comments-section">
                <h2 class="font-display text-xl md:text-2xl font-bold text-news-ink mb-6">
                    Komentar (<span id="comments-count">{{ $article->approvedComments->where('parent_id', null)->count() }}</span>)
                </h2>

                <div class="mb-8 border-b border-news-line pb-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-news-muted mb-4">Tulis Komentar</h3>

                    @auth
                        <form id="comment-form" action="{{ route('comments.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="article_id" value="{{ $article->id }}">
                            <input type="hidden" name="parent_id" id="reply-to-id" value="">

                            <div class="mb-2 px-3 py-2 border border-news-line bg-news-paper text-sm text-news-ink">
                                Berkomentar sebagai <strong>{{ auth()->user()->name }}</strong>
                            </div>

                            <div id="reply-indicator" class="hidden mb-3 px-3 py-2 border border-yellow-400 bg-yellow-50">
                                <div class="flex items-center justify-between text-sm text-yellow-900">
                                    <span>Membalas komentar dari <strong id="reply-to-name"></strong></span>
                                    <button type="button" onclick="cancelReply()" class="hover:text-news-accent" aria-label="Batal balas">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="comment" class="block text-sm font-medium text-news-ink mb-2">
                                    Komentar <span class="text-news-accent">*</span>
                                </label>
                                <textarea name="comment"
                                          id="comment"
                                          rows="5"
                                          required
                                          maxlength="1000"
                                          class="w-full px-3 py-2 border border-news-line text-news-ink focus:outline-none focus:ring-2 focus:ring-news-accent focus:border-transparent"
                                          placeholder="Tulis komentar Anda di sini... (Maksimal 1000 karakter)"></textarea>
                                <div class="flex justify-end mt-1">
                                    <span class="text-[11px] text-news-muted"><span id="char-count">0</span>/1000</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit"
                                        id="submit-comment-btn"
                                        class="bg-news-ink text-white px-6 py-2.5 text-sm font-semibold hover:bg-news-accent transition-colors inline-flex items-center gap-2">
                                    <i class="fas fa-paper-plane text-xs"></i>
                                    Kirim Komentar
                                </button>
                                <button type="button"
                                        id="cancel-reply-btn"
                                        onclick="cancelReply()"
                                        class="hidden px-5 py-2.5 border border-news-line text-news-ink text-sm font-medium hover:border-news-ink transition-colors">
                                    Batal
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="border border-news-line px-5 py-8 text-center">
                            <p class="text-news-ink mb-4 font-medium">Login terlebih dahulu untuk berkomentar.</p>
                            <a href="{{ route('login') }}"
                               class="inline-block bg-news-ink text-white px-5 py-2.5 text-sm font-semibold hover:bg-news-accent transition-colors">
                                Login Sekarang
                            </a>
                            <p class="text-sm text-news-muted mt-3">
                                Belum punya akun?
                                <a href="{{ route('register') }}" class="text-news-accent font-medium hover:underline">Daftar di sini</a>
                            </p>
                        </div>
                    @endauth
                </div>

                <div id="comments-list" class="space-y-4">
                    @if($article->approvedComments->where('parent_id', null)->count() > 0)
                        @foreach($article->approvedComments->where('parent_id', null) as $comment)
                            @include('comments.comment-item', ['comment' => $comment, 'level' => 0])
                        @endforeach
                    @else
                        <div class="text-center py-12 border border-news-line">
                            <p class="text-news-muted">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Right sidebar --}}
        <aside class="lg:col-span-3 order-2 lg:order-3 space-y-8" aria-label="Sidebar kanan">
            @if($relatedArticles->count() > 0)
            <div class="border border-news-line">
                <div class="bg-news-ink text-white px-4 py-2.5">
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Berita Terkait</h2>
                </div>
                <ul class="divide-y divide-news-line">
                    @foreach($relatedArticles as $relatedArticle)
                    <li class="flex gap-3 px-4 py-3">
                        <a href="{{ $relatedArticle->publicUrl() }}" class="w-16 h-16 shrink-0 overflow-hidden bg-news-line">
                            <img
                                src="{{ $relatedArticle->featured_image ? asset('storage/' . $relatedArticle->featured_image) : asset('images/default-news.jpg') }}"
                                alt=""
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        </a>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold leading-snug text-news-ink line-clamp-2">
                                <a href="{{ $relatedArticle->publicUrl() }}" class="hover:text-news-accent transition-colors" title="{{ $relatedArticle->title }}">
                                    {{ $relatedArticle->title }}
                                </a>
                            </h3>
                            <time class="block mt-1 text-[11px] text-news-muted">
                                {{ $relatedArticle->published_at ? $relatedArticle->published_at->format('d M Y') : 'Belum dipublikasi' }}
                            </time>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @include('widgets.weather')
            @include('widgets.prayer')
            @include('widgets.poll')
            @include('widgets.contact-important')
            @include('widgets.newsletter')
        </aside>
    </div>
</div>
@section('structured-data')
<script type="application/ld+json">
{!! json_encode(\App\Helpers\SeoHelper::generateArticleStructuredData($article), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('scripts')
<script>
// Toggle Bookmark
function toggleBookmark(articleId) {
    @guest
    window.location.href = '{{ route("login") }}';
    return;
    @endguest

    const btn = document.getElementById(`bookmark-btn-${articleId}`);
    const text = document.getElementById(`bookmark-text-${articleId}`);
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    
    fetch(`/articles/${articleId}/bookmark`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.bookmarked) {
                btn.classList.remove('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-ink');
                btn.classList.add('bg-yellow-50', 'text-yellow-800', 'border-yellow-300');
                text.textContent = 'Bookmarked';
            } else {
                btn.classList.remove('bg-yellow-50', 'text-yellow-800', 'border-yellow-300');
                btn.classList.add('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-ink');
                text.textContent = 'Bookmark';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// Toggle Follow
function toggleFollow(userId) {
    @guest
    window.location.href = '{{ route("login") }}';
    return;
    @endguest

    const btn = document.getElementById(`follow-btn-${userId}`);
    const text = document.getElementById(`follow-text-${userId}`);
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    
    fetch(`/users/${userId}/follow`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.following) {
                btn.classList.remove('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-accent', 'hover:text-news-accent');
                btn.classList.add('bg-news-ink', 'text-white', 'border-news-ink');
                icon.classList.remove('fa-user-plus');
                icon.classList.add('fa-user-check');
                text.textContent = 'Mengikuti';
            } else {
                btn.classList.remove('bg-news-ink', 'text-white', 'border-news-ink');
                btn.classList.add('bg-white', 'text-news-ink', 'border-news-line', 'hover:border-news-accent', 'hover:text-news-accent');
                icon.classList.remove('fa-user-check');
                icon.classList.add('fa-user-plus');
                text.textContent = 'Ikuti Penulis';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// CRITICAL: Define toggleLike FIRST before anything else to ensure it's always available
window.toggleLike = window.toggleLike || function(commentId, isLike) {
        if (!commentId) {
            console.error('Comment ID is required');
            return;
        }
        
        @guest
        window.location.href = '{{ route("login") }}';
        return;
        @endguest

        // Get buttons
        const likeBtn = document.getElementById(`like-btn-${commentId}`);
        const dislikeBtn = document.getElementById(`dislike-btn-${commentId}`);
        
        // Check if buttons exist
        if (!likeBtn || !dislikeBtn) {
            console.error('Like/Dislike buttons not found for comment:', commentId);
            return;
        }
        
        // Disable button during request
        likeBtn.disabled = true;
        dislikeBtn.disabled = true;
        likeBtn.style.pointerEvents = 'none';
        dislikeBtn.style.pointerEvents = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        fetch(`/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ is_like: isLike })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const likesCountEl = document.getElementById(`likes-count-${commentId}`);
                const dislikesCountEl = document.getElementById(`dislikes-count-${commentId}`);
                
                if (likesCountEl) likesCountEl.textContent = data.likes_count || 0;
                if (dislikesCountEl) dislikesCountEl.textContent = data.dislikes_count || 0;
                
                // Update like button style
                if (likeBtn) {
                    if (data.is_liked) {
                        likeBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-green-50', 'hover:text-green-700', 'hover:border-green-200');
                        likeBtn.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
                    } else {
                        likeBtn.classList.remove('bg-green-50', 'text-green-700', 'border-green-200');
                        likeBtn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-green-50', 'hover:text-green-700', 'hover:border-green-200');
                    }
                }
                
                // Update dislike button style
                if (dislikeBtn) {
                    if (data.is_disliked) {
                        dislikeBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-red-50', 'hover:text-red-700', 'hover:border-red-200');
                        dislikeBtn.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
                    } else {
                        dislikeBtn.classList.remove('bg-red-50', 'text-red-700', 'border-red-200');
                        dislikeBtn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-red-50', 'hover:text-red-700', 'hover:border-red-200');
                    }
                }
            } else {
                if (typeof showNotification === 'function') {
                    showNotification(data.error || 'Terjadi kesalahan saat memproses like/dislike.', 'error');
                } else {
                    alert(data.error || 'Terjadi kesalahan saat memproses like/dislike.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMsg = error.error || error.message || 'Terjadi kesalahan saat memproses like/dislike.';
            if (typeof showNotification === 'function') {
                showNotification(errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
        })
        .finally(() => {
            if (likeBtn) {
                likeBtn.disabled = false;
                likeBtn.style.pointerEvents = 'auto';
            }
            if (dislikeBtn) {
                dislikeBtn.disabled = false;
                dislikeBtn.style.pointerEvents = 'auto';
            }
        });
};

// Event delegation as fallback for dynamically created buttons (only if onclick fails)
// This will only trigger if the onclick handler didn't work
setTimeout(function() {
    document.addEventListener('click', function(e) {
        const button = e.target.closest('[id^="like-btn-"]') || e.target.closest('[id^="dislike-btn-"]');
        if (button && button.hasAttribute('onclick')) {
            // If button has onclick attribute, skip delegation (onclick should handle it)
            return;
        }
        
        if (button) {
            // Extract comment ID and action from button ID
            const buttonId = button.id;
            const match = buttonId.match(/(like|dislike)-btn-(\d+)/);
            if (match && typeof window.toggleLike === 'function') {
                const isLike = match[1] === 'like';
                const commentId = parseInt(match[2]);
                e.preventDefault();
                e.stopPropagation();
                window.toggleLike(commentId, isLike);
            }
        }
    }, 100); // Small delay to ensure onclick handlers are attached first
}, 0);

// Reply to comment
window.replyToComment = function(commentId, commenterName) {
    if (!commentId) {
        console.error('Comment ID is required');
        return;
    }
    
    const replyToId = document.getElementById('reply-to-id');
    const replyToName = document.getElementById('reply-to-name');
    const replyIndicator = document.getElementById('reply-indicator');
    const cancelBtn = document.getElementById('cancel-reply-btn');
    const commentForm = document.getElementById('comment-form');
    const commentTextarea = document.getElementById('comment');
    
    if (!replyToId || !replyToName || !replyIndicator || !cancelBtn || !commentForm) {
        console.error('Required elements not found');
        return;
    }
    
    replyToId.value = commentId;
    replyToName.textContent = commenterName || 'User';
    replyIndicator.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
    
    // Scroll to comment form
    commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
    if (commentTextarea) {
        setTimeout(() => commentTextarea.focus(), 300);
    }
};

// Cancel reply
window.cancelReply = function() {
    const replyToId = document.getElementById('reply-to-id');
    const replyIndicator = document.getElementById('reply-indicator');
    const cancelBtn = document.getElementById('cancel-reply-btn');
    
    if (replyToId) replyToId.value = '';
    if (replyIndicator) replyIndicator.classList.add('hidden');
    if (cancelBtn) cancelBtn.classList.add('hidden');
};

// Edit comment
window.editComment = function(commentId, commentText) {
    const modal = document.getElementById(`edit-comment-modal-${commentId}`);
    const textarea = document.getElementById(`edit-comment-text-${commentId}`);
    const charCount = document.getElementById(`edit-char-count-${commentId}`);
    
    if (modal && textarea) {
        textarea.value = commentText;
        if (charCount) charCount.textContent = commentText.length;
        modal.classList.remove('hidden');
        
        // Character counter for edit
        textarea.addEventListener('input', function() {
            if (charCount) charCount.textContent = this.value.length;
        });
        
        // Handle form submission
        const form = document.getElementById(`edit-comment-form-${commentId}`);
        if (form) {
            // Remove existing listener to prevent duplicates
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            
            newForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = newForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                
                fetch(newForm.action, {
                    method: 'PUT',
                    body: new FormData(newForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const commentTextEl = document.querySelector(`#comment-${commentId} .comment-text p`);
                        if (commentTextEl) {
                            commentTextEl.textContent = data.comment.comment;
                        }
                        window.closeEditModal(commentId);
                        if (typeof showNotification === 'function') {
                            showNotification('Komentar berhasil diperbarui.', 'success');
                        } else {
                            alert('Komentar berhasil diperbarui.');
                        }
                    } else {
                        if (typeof showNotification === 'function') {
                            showNotification(data.error || 'Terjadi kesalahan saat memperbarui komentar.', 'error');
                        } else {
                            alert(data.error || 'Terjadi kesalahan saat memperbarui komentar.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showNotification === 'function') {
                        showNotification('Terjadi kesalahan saat memperbarui komentar.', 'error');
                    } else {
                        alert('Terjadi kesalahan saat memperbarui komentar.');
                    }
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    }
};

// Close edit modal
window.closeEditModal = function(commentId) {
    const modal = document.getElementById(`edit-comment-modal-${commentId}`);
    if (modal) {
        modal.classList.add('hidden');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Widget auto-refresh disembunyikan sementara
    // setInterval(function() { updateWidgetData(); }, 30 * 60 * 1000);
    // updateWidgetData();
    
    function updateWidgetData() {
        return; // widgets hidden
        // Update weather widget
        fetch('/api/widgets/weather')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateWeatherWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Weather update failed:', error);
            });
        
        // Update prayer times widget
        fetch('/api/widgets/prayer-times')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePrayerTimesWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Prayer times update failed:', error);
            });
        
        // Update maritime widget
        fetch('/api/widgets/maritime')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateMaritimeWidget(data.data);
                }
            })
            .catch(error => {
                console.log('Maritime update failed:', error);
            });
    }
    
    function updateWeatherWidget(weatherData) {
        const widget = document.getElementById('weather-widget');
        const weatherIcon = document.querySelector('.weather-widget');
        const weatherIconLarge = document.querySelector('.weather-widget-large');
        const weatherTemp = document.querySelector('.weather-temp');
        const weatherCondition = document.querySelector('.weather-condition');
        const weatherLocation = document.querySelector('.weather-location');
        const weatherUpdate = document.querySelector('.weather-update');
        
        // Add updating animation
        if (widget) {
            widget.classList.add('updating');
            setTimeout(() => widget.classList.remove('updating'), 500);
        }
        
        // Animate temperature change
        if (weatherTemp) {
            weatherTemp.classList.add('updating');
            setTimeout(() => {
                weatherTemp.textContent = weatherData.temperature + '°C';
                setTimeout(() => weatherTemp.classList.remove('updating'), 500);
            }, 100);
        }
        
        if (weatherIcon) {
            weatherIcon.style.opacity = '0';
            setTimeout(() => {
                weatherIcon.className = 'weather-widget ' + weatherData.icon + ' text-yellow-500 mr-2';
                weatherIcon.style.opacity = '1';
            }, 200);
        }
        
        if (weatherIconLarge) {
            weatherIconLarge.style.opacity = '0';
            setTimeout(() => {
                weatherIconLarge.className = 'weather-widget-large ' + weatherData.icon;
                weatherIconLarge.style.opacity = '1';
            }, 200);
        }
        
        if (weatherCondition) {
            weatherCondition.style.opacity = '0';
            setTimeout(() => {
                weatherCondition.textContent = weatherData.condition;
                weatherCondition.style.opacity = '1';
            }, 300);
        }
        
        if (weatherLocation) weatherLocation.textContent = weatherData.location;
        if (weatherUpdate) {
            weatherUpdate.style.opacity = '0';
            setTimeout(() => {
                weatherUpdate.textContent = 'Update: ' + weatherData.updated_at;
                weatherUpdate.style.opacity = '1';
            }, 400);
        }
        
        // Update forecast
        if (weatherData.forecast && weatherData.forecast.length > 0) {
            updateWeatherForecast(weatherData.forecast);
        }
    }
    
    function updateWeatherForecast(forecastData) {
        const forecastContainer = document.querySelector('.weather-forecast-container');
        if (!forecastContainer) return;
        
        // Fade out existing items
        const existingItems = forecastContainer.querySelectorAll('div');
        existingItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
        });
        
        setTimeout(() => {
            forecastContainer.innerHTML = '';
            
            forecastData.forEach((forecast, index) => {
                const tempDisplay = forecast.temp_min && forecast.temp_max 
                    ? `${forecast.temp_min}-${forecast.temp_max}°C`
                    : `${forecast.temperature}°C`;
                
                const forecastItem = document.createElement('div');
                forecastItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg hover:bg-gray-100 transition';
                forecastItem.style.opacity = '0';
                forecastItem.style.transform = 'translateY(20px)';
                forecastItem.innerHTML = `
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="text-yellow-500 text-lg">
                            <i class="${forecast.icon || 'fas fa-sun'}"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-800">${forecast.day || 'N/A'}</div>
                            <div class="text-xs text-gray-500">${forecast.date || ''}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-gray-800">${tempDisplay}</div>
                        <div class="text-xs text-gray-600">${forecast.condition || 'Cerah'}</div>
                    </div>
                `;
                forecastContainer.appendChild(forecastItem);
                
                // Animate in
                setTimeout(() => {
                    forecastItem.style.transition = 'all 0.5s ease';
                    forecastItem.style.opacity = '1';
                    forecastItem.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }, 300);
    }
    
    function updatePrayerTimesWidget(prayerData) {
        const widget = document.getElementById('prayer-times-widget');
        const prayerLocation = document.querySelector('.prayer-location');
        const prayerDate = document.querySelector('.prayer-date');
        const prayerUpdate = document.querySelector('.prayer-update');
        
        // Add updating animation
        if (widget) {
            widget.classList.add('updating');
            setTimeout(() => widget.classList.remove('updating'), 500);
        }
        
        if (prayerLocation) {
            prayerLocation.style.opacity = '0';
            setTimeout(() => {
                prayerLocation.textContent = prayerData.location;
                prayerLocation.style.opacity = '1';
            }, 200);
        }
        
        if (prayerDate) {
            prayerDate.style.opacity = '0';
            setTimeout(() => {
                prayerDate.textContent = new Date(prayerData.date).toLocaleDateString('id-ID');
                prayerDate.style.opacity = '1';
            }, 300);
        }
        
        if (prayerUpdate) {
            prayerUpdate.style.opacity = '0';
            setTimeout(() => {
                prayerUpdate.textContent = 'Update: ' + prayerData.updated_at;
                prayerUpdate.style.opacity = '1';
            }, 400);
        }
        
        // Update prayer times with animation
        const prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        prayers.forEach((prayer, index) => {
            const element = document.querySelector(`.prayer-${prayer}`);
            if (element && prayerData.prayers[prayer]) {
                element.classList.add('updating');
                setTimeout(() => {
                    element.textContent = prayerData.prayers[prayer];
                    setTimeout(() => element.classList.remove('updating'), 500);
                }, 100 + (index * 50));
            }
        });
    }
    
    function updateMaritimeWidget(maritimeData) {
        const widget = document.getElementById('maritime-widget');
        const waveHeight = document.querySelector('.maritime-wave-height');
        const waveCategory = document.querySelector('.maritime-wave-category');
        const tideStatus = document.querySelector('.maritime-tide-status');
        const tideIcon = document.querySelector('.maritime-tide-icon');
        const nextHighTide = document.querySelector('.maritime-next-high-tide');
        const nextLowTide = document.querySelector('.maritime-next-low-tide');
        const windSpeed = document.querySelector('.maritime-wind-speed');
        const windDirection = document.querySelector('.maritime-wind-direction');
        const location = document.querySelector('.maritime-location');
        const update = document.querySelector('.maritime-update');
        
        // Add updating animation
        if (widget) {
            widget.classList.add('loading');
            setTimeout(() => widget.classList.remove('loading'), 500);
        }
        
        // Update wave height
        if (waveHeight) {
            waveHeight.classList.add('updating');
            setTimeout(() => {
                waveHeight.textContent = maritimeData.wave_height || '1.2';
                setTimeout(() => waveHeight.classList.remove('updating'), 500);
            }, 200);
        }
        
        // Update wave category
        if (waveCategory && maritimeData.wave_height_category) {
            waveCategory.textContent = maritimeData.wave_height_category;
            // Update category color
            const category = maritimeData.wave_height_category;
            waveCategory.className = 'maritime-wave-category px-2 py-1 rounded-full text-xs font-bold ' + (
                category === 'Sangat Tinggi' ? 'bg-red-100 text-red-800' : 
                category === 'Tinggi' ? 'bg-orange-100 text-orange-800' : 
                category === 'Sedang' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
            );
        }
        
        // Update wave bar animation
        const waveBar = document.querySelector('.wave-bar');
        if (waveBar && maritimeData.wave_height) {
            const height = Math.min(100, (maritimeData.wave_height / 4) * 100);
            waveBar.style.height = height + '%';
        }
        
        // Update tide status
        if (tideStatus && maritimeData.tide) {
            tideStatus.classList.add('updating');
            tideStatus.textContent = maritimeData.tide.status || 'Pasang';
            tideStatus.className = 'maritime-tide-status font-bold ' + (
                maritimeData.tide.status === 'Pasang' ? 'text-blue-600' : 'text-gray-600'
            );
            setTimeout(() => tideStatus.classList.remove('updating'), 500);
        }
        
        // Update tide icon
        if (tideIcon && maritimeData.tide) {
            tideIcon.className = 'maritime-tide-icon ' + (maritimeData.tide.icon || 'fas fa-arrow-up') + ' ' + (
                maritimeData.tide.status === 'Pasang' ? 'text-blue-600' : 'text-gray-600'
            ) + ' tide-animation';
        }
        
        // Update tide times
        if (nextHighTide && maritimeData.tide) {
            nextHighTide.textContent = maritimeData.tide.next_high_tide || '07:00';
        }
        if (nextLowTide && maritimeData.tide) {
            nextLowTide.textContent = maritimeData.tide.next_low_tide || '13:00';
        }
        
        // Update tide level indicator
        const tideLevelIndicator = document.querySelector('.tide-level-indicator');
        if (tideLevelIndicator && maritimeData.tide) {
            tideLevelIndicator.style.width = (maritimeData.tide.level || 50) + '%';
        }
        
        // Update wind speed
        if (windSpeed && maritimeData.wind_speed) {
            windSpeed.textContent = maritimeData.wind_speed + ' ';
        }
        
        // Update wind direction
        if (windDirection && maritimeData.wind_direction) {
            windDirection.textContent = maritimeData.wind_direction;
        }
        
        // Update location
        if (location && maritimeData.location) {
            location.textContent = maritimeData.location;
        }
        
        // Update timestamp
        if (update && maritimeData.updated_at) {
            update.style.opacity = '0';
            setTimeout(() => {
                update.textContent = 'Update: ' + maritimeData.updated_at;
                update.style.opacity = '1';
            }, 400);
        }
        
        // Update warnings
        if (maritimeData.warning && maritimeData.warning.length > 0) {
            updateMaritimeWarnings(maritimeData.warning);
        }
        
        // Update forecast
        if (maritimeData.forecast && maritimeData.forecast.length > 0) {
            updateMaritimeForecast(maritimeData.forecast);
        }
    }
    
    function updateMaritimeWarnings(warnings) {
        const widget = document.getElementById('maritime-widget');
        if (!widget) return;
        
        // Find or create warnings container
        let warningsContainer = widget.querySelector('.mb-4.space-y-2');
        if (!warningsContainer) {
            // Create warnings container if it doesn't exist
            const tideInfo = widget.querySelector('.bg-gradient-to-r.from-teal-50');
            if (tideInfo) {
                warningsContainer = document.createElement('div');
                warningsContainer.className = 'mb-4 space-y-2';
                tideInfo.parentNode.insertBefore(warningsContainer, tideInfo.nextSibling);
            }
        }
        
        if (warningsContainer) {
            warningsContainer.innerHTML = '';
            warnings.forEach(warning => {
                const warningDiv = document.createElement('div');
                warningDiv.className = 'p-3 rounded-lg border-l-4 warning-animation ' + (
                    warning.level === 'danger' ? 'bg-red-50 border-red-500' : 'bg-yellow-50 border-yellow-500'
                );
                warningDiv.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="${warning.icon} ${warning.level === 'danger' ? 'text-red-600' : 'text-yellow-600'}"></i>
                        <span class="text-sm font-semibold ${warning.level === 'danger' ? 'text-red-800' : 'text-yellow-800'}">${warning.message}</span>
                    </div>
                `;
                warningsContainer.appendChild(warningDiv);
            });
        }
    }
    
    function updateMaritimeForecast(forecastData) {
        const forecastContainer = document.querySelector('.maritime-forecast-container');
        if (!forecastContainer) return;
        
        // Fade out existing items
        const existingItems = forecastContainer.querySelectorAll('div');
        existingItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
        });
        
        setTimeout(() => {
            forecastContainer.innerHTML = '';
            
            forecastData.forEach((forecast, index) => {
                const forecastItem = document.createElement('div');
                forecastItem.className = 'flex items-center justify-between p-2 hover:bg-gray-50 rounded transition-colors';
                forecastItem.style.opacity = '0';
                forecastItem.style.transform = 'translateY(20px)';
                forecastItem.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <i class="${forecast.icon || 'fas fa-water'} text-lg"></i>
                        <div>
                            <div class="text-xs font-semibold text-gray-700">${forecast.day || 'N/A'}, ${forecast.date || ''}</div>
                            <div class="text-xs text-gray-500">${forecast.wave_category || 'Sedang'}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-blue-600">${forecast.wave_height || '1.2'}m</div>
                        <div class="text-xs text-gray-500">${forecast.wind_speed || '15'} km/jam</div>
                    </div>
                `;
                forecastContainer.appendChild(forecastItem);
                
                // Animate in
                setTimeout(() => {
                    forecastItem.style.transition = 'all 0.3s ease';
                    forecastItem.style.opacity = '1';
                    forecastItem.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
        }, 300);
    }

    // ========== COMMENT SYSTEM FUNCTIONALITY ==========
    
    // Character counter
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    
    if (commentTextarea && charCount) {
        commentTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 900) {
                charCount.classList.add('text-red-500', 'font-semibold');
            } else {
                charCount.classList.remove('text-red-500', 'font-semibold');
            }
        });
    }

    // AJAX Comment Submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-comment-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add comment to list immediately (semua komentar langsung disetujui)
                    addCommentToDOM(data.comment);
                    showNotification('Komentar berhasil dikirim!', 'success');
                    commentForm.reset();
                    document.getElementById('char-count').textContent = '0';
                    cancelReply();
                } else {
                    showNotification(data.error || 'Terjadi kesalahan saat mengirim komentar.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }



    // Add comment to DOM
    function addCommentToDOM(commentData) {
        const commentsList = document.getElementById('comments-list');
        const commentsCount = document.getElementById('comments-count');
        
        if (!commentsList) return;
        
        // Escape HTML untuk keamanan
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };
        
        const commentName = escapeHtml(commentData.name || 'User');
        const commentText = escapeHtml(commentData.comment || '');
        const parentId = commentData.parent_id || null;
        
        // Create comment HTML
        const commentHTML = `
            <div class="comment-item ${parentId ? 'ml-4 sm:ml-8 mt-4' : ''} mb-4 border border-news-line bg-white overflow-hidden" 
                 data-comment-id="${commentData.id}" 
                 id="comment-${commentData.id}">
                <div class="p-4 md:p-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-news-ink flex items-center justify-center text-white font-bold text-lg">
                                ${(commentName.charAt(0) || 'U').toUpperCase()}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-3 gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <h5 class="font-bold text-news-ink text-base">${commentName}</h5>
                                    </div>
                                    <div class="flex items-center gap-3 flex-wrap text-xs text-news-muted">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            <span>Baru saja</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="comment-text mb-4">
                                <p class="text-news-ink leading-relaxed whitespace-pre-wrap text-[15px]">${commentText}</p>
                            </div>
                            <div class="flex items-center gap-3 pt-3 border-t border-news-line">
                                <div class="flex items-center gap-2">
                                    <button onclick="toggleLike(${commentData.id}, true)" 
                                            class="flex items-center gap-2 px-3 py-1.5 transition-colors bg-white text-news-muted hover:text-green-700 border border-news-line hover:border-green-300"
                                            id="like-btn-${commentData.id}">
                                        <i class="fas fa-thumbs-up text-sm"></i>
                                        <span class="text-sm font-medium" id="likes-count-${commentData.id}">${commentData.likes_count || 0}</span>
                                    </button>
                                    <button onclick="toggleLike(${commentData.id}, false)" 
                                            class="flex items-center gap-2 px-3 py-1.5 transition-colors bg-white text-news-muted hover:text-red-700 border border-news-line hover:border-red-300"
                                            id="dislike-btn-${commentData.id}">
                                        <i class="fas fa-thumbs-down text-sm"></i>
                                        <span class="text-sm font-medium" id="dislikes-count-${commentData.id}">${commentData.dislikes_count || 0}</span>
                                    </button>
                                </div>
                                <button onclick="replyToComment(${commentData.id}, '${commentName.replace(/'/g, "\\'")}')" 
                                        class="flex items-center gap-2 px-3 py-1.5 text-news-accent hover:text-news-ink font-medium transition-colors border border-transparent hover:border-news-line">
                                    <i class="fas fa-reply text-sm"></i>
                                    <span class="text-sm">Balas</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Jika ini adalah reply, tambahkan ke parent comment
        if (parentId) {
            const parentComment = document.getElementById(`comment-${parentId}`);
            if (parentComment) {
                // Cari atau buat container untuk replies
                let repliesContainer = parentComment.querySelector('.replies-container');
                if (!repliesContainer) {
                    repliesContainer = document.createElement('div');
                    repliesContainer.className = 'mt-5 pt-4 border-t border-gray-100 replies-container';
                    const repliesWrapper = document.createElement('div');
                    repliesWrapper.className = 'space-y-3';
                    repliesContainer.appendChild(repliesWrapper);
                    const parentContent = parentComment.querySelector('.flex-1.min-w-0');
                    if (parentContent) {
                        parentContent.appendChild(repliesContainer);
                    }
                }
                const repliesWrapper = repliesContainer.querySelector('.space-y-3') || repliesContainer;
                repliesWrapper.insertAdjacentHTML('beforeend', commentHTML);
            } else {
                // Jika parent tidak ditemukan, tambahkan ke list utama
                commentsList.insertAdjacentHTML('afterbegin', commentHTML);
            }
        } else {
            // Remove "no comments" message if exists
            const noCommentsMsg = commentsList.querySelector('.text-center');
            if (noCommentsMsg) {
                noCommentsMsg.remove();
            }
            
            // Add new comment at the top
            commentsList.insertAdjacentHTML('afterbegin', commentHTML);
            
            // Update count hanya untuk top-level comments
            if (commentsCount) {
                const currentCount = parseInt(commentsCount.textContent) || 0;
                commentsCount.textContent = currentCount + 1;
            }
        }
        
        // Scroll to new comment
        const newComment = document.getElementById(`comment-${commentData.id}`);
        if (newComment) {
            newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const colors = {
            success: 'bg-green-100 border-green-400 text-green-700',
            error: 'bg-red-100 border-red-400 text-red-700',
            info: 'bg-blue-100 border-blue-400 text-blue-700'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3 max-w-md animate-slide-in`;
        notification.innerHTML = `
            <i class="fas ${icons[type]} text-xl"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-slide-out');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Close modals on outside click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('bg-black')) {
            e.target.classList.add('hidden');
        }
    });
});
</script>

<style>
/* Weather Widget Animations */
@keyframes rotateSun {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.9; }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes numberChange {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); color: #f39c12; }
    100% { transform: scale(1); }
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Weather Icon Animations - Applied directly to icons */
/* Universal selector for all weather icons inside the widget */
#weather-widget i[class*="fa-"],
#weather-widget .weather-widget[class*="fa-"],
#weather-widget .weather-widget-large[class*="fa-"] {
    display: inline-block !important;
}

/* Sun animation - rotate */
#weather-widget i[class*="fa-sun"]:not([class*="fa-cloud-sun"]),
#weather-widget .weather-widget[class*="fa-sun"]:not([class*="fa-cloud-sun"]),
#weather-widget .weather-widget-large[class*="fa-sun"]:not([class*="fa-cloud-sun"]) {
    animation: rotateSun 20s linear infinite !important;
    display: inline-block;
}

/* Cloud animation - float */
#weather-widget i[class*="fa-cloud"]:not([class*="fa-cloud-sun"]):not([class*="fa-cloud-rain"]):not([class*="fa-cloud-showers"]):not([class*="fa-cloud-showers-heavy"]),
#weather-widget .weather-widget[class*="fa-cloud"]:not([class*="fa-cloud-sun"]):not([class*="fa-cloud-rain"]):not([class*="fa-cloud-showers"]):not([class*="fa-cloud-showers-heavy"]),
#weather-widget .weather-widget-large[class*="fa-cloud"]:not([class*="fa-cloud-sun"]):not([class*="fa-cloud-rain"]):not([class*="fa-cloud-showers"]):not([class*="fa-cloud-showers-heavy"]) {
    animation: float 3s ease-in-out infinite !important;
    display: inline-block;
}

/* Cloud-sun animation - pulse */
#weather-widget i[class*="fa-cloud-sun"],
#weather-widget .weather-widget[class*="fa-cloud-sun"],
#weather-widget .weather-widget-large[class*="fa-cloud-sun"] {
    animation: pulse 2s ease-in-out infinite !important;
    display: inline-block;
}

/* Rain animation - float */
#weather-widget i[class*="fa-cloud-rain"],
#weather-widget i[class*="fa-cloud-showers"],
#weather-widget i[class*="fa-cloud-showers-heavy"],
#weather-widget .weather-widget[class*="fa-cloud-rain"],
#weather-widget .weather-widget[class*="fa-cloud-showers"],
#weather-widget .weather-widget[class*="fa-cloud-showers-heavy"],
#weather-widget .weather-widget-large[class*="fa-cloud-rain"],
#weather-widget .weather-widget-large[class*="fa-cloud-showers"],
#weather-widget .weather-widget-large[class*="fa-cloud-showers-heavy"] {
    animation: float 2s ease-in-out infinite !important;
    display: inline-block;
}

/* Lightning animation - pulse */
#weather-widget i[class*="fa-bolt"],
#weather-widget i[class*="fa-lightning"],
#weather-widget .weather-widget[class*="fa-bolt"],
#weather-widget .weather-widget[class*="fa-lightning"],
#weather-widget .weather-widget-large[class*="fa-bolt"],
#weather-widget .weather-widget-large[class*="fa-lightning"] {
    animation: pulse 1s ease-in-out infinite !important;
    display: inline-block;
    color: #f1c40f !important;
}

/* Fog/Smog animation - float */
#weather-widget i[class*="fa-smog"],
#weather-widget i[class*="fa-fog"],
#weather-widget .weather-widget[class*="fa-smog"],
#weather-widget .weather-widget[class*="fa-fog"],
#weather-widget .weather-widget-large[class*="fa-smog"],
#weather-widget .weather-widget-large[class*="fa-fog"] {
    animation: float 4s ease-in-out infinite !important;
    display: inline-block;
}

/* Forecast icons animation - same as above but scoped to forecast container */
#weather-widget .weather-forecast-container i[class*="fa-sun"]:not([class*="fa-cloud-sun"]) {
    animation: rotateSun 20s linear infinite !important;
    display: inline-block;
}

#weather-widget .weather-forecast-container i[class*="fa-cloud"]:not([class*="fa-cloud-sun"]):not([class*="fa-cloud-rain"]):not([class*="fa-cloud-showers"]):not([class*="fa-cloud-showers-heavy"]) {
    animation: float 3s ease-in-out infinite !important;
    display: inline-block;
}

#weather-widget .weather-forecast-container i[class*="fa-cloud-sun"] {
    animation: pulse 2s ease-in-out infinite !important;
    display: inline-block;
}

#weather-widget .weather-forecast-container i[class*="fa-cloud-rain"],
#weather-widget .weather-forecast-container i[class*="fa-cloud-showers"],
#weather-widget .weather-forecast-container i[class*="fa-cloud-showers-heavy"] {
    animation: float 2s ease-in-out infinite !important;
    display: inline-block;
}

#weather-widget .weather-forecast-container i[class*="fa-bolt"],
#weather-widget .weather-forecast-container i[class*="fa-lightning"] {
    animation: pulse 1s ease-in-out infinite !important;
    display: inline-block;
    color: #f1c40f !important;
}

#weather-widget .weather-forecast-container i[class*="fa-smog"],
#weather-widget .weather-forecast-container i[class*="fa-fog"] {
    animation: float 4s ease-in-out infinite !important;
    display: inline-block;
}

/* Widget Container Animations */
#weather-widget {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease !important;
}

#weather-widget::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
    z-index: 1;
    pointer-events: none;
}

#weather-widget:hover::before {
    left: 100%;
}

#weather-widget:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

#weather-widget.updating {
    animation: shake 0.5s ease !important;
}

/* Temperature Animation */
.weather-temp {
    transition: all 0.3s ease !important;
    display: inline-block;
}

.weather-temp.updating {
    animation: numberChange 0.5s ease !important;
}

/* Forecast Items Animation */
.weather-forecast-container > div {
    animation: fadeInUp 0.5s ease forwards !important;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative;
}

.weather-forecast-container > div:nth-child(1) {
    animation-delay: 0.1s !important;
}

.weather-forecast-container > div:nth-child(2) {
    animation-delay: 0.2s !important;
}

.weather-forecast-container > div:nth-child(3) {
    animation-delay: 0.3s !important;
}

.weather-forecast-container > div:hover {
    transform: translateX(5px) scale(1.02) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.weather-forecast-container > div::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #f39c12, #3498db);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.weather-forecast-container > div:hover::before {
    transform: scaleY(1);
}

.weather-forecast-container > div:hover i {
    transform: scale(1.2) rotate(5deg) !important;
}

/* Live Badge Animation */
#weather-widget .bg-green-100 {
    position: relative;
    overflow: hidden;
}

#weather-widget .bg-green-100::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
    animation: shimmer 2s infinite;
    z-index: 1;
    pointer-events: none;
}

@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
    animation: slide-out 0.3s ease-out;
}

/* Prayer Times Widget Animations */
@keyframes pulseMoon {
    0%, 100% { 
        transform: scale(1); 
        opacity: 1; 
        filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.5));
    }
    50% { 
        transform: scale(1.15); 
        opacity: 0.9; 
        filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.8));
    }
}

/* Mosque icon animation */
#prayer-times-widget i.fa-mosque {
    animation: pulse 2s ease-in-out infinite !important;
    display: inline-block;
}

/* Sun icons in prayer times - rotate */
#prayer-times-widget i.fa-sun,
#prayer-times-widget .prayer-times-list i.fa-sun {
    animation: rotateSun 20s linear infinite !important;
    display: inline-block;
}

/* Moon icon in prayer times - pulse with glow */
#prayer-times-widget i.fa-moon,
#prayer-times-widget .prayer-times-list i.fa-moon {
    animation: pulseMoon 3s ease-in-out infinite !important;
    display: inline-block;
}

/* Prayer times container hover effect */
#prayer-times-widget {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease !important;
}

#prayer-times-widget::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
    transition: left 0.5s;
    z-index: 1;
    pointer-events: none;
}

#prayer-times-widget:hover::before {
    left: 100%;
}

#prayer-times-widget:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

#prayer-times-widget.updating {
    animation: shake 0.5s ease !important;
}

/* Prayer items animation - fade in with delay */
#prayer-times-widget .prayer-times-list > div {
    animation: fadeInUp 0.5s ease forwards !important;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

#prayer-times-widget .prayer-times-list > div:nth-child(1) {
    animation-delay: 0.1s !important;
}

#prayer-times-widget .prayer-times-list > div:nth-child(2) {
    animation-delay: 0.2s !important;
}

#prayer-times-widget .prayer-times-list > div:nth-child(3) {
    animation-delay: 0.3s !important;
}

#prayer-times-widget .prayer-times-list > div:nth-child(4) {
    animation-delay: 0.4s !important;
}

#prayer-times-widget .prayer-times-list > div:nth-child(5) {
    animation-delay: 0.5s !important;
}

/* Prayer item hover effect */
#prayer-times-widget .prayer-times-list > div:hover {
    transform: translateX(5px) scale(1.02) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    background-color: rgba(34, 197, 94, 0.05) !important;
}

/* Prayer item icon container hover */
#prayer-times-widget .prayer-times-list > div:hover .w-8.h-8 {
    transform: scale(1.15) rotate(5deg) !important;
    transition: all 0.3s ease !important;
}

/* Prayer time text animation on update */
#prayer-times-widget .prayer-times-list > div span[class*="prayer-"] {
    transition: all 0.3s ease !important;
    display: inline-block;
}

#prayer-times-widget .prayer-times-list > div span[class*="prayer-"].updating {
    animation: numberChange 0.5s ease !important;
}

/* Green header shimmer effect */
#prayer-times-widget .bg-green-50 {
    position: relative;
    overflow: hidden;
}

#prayer-times-widget .bg-green-50::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
    animation: shimmer 3s infinite;
    z-index: 1;
    pointer-events: none;
}
</style>
@endsection
@endsection
