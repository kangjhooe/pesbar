@extends('layouts.user')

@section('title', 'Penulis yang Diikuti')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">Penulis yang Diikuti</h1>
    <p class="text-news-muted mt-1">Daftar penulis yang Anda ikuti</p>
</div>

@if($follows->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-8">
        @foreach($follows as $follow)
        <div class="bg-white border border-news-line p-5 hover:border-news-ink transition-colors">
            <div class="flex items-center gap-3 mb-4">
                @if($follow->following->profile && $follow->following->profile->avatar)
                <img src="{{ asset('storage/' . $follow->following->profile->avatar) }}"
                     alt="{{ $follow->following->name }}"
                     class="w-14 h-14 rounded-full object-cover border border-news-line">
                @else
                <div class="w-14 h-14 bg-news-ink rounded-full flex items-center justify-center text-white text-lg font-bold">
                    {{ substr($follow->following->name, 0, 1) }}
                </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="font-display text-base font-bold text-news-ink truncate">
                        <a href="{{ route('penulis.public-profile', $follow->following->username) }}"
                           class="hover:text-news-accent transition-colors">
                            {{ $follow->following->name }}
                        </a>
                    </h3>
                    <p class="text-sm text-news-muted truncate">@{{ $follow->following->username }}</p>
                </div>
            </div>

            @if($follow->following->profile && $follow->following->profile->bio)
            <p class="text-news-muted text-sm mb-4 line-clamp-2">
                {{ $follow->following->profile->bio }}
            </p>
            @endif

            <div class="flex items-center justify-between text-xs text-news-muted mb-4 pb-4 border-b border-news-line">
                <span>{{ $follow->following->articles_count ?? 0 }} artikel</span>
                <span>Diikuti {{ $follow->created_at->diffForHumans() }}</span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('penulis.public-profile', $follow->following->username) }}"
                   class="flex-1 bg-news-ink hover:bg-news-accent text-white px-3 py-2 text-center text-sm font-semibold transition-colors">
                    Lihat Profil
                </a>
                <button type="button"
                        onclick="toggleFollow({{ $follow->following->id }})"
                        class="border border-news-line text-news-muted hover:border-news-ink hover:text-news-ink px-3 py-2 text-sm font-semibold transition-colors"
                        id="follow-btn-{{ $follow->following->id }}">
                    Berhenti
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div class="flex justify-center">
        {{ $follows->links() }}
    </div>
@else
    <div class="bg-white border border-news-line p-10 sm:p-12 text-center">
        <i class="fas fa-user-plus text-news-line text-4xl mb-4"></i>
        <h3 class="font-display text-xl font-bold text-news-ink mb-2">Belum Mengikuti Penulis</h3>
        <p class="text-news-muted mb-6">Ikuti penulis favorit untuk update artikel terbaru</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-5 py-2.5 text-sm font-semibold transition-colors">
            Ke Beranda
        </a>
    </div>
@endif

<script>
function toggleFollow(userId) {
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
                const btn = document.getElementById(`follow-btn-${userId}`);
                btn.textContent = 'Berhenti';
            } else {
                location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
