@props([
    'article',
])

@php
    $bookmarked = auth()->check() && auth()->user()->hasBookmarked($article);
@endphp

<button
    type="button"
    id="bookmark-btn-{{ $article->id }}"
    data-bookmark-url="{{ route('articles.bookmark', $article) }}"
    data-login-url="{{ route('login') }}"
    data-bookmarked="{{ $bookmarked ? '1' : '0' }}"
    @class([
        'inline-flex items-center gap-2 px-3 py-1.5 text-sm border transition-colors',
        'bg-yellow-50 text-yellow-800 border-yellow-300' => $bookmarked,
        'bg-white text-news-ink border-news-line hover:border-news-ink' => ! $bookmarked,
    ])
>
    <i class="fas fa-bookmark" aria-hidden="true"></i>
    <span class="bookmark-text">{{ $bookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
</button>
