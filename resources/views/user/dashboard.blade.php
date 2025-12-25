@extends('layouts.user')

@section('title', 'Dashboard User')

@section('content')
<div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard User</h1>
        <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    @auth
        @if(auth()->user()->role === 'user')
            @if(auth()->user()->verification_request_status === 'pending')
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            <div>
                                <span class="font-semibold">Permintaan upgrade Anda sedang dalam proses review oleh admin.</span>
                                @if(auth()->user()->verification_requested_at)
                                    <p class="text-sm mt-1">Dikirim pada: {{ auth()->user()->verification_requested_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->verification_request_status === 'rejected')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>
                            <div>
                                <span class="font-semibold">Permintaan upgrade Anda ditolak.</span>
                                <p class="text-sm mt-1">Anda dapat mengajukan ulang dengan melengkapi informasi yang lebih lengkap.</p>
                            </div>
                        </div>
                        <a href="{{ route('user.upgrade-request') }}" class="ml-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium">
                            Ajukan Ulang
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Ingin menjadi penulis? Ajukan permintaan upgrade untuk bisa menulis artikel!</span>
                        </div>
                        <a href="{{ route('user.upgrade-request') }}" class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
                            Ajukan Upgrade
                        </a>
                    </div>
                </div>
            @endif
        @endif
    @endauth

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Komentar</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_comments'] }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Komentar Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved_comments'] }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Menunggu Persetujuan</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_comments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 mb-8">
        <a href="{{ route('profile.edit') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Edit Profil
        </a>
        @if(auth()->user()->role === 'user')
            <a href="{{ route('user.upgrade-request') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Ajukan Menjadi Penulis
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('user.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Komentar</label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                </select>
            </div>
            
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Terapkan Filter
                </button>
                @if(request()->has('status'))
                    <a href="{{ route('user.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Comments Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Komentar Saya</h2>
            <span class="text-sm text-gray-600">Total: {{ $comments->total() }} komentar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="{{ route('articles.show', $comment->article) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ Str::limit($comment->article->title, 50) }}
                                </a>
                            </div>
                            @if($comment->article->category)
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $comment->article->category->name }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($comment->comment, 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($comment->is_approved)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $comment->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <button onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->comment) }}')" 
                                        class="text-blue-600 hover:text-blue-900" 
                                        title="Edit Komentar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('user.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus Komentar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            @if(request()->has('status'))
                                Tidak ada komentar yang sesuai dengan filter. <a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-900">Reset filter</a>
                            @else
                                Belum ada komentar. <a href="{{ route('articles.index') }}" class="text-blue-600 hover:text-blue-900">Lihat artikel</a> dan berikan komentar!
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $comments->links() }}
        </div>
    </div>

    <!-- Recent Articles Section -->
    @if(isset($recentArticles) && $recentArticles->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mt-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Artikel Terbaru</h2>
        <div class="space-y-3">
            @foreach($recentArticles as $article)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <a href="{{ route('articles.show', $article) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                        {{ Str::limit($article->title, 60) }}
                    </a>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $article->category->name ?? 'Tanpa Kategori' }} • {{ $article->published_at ? $article->published_at->format('d M Y') : '' }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('articles.show', $article) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                        Baca →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Edit Comment Modal -->
<div id="editCommentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Komentar</h3>
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="edit_comment_text" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea id="edit_comment_text" 
                              name="comment" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              maxlength="1000"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeEditModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editComment(commentId, commentText) {
    const modal = document.getElementById('editCommentModal');
    const form = document.getElementById('editCommentForm');
    const textarea = document.getElementById('edit_comment_text');
    
    form.action = `/user/comments/${commentId}`;
    textarea.value = commentText;
    modal.classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCommentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('editCommentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection

