@extends('layouts.user')

@section('title', 'Ringkasan Akun - ' . \App\Helpers\SettingsHelper::siteName())

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">
                Ringkasan
            </h1>
            <p class="text-news-muted mt-1">
                Halo, <span class="font-semibold text-news-ink">{{ auth()->user()->name }}</span> — aktivitas akun Anda
            </p>
        </div>
        <div class="hidden sm:flex w-12 h-12 bg-news-ink text-white items-center justify-center text-lg font-bold shrink-0">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
    </div>
</div>

@auth
    @if(auth()->user()->role === 'user')
        @if(auth()->user()->verification_request_status === 'pending')
            <div class="bg-amber-50 border border-amber-200 border-l-4 border-l-amber-500 mb-6 p-4 sm:p-5">
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock text-amber-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-news-ink">Permintaan upgrade sedang direview</p>
                        <p class="text-sm text-news-muted mt-1">
                            Akun Anda tetap sebagai pembaca sampai admin menyetujui.
                            @if(auth()->user()->verification_requested_at)
                                Dikirim: {{ auth()->user()->verification_requested_at->format('d M Y, H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_request_status === 'rejected')
            <div class="bg-red-50 border border-red-200 border-l-4 border-l-news-accent mb-6 p-4 sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <i class="fas fa-times-circle text-news-accent mt-1"></i>
                        <div>
                            <p class="font-semibold text-news-ink">Permintaan upgrade ditolak</p>
                            @if(auth()->user()->verification_rejection_reason)
                                <p class="text-sm text-news-ink mt-1">
                                    Alasan: {{ auth()->user()->verification_rejection_reason }}
                                </p>
                            @endif
                            <p class="text-sm text-news-muted mt-1">Anda dapat mengajukan ulang dengan informasi yang lebih lengkap.</p>
                        </div>
                    </div>
                    <a href="{{ route('user.upgrade-request') }}"
                       class="inline-flex items-center bg-news-accent hover:bg-red-800 text-white px-4 py-2 text-sm font-semibold transition-colors">
                        Ajukan Ulang
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white border border-news-line border-l-4 border-l-news-accent mb-6 p-4 sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <i class="fas fa-pen-nib text-news-accent mt-1"></i>
                        <div>
                            <p class="font-semibold text-news-ink">Ingin menjadi penulis?</p>
                            <p class="text-sm text-news-muted mt-1">Ajukan upgrade. Role penulis aktif setelah disetujui admin.</p>
                        </div>
                    </div>
                    <a href="{{ route('user.upgrade-request') }}"
                       class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-4 py-2 text-sm font-semibold transition-colors">
                        Ajukan Upgrade
                    </a>
                </div>
            </div>
        @endif
    @endif
@endauth

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white border border-news-line border-t-2 border-t-news-ink p-4 sm:p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Total Komentar</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ $stats['total_comments'] }}</p>
    </div>
    <div class="bg-white border border-news-line border-t-2 border-t-emerald-600 p-4 sm:p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Disetujui</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ $stats['approved_comments'] }}</p>
    </div>
    <div class="bg-white border border-news-line border-t-2 border-t-amber-500 p-4 sm:p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Menunggu</p>
        <p class="text-3xl font-bold text-news-ink mt-1 tabular-nums">{{ $stats['pending_comments'] }}</p>
    </div>
</div>

{{-- Pintasan angka (menu lengkap ada di sub-nav) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
    <a href="{{ route('user.bookmarks') }}"
       class="bg-white border border-news-line p-4 hover:border-news-ink transition-colors flex items-center justify-between group">
        <div>
            <p class="text-xs text-news-muted uppercase tracking-wide font-semibold">Bookmark</p>
            <p class="text-2xl font-bold text-news-ink tabular-nums">{{ $stats['bookmarks'] }}</p>
        </div>
        <i class="fas fa-bookmark text-news-muted group-hover:text-news-accent transition-colors"></i>
    </a>
    <a href="{{ route('user.reading-history') }}"
       class="bg-white border border-news-line p-4 hover:border-news-ink transition-colors flex items-center justify-between group">
        <div>
            <p class="text-xs text-news-muted uppercase tracking-wide font-semibold">Riwayat Baca</p>
            <p class="text-2xl font-bold text-news-ink tabular-nums">{{ $stats['reading_history'] }}</p>
        </div>
        <i class="fas fa-history text-news-muted group-hover:text-news-accent transition-colors"></i>
    </a>
    <a href="{{ route('user.following') }}"
       class="bg-white border border-news-line p-4 hover:border-news-ink transition-colors flex items-center justify-between group">
        <div>
            <p class="text-xs text-news-muted uppercase tracking-wide font-semibold">Mengikuti</p>
            <p class="text-2xl font-bold text-news-ink tabular-nums">{{ $stats['following'] }}</p>
        </div>
        <i class="fas fa-user-plus text-news-muted group-hover:text-news-accent transition-colors"></i>
    </a>
</div>

{{-- Filter --}}
<div class="bg-white border border-news-line p-4 sm:p-5 mb-5">
    <h2 class="text-sm font-bold uppercase tracking-wider text-news-ink mb-4">Filter Komentar</h2>
    <form method="GET" action="{{ route('user.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
            <label for="status" class="block text-sm font-medium text-news-muted mb-1.5">Status</label>
            <select
                id="status"
                name="status"
                class="w-full border border-news-line px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"
            >
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
            </select>
        </div>
        <div class="md:col-span-2 flex items-end gap-2">
            <button type="submit"
                    class="flex-1 bg-news-ink hover:bg-news-accent text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                Terapkan
            </button>
            @if(request()->has('status'))
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center border border-news-line px-4 py-2.5 text-sm font-semibold text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Comments --}}
<div class="bg-white border border-news-line overflow-hidden">
    <div class="px-4 sm:px-5 py-4 border-b border-news-line flex flex-wrap items-center justify-between gap-2">
        <h2 class="font-display text-lg font-bold text-news-ink">Komentar Saya</h2>
        <span class="text-xs font-semibold text-news-muted uppercase tracking-wide">
            {{ $comments->total() }} komentar
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-news-line">
            <thead class="bg-news-paper">
                <tr>
                    <th class="px-4 sm:px-5 py-3 text-left text-[11px] font-bold text-news-muted uppercase tracking-wider">Artikel</th>
                    <th class="px-4 sm:px-5 py-3 text-left text-[11px] font-bold text-news-muted uppercase tracking-wider">Komentar</th>
                    <th class="px-4 sm:px-5 py-3 text-left text-[11px] font-bold text-news-muted uppercase tracking-wider">Status</th>
                    <th class="px-4 sm:px-5 py-3 text-left text-[11px] font-bold text-news-muted uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 sm:px-5 py-3 text-left text-[11px] font-bold text-news-muted uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-news-line">
                @forelse($comments as $comment)
                <tr class="hover:bg-news-paper/80 transition-colors">
                    <td class="px-4 sm:px-5 py-4 align-top">
                        @if($comment->article)
                            <a href="{{ $comment->article->publicUrl() }}"
                               class="text-sm font-semibold text-news-ink hover:text-news-accent line-clamp-2"
                               title="{{ $comment->article->title }}">
                                {{ $comment->article->title }}
                            </a>
                            @if($comment->article->category)
                                <span class="inline-block mt-2 text-[10px] font-bold uppercase tracking-wider text-news-accent">
                                    {{ $comment->article->category->name }}
                                </span>
                            @endif
                        @else
                            <span class="text-sm text-news-muted">Artikel tidak tersedia</span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-5 py-4 align-top">
                        <p class="text-sm text-news-muted leading-relaxed line-clamp-3">
                            {{ Str::limit($comment->comment, 100) }}
                        </p>
                    </td>
                    <td class="px-4 sm:px-5 py-4 whitespace-nowrap align-top">
                        @if($comment->is_approved)
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Disetujui
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                Menunggu
                            </span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-5 py-4 whitespace-nowrap align-top">
                        <div class="text-sm text-news-ink">{{ $comment->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-news-muted mt-0.5">{{ $comment->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-4 sm:px-5 py-4 whitespace-nowrap align-top">
                        <div class="flex items-center gap-1">
                            <button type="button"
                                    class="edit-comment-btn p-2 text-news-muted hover:text-news-accent hover:bg-news-paper transition-colors"
                                    title="Edit Komentar"
                                    data-comment-id="{{ $comment->id }}"
                                    data-comment-payload="{{ e(json_encode($comment->comment, JSON_UNESCAPED_UNICODE)) }}">
                                <i class="fas fa-pen text-sm"></i>
                            </button>
                            <form action="{{ route('user.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return window.pesbarConfirmForm(event, 'Hapus komentar ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-news-muted hover:text-news-accent hover:bg-news-paper transition-colors" title="Hapus Komentar">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center">
                        <p class="text-news-ink font-medium mb-1">
                            @if(request()->has('status'))
                                Tidak ada komentar yang sesuai filter
                            @else
                                Belum ada komentar
                            @endif
                        </p>
                        <p class="text-sm text-news-muted">
                            @if(request()->has('status'))
                                <a href="{{ route('user.dashboard') }}" class="text-news-accent hover:underline font-medium">Reset filter</a>
                            @else
                                <a href="{{ route('articles.index') }}" class="text-news-accent hover:underline font-medium">Lihat artikel dan berkomentar</a>
                            @endif
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
    <div class="px-4 sm:px-5 py-4 border-t border-news-line bg-news-paper">
        <div class="flex justify-center">
            {{ $comments->links() }}
        </div>
    </div>
    @endif
</div>

@if(isset($recentArticles) && $recentArticles->count() > 0)
<div class="bg-white border border-news-line mt-6 p-4 sm:p-5">
    <h2 class="font-display text-lg font-bold text-news-ink mb-4">Artikel Terbaru</h2>
    <div class="divide-y divide-news-line">
        @foreach($recentArticles as $article)
        <div class="flex items-start justify-between gap-4 py-3 first:pt-0 last:pb-0">
            <div class="min-w-0 flex-1">
                <a href="{{ $article->publicUrl() }}"
                   class="text-sm font-semibold text-news-ink hover:text-news-accent line-clamp-2"
                   title="{{ $article->title }}">
                    {{ $article->title }}
                </a>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5">
                    @if($article->category)
                        <span class="text-[10px] font-bold uppercase tracking-wider text-news-accent">
                            {{ $article->category->name }}
                        </span>
                    @endif
                    @if($article->published_at)
                        <time class="text-xs text-news-muted" datetime="{{ $article->published_at->toIso8601String() }}">
                            {{ $article->published_at->format('d M Y') }}
                        </time>
                    @endif
                </div>
            </div>
            <a href="{{ $article->publicUrl() }}"
               class="shrink-0 text-xs font-bold uppercase tracking-wide text-news-ink hover:text-news-accent border-b border-news-ink hover:border-news-accent pb-0.5 transition-colors">
                Baca
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Edit Comment Modal --}}
<div id="editCommentModal" class="fixed inset-0 bg-black/50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-16 sm:top-20 mx-auto w-full max-w-md border border-news-line bg-white shadow-xl" id="modalContent">
        <div class="bg-news-ink px-5 py-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-white">Edit Komentar</h3>
            <button type="button" onclick="closeEditModal()" class="text-white/70 hover:text-white" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5">
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label for="edit_comment_text" class="block text-sm font-semibold text-news-ink mb-1.5">Komentar</label>
                    <textarea id="edit_comment_text"
                              name="comment"
                              rows="5"
                              required
                              class="w-full border border-news-line px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent resize-none"
                              maxlength="1000"
                              placeholder="Tulis komentar Anda..."></textarea>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-news-muted">Maks. 1000 karakter</p>
                        <p class="text-xs text-news-muted" id="charCount">0 / 1000</p>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button"
                            onclick="closeEditModal()"
                            class="px-4 py-2 border border-news-line text-sm font-semibold text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-news-accent hover:bg-red-800 text-white text-sm font-semibold transition-colors">
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
    updateCharCount();
    modal.classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCommentModal').classList.add('hidden');
}

function updateCharCount() {
    const textarea = document.getElementById('edit_comment_text');
    const charCount = document.getElementById('charCount');
    if (textarea && charCount) {
        const length = textarea.value.length;
        charCount.textContent = `${length} / 1000`;
        charCount.classList.toggle('text-news-accent', length > 900);
        charCount.classList.toggle('text-news-muted', length <= 900);
    }
}

document.getElementById('editCommentModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeEditModal();
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-comment-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            let text = '';
            try {
                text = JSON.parse(this.getAttribute('data-comment-payload') || '""');
            } catch (e) {
                text = '';
            }
            editComment(this.dataset.commentId, text);
        });
    });

    document.getElementById('edit_comment_text')?.addEventListener('input', updateCharCount);
});
</script>
@endsection
