@extends('layouts.penulis')

@section('title', 'Kelola Komentar')
@section('page-title', 'Kelola Komentar')
@section('page-subtitle', 'Kelola komentar pada artikel Anda')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-news-ink mb-2">Kelola Komentar</h1>
 <p class="text-news-muted">Kelola komentar pada artikel: <strong>{{ $article->title }}</strong></p>
 </div>
 <div class="flex gap-2">
 <a href="{{ route('penulis.articles.show', $article) }}" class="bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg font-medium transition-colors">
 Kembali ke Detail
 </a>
 <a href="{{ route('penulis.articles.index') }}" class="bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded-lg font-medium transition-colors">
 Dashboard
 </a>
 </div>
 </div>
 </div>

 @if(session('success'))
 <div class="bg-news-paper border border-news-line text-news-ink px-4 py-3 rounded mb-6">
 {{ session('success') }}
 </div>
 @endif

 <!-- Stats -->
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
 <div class="bg-white border border-news-line p-6">
 <div class="flex items-center">
 <div class="p-2 bg-news-paper rounded-lg">
 <svg class="w-6 h-6 text-news-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
 </svg>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-news-muted">Total Komentar</p>
 <p class="text-2xl font-semibold text-news-ink">{{ $comments->total() }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white border border-news-line p-6">
 <div class="flex items-center">
 <div class="p-2 bg-news-paper rounded-lg">
 <svg class="w-6 h-6 text-news-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-news-muted">Disetujui</p>
 <p class="text-2xl font-semibold text-news-ink">{{ $article->comments()->where('is_approved', true)->count() }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white border border-news-line p-6">
 <div class="flex items-center">
 <div class="p-2 bg-news-paper rounded-lg">
 <svg class="w-6 h-6 text-news-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-news-muted">Menunggu</p>
 <p class="text-2xl font-semibold text-news-ink">{{ $article->comments()->where('is_approved', false)->count() }}</p>
 </div>
 </div>
 </div>
 </div>

 <!-- Comments List -->
 <div class="bg-white border border-news-line">
 <div class="px-6 py-4 border-b border-news-line">
 <h2 class="text-lg font-semibold text-news-ink">Daftar Komentar</h2>
 </div>
 
 @if($comments->count() > 0)
 <div class="divide-y divide-news-line">
 @foreach($comments as $comment)
 <div class="p-6 hover:bg-news-paper">
 <div class="flex items-start justify-between">
 <div class="flex-1">
 <div class="flex items-center gap-3 mb-2">
 <div class="flex-1">
 <p class="font-medium text-news-ink">{{ $comment->name }}</p>
 <p class="text-sm text-news-muted">{{ $comment->email }}</p>
 </div>
 <div>
 @if($comment->is_approved)
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
 Disetujui
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
 Menunggu Persetujuan
 </span>
 @endif
 </div>
 </div>
 
 <p class="text-news-ink mb-3">{{ $comment->comment }}</p>
 
 <div class="flex items-center gap-4 text-xs text-news-muted">
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
 <button type="submit" class="bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded text-sm font-medium transition-colors">
 Setujui
 </button>
 </form>
 @else
 <form action="{{ route('penulis.articles.comments.status', [$article, $comment]) }}" method="POST" class="inline">
 @csrf
 <input type="hidden" name="is_approved" value="0">
 <button type="submit" class="bg-news-ink hover:bg-news-accent text-white px-4 py-2 rounded text-sm font-medium transition-colors">
 Batalkan Persetujuan
 </button>
 </form>
 @endif
 
 <form action="{{ route('penulis.articles.comments.delete', [$article, $comment]) }}" method="POST" class="inline" onsubmit="return window.pesbarConfirmForm(event, 'Apakah Anda yakin ingin menghapus komentar ini?')">
 @csrf
 @method('DELETE')
 <button type="submit" class="bg-news-accent hover:bg-news-ink text-white px-4 py-2 rounded text-sm font-medium transition-colors w-full">
 Hapus
 </button>
 </form>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 
 <div class="px-6 py-4 border-t border-news-line">
 {{ $comments->links() }}
 </div>
 @else
 <div class="p-12 text-center">
 <svg class="mx-auto h-12 w-12 text-news-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
 </svg>
 <h3 class="mt-2 text-sm font-medium text-news-ink">Tidak ada komentar</h3>
 <p class="mt-1 text-sm text-news-muted">Belum ada komentar pada artikel ini.</p>
 </div>
 @endif
 </div>
</div>
@endsection

