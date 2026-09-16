@extends('layouts.penulis')

@section('title', 'Manajemen Komentar Lanjutan')
@section('page-title', 'Manajemen Komentar Lanjutan')
@section('page-subtitle', 'Kelola semua komentar artikel Anda')

@section('content')
<div>
 <!-- Header -->
 <div class="mb-6">
 <div class="flex items-center justify-between flex-wrap gap-4">
 <div>
 <h1 class="text-3xl font-bold text-news-ink">Manajemen Komentar</h1>
 <p class="text-news-muted mt-1">Kelola dan moderasi komentar artikel Anda</p>
 </div>
 <div class="flex gap-2">
 <a href="{{ route('penulis.dashboard') }}" class="px-4 py-2 border border-news-line text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
 <i class="fas fa-arrow-left mr-2"></i>Kembali
 </a>
 </div>
 </div>
 </div>

 @if(session('success'))
 <div class="mb-6 bg-news-paper border-l-4 border-news-ink text-news-ink px-4 py-3 rounded">
 {{ session('success') }}
 </div>
 @endif

 <!-- Stats Cards -->
 <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-accent">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Total Komentar</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['total']) }}</p>
 </div>
 <i class="fas fa-comments text-news-accent text-3xl"></i>
 </div>
 </div>
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Disetujui</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['approved']) }}</p>
 </div>
 <i class="fas fa-check-circle text-news-accent text-3xl"></i>
 </div>
 </div>
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Pending</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['pending']) }}</p>
 </div>
 <i class="fas fa-clock text-news-muted text-3xl"></i>
 </div>
 </div>
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Dengan Balasan</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['with_replies']) }}</p>
 </div>
 <i class="fas fa-reply text-news-accent text-3xl"></i>
 </div>
 </div>
 </div>

 <!-- Filters -->
 <div class="bg-white border border-news-line p-6 mb-6">
 <form method="GET" action="{{ route('penulis.comments.advanced') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
 <div>
 <label class="block text-sm font-medium text-news-ink mb-2">Status</label>
 <select name="status" class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 <option value="">Semua</option>
 <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
 <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-medium text-news-ink mb-2">Artikel</label>
 <select name="article_id" class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 <option value="">Semua Artikel</option>
 @foreach($articles as $article)
 <option value="{{ $article->id }}" {{ request('article_id') == $article->id ? 'selected' : '' }}>
 {{ Str::limit($article->title, 40) }}
 </option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-sm font-medium text-news-ink mb-2">Dari Tanggal</label>
 <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 </div>
 <div>
 <label class="block text-sm font-medium text-news-ink mb-2">Sampai Tanggal</label>
 <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 </div>
 <div>
 <label class="block text-sm font-medium text-news-ink mb-2">Pencarian</label>
 <div class="flex gap-2">
 <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari komentar..." class="flex-1 px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 <button type="submit" class="px-4 py-2 bg-news-ink hover:bg-news-accent text-white rounded-lg transition">
 <i class="fas fa-search"></i>
 </button>
 </div>
 </div>
 </form>
 </div>

 <!-- Bulk Actions -->
 <form id="bulkForm" method="POST" action="{{ route('penulis.comments.bulk-action') }}" class="mb-4">
 @csrf
 <div class="flex items-center gap-4 bg-white border border-news-line p-4">
 <div class="flex items-center gap-2">
 <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-news-accent border-news-line rounded focus:ring-news-accent/30">
 <label for="selectAll" class="text-sm font-medium text-news-ink">Pilih Semua</label>
 </div>
 <select name="action" id="bulkAction" class="px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 <option value="">Pilih Aksi</option>
 <option value="approve">Setujui</option>
 <option value="reject">Tolak</option>
 <option value="delete">Hapus</option>
 </select>
 <button type="submit" class="px-4 py-2 bg-news-ink hover:bg-news-accent text-white rounded-lg transition">
 <i class="fas fa-check mr-2"></i>Terapkan
 </button>
 </div>
 </form>

 <!-- Comments Table -->
 <div class="bg-white border border-news-line overflow-hidden">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-news-line">
 <thead class="bg-news-paper">
 <tr>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
 <input type="checkbox" onchange="toggleSelectAll()" class="w-4 h-4 text-news-accent border-news-line rounded">
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Komentar</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Artikel</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Penulis</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Status</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Tanggal</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Aksi</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-news-line">
 @forelse($comments as $comment)
 <tr class="hover:bg-news-paper">
 <td class="px-6 py-4 whitespace-nowrap">
 <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox w-4 h-4 text-news-accent border-news-line rounded">
 </td>
 <td class="px-6 py-4">
 <div class="text-sm text-news-ink">{{ Str::limit(strip_tags($comment->comment), 100) }}</div>
 @if($comment->parent_id)
 <div class="text-xs text-news-muted mt-1">
 <i class="fas fa-reply mr-1"></i>Balasan untuk komentar #{{ $comment->parent_id }}
 </div>
 @endif
 @if($comment->replies_count > 0)
 <div class="text-xs text-news-accent mt-1">
 <i class="fas fa-comments mr-1"></i>{{ $comment->replies_count }} balasan
 </div>
 @endif
 </td>
 <td class="px-6 py-4">
 <a href="{{ route('penulis.articles.show', $comment->article_id) }}" class="text-sm text-news-accent hover:text-news-ink line-clamp-2" title="{{ $comment->article->title }}">
 {{ $comment->article->title }}
 </a>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-news-ink">{{ $comment->name }}</div>
 <div class="text-xs text-news-muted">{{ $comment->email }}</div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @if($comment->is_approved)
 <span class="px-2 py-1 text-xs font-semibold rounded-full bg-news-paper text-news-ink">
 <i class="fas fa-check-circle mr-1"></i>Disetujui
 </span>
 @else
 <span class="px-2 py-1 text-xs font-semibold rounded-full bg-news-paper text-news-ink">
 <i class="fas fa-clock mr-1"></i>Pending
 </span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
 {{ $comment->created_at->format('d/m/Y H:i') }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
 <div class="flex gap-2">
 @if(!$comment->is_approved)
 <form method="POST" action="{{ route('penulis.articles.comments.status', [$comment->article_id, $comment->id]) }}" class="inline">
 @csrf
 <input type="hidden" name="is_approved" value="1">
 <button type="submit" class="text-news-accent hover:text-news-ink" title="Setujui">
 <i class="fas fa-check"></i>
 </button>
 </form>
 @else
 <form method="POST" action="{{ route('penulis.articles.comments.status', [$comment->article_id, $comment->id]) }}" class="inline">
 @csrf
 <input type="hidden" name="is_approved" value="0">
 <button type="submit" class="text-news-muted hover:text-news-ink" title="Tolak">
 <i class="fas fa-times"></i>
 </button>
 </form>
 @endif
 <button onclick="showReplyModal({{ $comment->id }}, '{{ $comment->article_id }}')" class="text-news-accent hover:text-news-ink" title="Balas">
 <i class="fas fa-reply"></i>
 </button>
 <form method="POST" action="{{ route('penulis.articles.comments.delete', [$comment->article_id, $comment->id]) }}" class="inline" onsubmit="return window.pesbarConfirmForm(event, 'Yakin ingin menghapus komentar ini?')">
 @csrf
 @method('DELETE')
 <button type="submit" class="text-news-accent hover:text-news-ink" title="Hapus">
 <i class="fas fa-trash"></i>
 </button>
 </form>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="px-6 py-4 text-center text-news-muted">Tidak ada komentar ditemukan</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 
 <!-- Pagination -->
 <div class="px-6 py-4 border-t border-news-line">
 {{ $comments->links() }}
 </div>
 </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
 <div class="bg-white border-2 border-news-ink p-6 max-w-2xl w-full mx-4">
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-xl font-semibold text-news-ink">Balas Komentar</h3>
 <button onclick="closeReplyModal()" class="text-news-muted hover:text-news-muted">
 <i class="fas fa-times"></i>
 </button>
 </div>
 <form id="replyForm" method="POST">
 @csrf
 <div class="mb-4">
 <label class="block text-sm font-medium text-news-ink mb-2">Nama</label>
 <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-news-ink mb-2">Email</label>
 <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-news-ink mb-2">Balasan</label>
 <textarea name="comment" rows="4" required class="w-full px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"></textarea>
 </div>
 <div class="flex gap-2">
 <button type="submit" class="flex-1 px-4 py-2 bg-news-ink hover:bg-news-accent text-white rounded-lg transition">
 <i class="fas fa-paper-plane mr-2"></i>Kirim Balasan
 </button>
 <button type="button" onclick="closeReplyModal()" class="px-4 py-2 border border-news-line text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
 Batal
 </button>
 </div>
 </form>
 </div>
</div>

@push('scripts')
<script>
 function toggleSelectAll() {
 const selectAll = document.getElementById('selectAll');
 const checkboxes = document.querySelectorAll('.comment-checkbox');
 checkboxes.forEach(cb => cb.checked = selectAll.checked);
 }

 function showReplyModal(commentId, articleId) {
 document.getElementById('replyForm').action = `/penulis/articles/${articleId}/comments/${commentId}/reply`;
 document.getElementById('replyModal').classList.remove('hidden');
 document.getElementById('replyModal').classList.add('flex');
 }

 function closeReplyModal() {
 document.getElementById('replyModal').classList.add('hidden');
 document.getElementById('replyModal').classList.remove('flex');
 document.getElementById('replyForm').reset();
 }
</script>
@endpush
@endsection

