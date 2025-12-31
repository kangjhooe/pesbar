@extends('layouts.penulis')

@section('title', 'Manajemen Komentar Lanjutan')
@section('page-title', 'Manajemen Komentar Lanjutan')
@section('page-subtitle', 'Kelola semua komentar artikel Anda')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Komentar</h1>
                <p class="text-gray-600 mt-1">Kelola dan moderasi komentar artikel Anda</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('penulis.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Komentar</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <i class="fas fa-comments text-blue-600 text-3xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Disetujui</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['approved']) }}</p>
                </div>
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                </div>
                <i class="fas fa-clock text-yellow-600 text-3xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Dengan Balasan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['with_replies']) }}</p>
                </div>
                <i class="fas fa-reply text-purple-600 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('penulis.comments.advanced') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Artikel</label>
                <select name="article_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Artikel</option>
                    @foreach($articles as $article)
                    <option value="{{ $article->id }}" {{ request('article_id') == $article->id ? 'selected' : '' }}>
                        {{ Str::limit($article->title, 40) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari komentar..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <form id="bulkForm" method="POST" action="{{ route('penulis.comments.bulk-action') }}" class="mb-4">
        @csrf
        <div class="flex items-center gap-4 bg-white rounded-xl shadow-md p-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="selectAll" class="text-sm font-medium text-gray-700">Pilih Semua</label>
            </div>
            <select name="action" id="bulkAction" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Aksi</option>
                <option value="approve">Setujui</option>
                <option value="reject">Tolak</option>
                <option value="delete">Hapus</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-check mr-2"></i>Terapkan
            </button>
        </div>
    </form>

    <!-- Comments Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" onchange="toggleSelectAll()" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded">
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit(strip_tags($comment->comment), 100) }}</div>
                            @if($comment->parent_id)
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-reply mr-1"></i>Balasan untuk komentar #{{ $comment->parent_id }}
                            </div>
                            @endif
                            @if($comment->replies_count > 0)
                            <div class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-comments mr-1"></i>{{ $comment->replies_count }} balasan
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('penulis.articles.show', $comment->article_id) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                {{ Str::limit($comment->article->title, 40) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $comment->name }}</div>
                            <div class="text-xs text-gray-500">{{ $comment->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($comment->is_approved)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $comment->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                @if(!$comment->is_approved)
                                <form method="POST" action="{{ route('penulis.articles.comments.status', [$comment->article_id, $comment->id]) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="is_approved" value="1">
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('penulis.articles.comments.status', [$comment->article_id, $comment->id]) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="is_approved" value="0">
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                                <button onclick="showReplyModal({{ $comment->id }}, '{{ $comment->article_id }}')" class="text-blue-600 hover:text-blue-900" title="Balas">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <form method="POST" action="{{ route('penulis.articles.comments.delete', [$comment->article_id, $comment->id]) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada komentar ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $comments->links() }}
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-2xl w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Balas Komentar</h3>
            <button onclick="closeReplyModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="replyForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Balasan</label>
                <textarea name="comment" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Balasan
                </button>
                <button type="button" onclick="closeReplyModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
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

