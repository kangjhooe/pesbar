@extends('layouts.user')

@section('title', 'Pengikut Saya')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">Pengikut Saya</h1>
    <p class="text-news-muted mt-1">Pengguna yang mengikuti Anda</p>
</div>

@if($followers->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-8">
        @foreach($followers as $follower)
        <div class="bg-white border border-news-line p-5 hover:border-news-ink transition-colors">
            <div class="flex items-center gap-3 mb-4">
                @if($follower->follower->profile && $follower->follower->profile->avatar)
                <img src="{{ asset('storage/' . $follower->follower->profile->avatar) }}"
                     alt="{{ $follower->follower->name }}"
                     class="w-14 h-14 rounded-full object-cover border border-news-line">
                @else
                <div class="w-14 h-14 bg-news-ink rounded-full flex items-center justify-center text-white text-lg font-bold">
                    {{ substr($follower->follower->name, 0, 1) }}
                </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="font-display text-base font-bold text-news-ink truncate">
                        {{ $follower->follower->name }}
                    </h3>
                    <p class="text-sm text-news-muted truncate">@{{ $follower->follower->username }}</p>
                </div>
            </div>

            @if($follower->follower->profile && $follower->follower->profile->bio)
            <p class="text-news-muted text-sm mb-4 line-clamp-2">
                {{ $follower->follower->profile->bio }}
            </p>
            @endif

            <div class="text-xs text-news-muted pt-4 border-t border-news-line">
                Mengikuti sejak {{ $follower->created_at->format('d M Y') }}
            </div>
        </div>
        @endforeach
    </div>

    <div class="flex justify-center">
        {{ $followers->links() }}
    </div>
@else
    <div class="bg-white border border-news-line p-10 sm:p-12 text-center">
        <i class="fas fa-users text-news-line text-4xl mb-4"></i>
        <h3 class="font-display text-xl font-bold text-news-ink mb-2">Belum Ada Pengikut</h3>
        <p class="text-news-muted mb-6">Buat artikel menarik untuk mendapatkan pengikut</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-5 py-2.5 text-sm font-semibold transition-colors">
            Ke Beranda
        </a>
    </div>
@endif
@endsection
