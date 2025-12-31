@extends('layouts.user')

@section('title', 'Penulis yang Diikuti')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Penulis yang Diikuti</h1>
        <p class="text-gray-600">Daftar penulis yang Anda ikuti</p>
    </div>

    @if($follows->count() > 0)
        <!-- Authors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($follows as $follow)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <!-- Author Avatar and Name -->
                    <div class="flex items-center space-x-4 mb-4">
                        @if($follow->following->profile && $follow->following->profile->avatar)
                        <img src="{{ asset('storage/' . $follow->following->profile->avatar) }}" 
                             alt="{{ $follow->following->name }}" 
                             class="w-16 h-16 rounded-full object-cover border-2 border-primary-200">
                        @else
                        <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            {{ substr($follow->following->name, 0, 1) }}
                        </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">
                                <a href="{{ route('penulis.public-profile', $follow->following->username) }}" 
                                   class="hover:text-primary-600 transition-colors">
                                    {{ $follow->following->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500">@{{ $follow->following->username }}</p>
                        </div>
                    </div>

                    <!-- Author Bio -->
                    @if($follow->following->profile && $follow->following->profile->bio)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $follow->following->profile->bio }}
                    </p>
                    @endif

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4 pb-4 border-b border-gray-100">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-newspaper text-primary-500"></i>
                            <span>{{ $follow->following->articles_count ?? 0 }} Artikel</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-calendar text-primary-500"></i>
                            <span>Diikuti {{ $follow->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('penulis.public-profile', $follow->following->username) }}" 
                           class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors text-center text-sm font-medium">
                            <i class="fas fa-user mr-2"></i>
                            Lihat Profil
                        </a>
                        <button onclick="toggleFollow({{ $follow->following->id }})" 
                                class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium"
                                id="follow-btn-{{ $follow->following->id }}">
                            <i class="fas fa-user-minus mr-2"></i>
                            Berhenti Ikuti
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $follows->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="mb-4">
                <i class="fas fa-user-plus text-gray-300 text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Mengikuti Penulis</h3>
            <p class="text-gray-600 mb-6">Mulai ikuti penulis favorit Anda untuk mendapatkan update artikel terbaru</p>
            <a href="{{ route('home') }}" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    @endif
</div>

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
                document.getElementById(`follow-btn-${userId}`).innerHTML = '<i class="fas fa-user-minus mr-2"></i> Berhenti Ikuti';
                document.getElementById(`follow-btn-${userId}`).classList.remove('bg-primary-600', 'text-white');
                document.getElementById(`follow-btn-${userId}`).classList.add('bg-gray-100', 'text-gray-700');
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

