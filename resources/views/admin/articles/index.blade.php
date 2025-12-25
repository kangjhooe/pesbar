@extends('layouts.admin-simple')

@section('title', 'Manajemen Artikel - Admin Panel')
@section('page-title', 'Manajemen Artikel')
@section('page-subtitle', 'Kelola artikel berita dan konten website')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Daftar Artikel</h3>
            <p class="text-sm text-gray-600">
                Total {{ $articles->total() }} artikel
                @if(request('status'))
                    - Filter: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.articles.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Artikel
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasi</option>
                    <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
                <select name="featured" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Non-Featured</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari judul artikel..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.articles.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4" id="bulk-actions" style="display: none;">
        <form id="bulk-form" method="POST" action="{{ route('admin.articles.bulk') }}">
            @csrf
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600" id="selected-count">0 artikel dipilih</span>
                <select name="action" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Aksi</option>
                    <option value="publish">Publikasi</option>
                    <option value="draft">Ubah ke Draft</option>
                    <option value="featured">Tandai Featured</option>
                    <option value="delete">Hapus</option>
                </select>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Jalankan
                </button>
                <button type="button" onclick="clearSelection()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Artikel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penulis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Featured/Breaking
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Alasan Penolakan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($articles as $article)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="articles[]" value="{{ $article->id }}" 
                                   class="article-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <img class="h-12 w-12 rounded-lg object-cover" 
                                         src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}" 
                                         alt="{{ $article->title }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 line-clamp-2">
                                        {{ $article->title }}
                                        @if($article->is_featured)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-star mr-1"></i>Featured
                                            </span>
                                        @endif
                                        @if($article->is_breaking)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-bolt mr-1"></i>Breaking
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ Str::limit($article->excerpt, 60) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $article->category->name ?? 'Tidak ada kategori' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($article->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Dipublikasi
                                </span>
                            @elseif($article->status === 'pending_review')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Menunggu Review
                                </span>
                            @elseif($article->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Ditolak
                                </span>
                            @elseif($article->status === 'archived')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-archive mr-1"></i>
                                    Diarsipkan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-edit mr-1"></i>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-900">{{ $article->user->name ?? 'Sistem' }}</span>
                                @if($article->user)
                                    <x-user-role-badge :user="$article->user" size="xs" />
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <form action="{{ route('admin.articles.toggle-featured', $article) }}" method="POST" class="inline toggle-featured-form" data-article-id="{{ $article->id }}">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $article->is_featured ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            title="{{ $article->is_featured ? 'Hapus Featured' : 'Tandai Featured' }}">
                                        <i class="fas fa-star {{ $article->is_featured ? 'text-yellow-500' : 'text-gray-400' }}"></i>
                                        <span class="ml-1">{{ $article->is_featured ? 'Featured' : 'Featured' }}</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST" class="inline toggle-breaking-form" data-article-id="{{ $article->id }}">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $article->is_breaking ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            title="{{ $article->is_breaking ? 'Hapus Breaking' : 'Tandai Breaking' }}">
                                        <i class="fas fa-bolt {{ $article->is_breaking ? 'text-red-500' : 'text-gray-400' }}"></i>
                                        <span class="ml-1">{{ $article->is_breaking ? 'Breaking' : 'Breaking' }}</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($article->status === 'rejected' && $article->rejection_reason)
                                <span class="text-red-600" title="{{ $article->rejection_reason }}">
                                    {{ Str::limit($article->rejection_reason, 30) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.articles.show', $article) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 rounded-md transition-colors" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $article)
                                <a href="{{ route('admin.articles.edit', $article) }}" 
                                   class="text-green-600 hover:text-green-900 p-2 rounded-md transition-colors" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @if($article->status !== 'archived')
                                <form action="{{ route('admin.articles.archive', $article) }}" 
                                      method="POST" class="inline archive-form" 
                                      data-article-id="{{ $article->id }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin mengarsipkan artikel ini? Penulis akan diberi kesempatan untuk mereview kembali tulisannya.')">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-900 p-2 rounded-md transition-colors" title="Arsipkan (beri kesempatan penulis untuk review)">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.articles.destroy', $article) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2 rounded-md transition-colors" title="Hapus (jika melanggar kode etik jurnalistik)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-newspaper text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada artikel</p>
                                <p class="text-sm">Mulai buat artikel pertama Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($articles->hasPages())
    <div class="flex justify-center">
        {{ $articles->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const articleCheckboxes = document.querySelectorAll('.article-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-form');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        articleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox change
    articleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.article-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = `${count} artikel dipilih`;
            
            // Update form with selected articles
            const form = bulkForm;
            form.innerHTML = form.innerHTML.replace(/<input[^>]*name="articles\[\]"[^>]*>/g, '');
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'articles[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
        } else {
            bulkActions.style.display = 'none';
        }
    }

    // Bulk form submission
    bulkForm.addEventListener('submit', function(e) {
        const action = this.querySelector('select[name="action"]').value;
        if (!action) {
            e.preventDefault();
            alert('Pilih aksi yang akan dilakukan');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm('Apakah Anda yakin ingin menghapus artikel yang dipilih?')) {
                e.preventDefault();
                return;
            }
        }
    });

    // Toggle Featured with AJAX for better UX
    document.querySelectorAll('.toggle-featured-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const button = form.querySelector('button');
            const articleId = form.dataset.articleId;
            const originalHTML = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.json().catch(() => ({ success: true }));
                }
                throw new Error('Network response was not ok');
            })
            .then(data => {
                // Reload page to show updated state
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Terjadi kesalahan saat memperbarui status featured');
            });
        });
    });

    // Toggle Breaking with AJAX for better UX
    document.querySelectorAll('.toggle-breaking-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const button = form.querySelector('button');
            const articleId = form.dataset.articleId;
            const originalHTML = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.json().catch(() => ({ success: true }));
                }
                throw new Error('Network response was not ok');
            })
            .then(data => {
                // Reload page to show updated state
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Terjadi kesalahan saat memperbarui status breaking');
            });
        });
    });

    // Archive form with AJAX for better UX
    document.querySelectorAll('.archive-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const button = form.querySelector('button');
            const articleId = form.dataset.articleId;
            const originalHTML = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.json().catch(() => ({ success: true }));
                }
                throw new Error('Network response was not ok');
            })
            .then(data => {
                // Reload page to show updated state
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Terjadi kesalahan saat mengarsipkan artikel');
            });
        });
    });
});

function clearSelection() {
    document.querySelectorAll('.article-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('bulk-actions').style.display = 'none';
}
</script>
@endpush
@endsection
