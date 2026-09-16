@props([
    'user',
    'compact' => false,
])

@php
    $isSelf = auth()->check() && (int) auth()->id() === (int) $user->id;
    $following = auth()->check() && auth()->user()->isFollowing($user);
@endphp

@unless($isSelf)
<button
    type="button"
    id="follow-btn-{{ $user->id }}"
    data-follow-url="{{ route('users.follow', $user) }}"
    data-login-url="{{ route('login') }}"
    data-compact="{{ $compact ? '1' : '0' }}"
    data-following="{{ $following ? '1' : '0' }}"
    @class([
        'inline-flex items-center justify-center gap-1.5 border transition-colors',
        'px-2 py-1 text-[11px] font-semibold w-full mt-2' => $compact,
        'px-3 py-1.5 text-sm font-medium' => ! $compact,
        'bg-news-ink text-white border-news-ink' => $following,
        'bg-white text-news-ink border-news-line hover:border-news-accent hover:text-news-accent' => ! $following,
    ])
>
    <i class="fas {{ $following ? 'fa-user-check' : 'fa-user-plus' }} {{ $compact ? 'text-[10px]' : '' }}" aria-hidden="true"></i>
    <span class="follow-text">
        @if($following)
            Mengikuti
        @else
            {{ $compact ? 'Ikuti' : 'Ikuti Penulis' }}
        @endif
    </span>
</button>
@endunless
