@extends('layouts.admin-simple')

@section('title', 'Kelola Komentar')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Komentar</h1>
                <p class="text-gray-600">Kelola komentar pada artikel: <strong>{{ $article->title }}</strong></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('penulis.articles.show', $article) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Kembali ke Detail
                </a>
                <a href="{{ route('penulis.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Komentar</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $comments->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $article->comments()->where('is_approved', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $article->comments()->where('is_approved', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Komentar</h2>
        </div>
        
        @if($comments->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($comments as $comment)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $comment->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $comment->email }}</p>
                                    </div>
                                    <div>
                                        @if($comment->is_approved)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Menunggu Persetujuan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="text-gray-700 mb-3">{{ $comment->comment }}</p>
                                
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>{{ $comment->created_at->format('d M Y, H:i') }}</span>
                                    @if($comment->ip_address)
                                        <span>IP: {{ $comment->ip_address }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-4 flex flex-col gap-2">
                                @if(!$comment->is_approved)
                                    <form action="{{ route('penulis.articles.comments.status', [$article, $comment]) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="is_approved" value="1">
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                                            Setujui
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('penulis.articles.comments.status', [$article, $comment]) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="is_approved" value="0">
                                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                                            Batalkan Persetujuan
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('penulis.articles.comments.delete', [$article, $comment]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors w-full">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $comments->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada komentar</h3>
                <p class="mt-1 text-sm text-gray-500">Belum ada komentar pada artikel ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

