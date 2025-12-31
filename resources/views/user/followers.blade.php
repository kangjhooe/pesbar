@extends('layouts.user')

@section('title', 'Pengikut Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pengikut Saya</h1>
        <p class="text-gray-600">Daftar pengguna yang mengikuti Anda</p>
    </div>

    @if($followers->count() > 0)
        <!-- Followers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($followers as $follower)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <!-- Follower Avatar and Name -->
                    <div class="flex items-center space-x-4 mb-4">
                        @if($follower->follower->profile && $follower->follower->profile->avatar)
                        <img src="{{ asset('storage/' . $follower->follower->profile->avatar) }}" 
                             alt="{{ $follower->follower->name }}" 
                             class="w-16 h-16 rounded-full object-cover border-2 border-primary-200">
                        @else
                        <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            {{ substr($follower->follower->name, 0, 1) }}
                        </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $follower->follower->name }}
                            </h3>
                            <p class="text-sm text-gray-500">@{{ $follower->follower->username }}</p>
                        </div>
                    </div>

                    <!-- Follower Bio -->
                    @if($follower->follower->profile && $follower->follower->profile->bio)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $follower->follower->profile->bio }}
                    </p>
                    @endif

                    <!-- Follow Date -->
                    <div class="text-sm text-gray-500 pt-4 border-t border-gray-100">
                        <i class="fas fa-calendar text-primary-500 mr-2"></i>
                        Mengikuti sejak {{ $follower->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $followers->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="mb-4">
                <i class="fas fa-users text-gray-300 text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pengikut</h3>
            <p class="text-gray-600 mb-6">Mulai buat artikel menarik untuk mendapatkan pengikut</p>
            <a href="{{ route('home') }}" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    @endif
</div>
@endsection

