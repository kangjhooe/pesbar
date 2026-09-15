@extends('layouts.admin-simple')

@section('page-title', 'Artikel Menunggu Persetujuan')
@section('page-subtitle', 'Tinjau dan setujui artikel yang diajukan penulis')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu Review</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $articles->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disetujui Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Article::where('status', 'published')->whereDate('updated_at', today())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ditolak Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Article::where('status', 'rejected')->whereDate('updated_at', today())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.articles.pending') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Artikel</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Judul atau penulis..."
                           class="form-input">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category" id="category" class="form-input">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-input">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-input">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full btn-primary justify-center">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('admin.articles.pending') }}" class="w-full btn-secondary justify-center">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                </div>
            </div>
            <div class="flex items-center justify-end text-sm text-gray-500">
                Menampilkan {{ $articles->count() }} dari {{ $articles->total() }} artikel
            </div>
        </form>
    </div>

    {{-- Bulk approve/reject via JSON article_ids (AdminDashboardController) --}}
    <x-admin.table
        :paginator="$articles"
        :bulk="true"
        bulk-id="pending-bulk"
        empty-icon="fas fa-newspaper"
        empty-title="Tidak Ada Artikel"
        empty-text="Tidak ada artikel yang menunggu persetujuan saat ini."
        :colspan="7"
    >
        <x-slot:bulkBar>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-gray-600">
                    <span id="pending-bulk-count" class="font-semibold text-news-accent">0</span> dipilih
                </span>
                <button type="button" id="bulkApproveBtn"
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-check mr-1.5"></i>Setujui
                </button>
                <button type="button" id="bulkRejectBtn"
                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-times mr-1.5"></i>Tolak
                </button>
                <button type="button"
                        onclick="window.adminTableClearSelection('pending-bulk')"
                        class="inline-flex items-center px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
            </div>
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="pending-bulk" name="article_ids[]" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="title" label="Artikel" />
            <x-admin.th :sortable="false" label="Penulis" />
            <x-admin.th :sortable="false" label="Kategori" />
            <x-admin.th column="created_at" label="Diajukan" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($articles as $article)
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox bulk-id="pending-bulk" name="article_ids[]" :value="$article->id" />
                <x-admin.td-number :index="$articles->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="flex items-start space-x-4">
                        @if($article->featured_image)
                            <div class="flex-shrink-0 h-16 w-24">
                                <img class="h-16 w-24 rounded-lg object-cover" src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}">
                            </div>
                        @else
                            <div class="flex-shrink-0 h-16 w-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-newspaper text-gray-400"></i>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">{{ $article->title }}</h4>
                            <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ number_format($article->views) }}</span>
                                </span>
                                @if($article->is_featured)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i>Featured
                                    </span>
                                @endif
                                @if($article->is_breaking)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-bolt mr-1"></i>Breaking
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            @if($article->author->profile && $article->author->profile->avatar)
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . $article->author->profile->avatar) }}" alt="{{ $article->author->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-700">{{ substr($article->author->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($article->author && $article->author->isPenulis() && $article->author->username)
                                        <a href="{{ route('penulis.public-profile', $article->author->username) }}" class="text-news-accent hover:text-red-800 font-medium" target="_blank">
                                            {{ $article->author->name }}
                                        </a>
                                    @else
                                        {{ $article->author->name }}
                                    @endif
                                </div>
                                @if($article->author)
                                    <x-user-role-badge :user="$article->author" size="xs" />
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">{{ $article->author->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-50 text-news-accent">
                        {{ $article->category->name }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>{{ $article->created_at->format('d-m-Y') }}</div>
                    <div class="text-xs">{{ $article->created_at->format('H:i') }} WIB</div>
                </td>
                <x-admin.actions>
                    <x-admin.action-icon
                        :href="route('admin.articles.detail', $article)"
                        icon="fas fa-eye"
                        color="accent"
                        title="Review Artikel"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-newspaper text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Tidak Ada Artikel</p>
                    <p class="text-sm">Tidak ada artikel yang menunggu persetujuan saat ini.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tolak Artikel</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600" type="button">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="rejectForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea id="rejectReason" name="reason" rows="4" required
                                  class="form-input"
                                  placeholder="Berikan alasan mengapa artikel ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeRejectModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        <i class="fas fa-times mr-2"></i>Tolak Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentArticleId = null;

function pendingCheckedIds() {
    return Array.from(document.querySelectorAll('.admin-row-checkbox[data-bulk-id="pending-bulk"]:checked')).map(cb => cb.value);
}

function updatePendingBulkButtons() {
    const enabled = pendingCheckedIds().length > 0;
    const approveBtn = document.getElementById('bulkApproveBtn');
    const rejectBtn = document.getElementById('bulkRejectBtn');
    if (approveBtn) approveBtn.disabled = !enabled;
    if (rejectBtn) rejectBtn.disabled = !enabled;
}

document.addEventListener('change', function(e) {
    if (e.target.matches('.admin-row-checkbox[data-bulk-id="pending-bulk"], .admin-select-all[data-bulk-id="pending-bulk"]')) {
        updatePendingBulkButtons();
    }
});

document.getElementById('bulkApproveBtn')?.addEventListener('click', function() {
    const articleIds = pendingCheckedIds();
    if (articleIds.length && confirm(`Setujui ${articleIds.length} artikel terpilih?`)) {
        bulkAction('approve', articleIds);
    }
});

document.getElementById('bulkRejectBtn')?.addEventListener('click', function() {
    const articleIds = pendingCheckedIds();
    if (articleIds.length && confirm(`Tolak ${articleIds.length} artikel terpilih?`)) {
        bulkAction('reject', articleIds);
    }
});

function bulkAction(action, articleIds) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('article_ids', JSON.stringify(articleIds));

    fetch(`/admin/articles/bulk-${action}`, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Terjadi kesalahan: ' + data.message);
        })
        .catch(() => alert('Terjadi kesalahan saat memproses permintaan'));
}

function rejectArticle(articleId) {
    currentArticleId = articleId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectReason').value = '';
    currentArticleId = null;
}

document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch(`/admin/articles/${currentArticleId}/reject`, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeRejectModal();
                location.reload();
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(() => alert('Terjadi kesalahan saat menolak artikel'));
});
</script>
@endpush
@endsection
