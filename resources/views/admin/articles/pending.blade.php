@extends('layouts.admin-simple')

@section('page-title', 'Artikel Menunggu Persetujuan')
@section('page-subtitle', 'Tinjau dan setujui artikel yang diajukan penulis')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
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

    <!-- Filter and Search -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.articles.pending') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                  <!-- Search -->
                  <div>
                      <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Artikel</label>
                      <input type="text" 
                             name="search" 
                             id="search"
                             value="{{ request('search') }}"
                             placeholder="Judul atau penulis..."
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  
                  <!-- Category Filter -->
                  <div>
                      <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                      <select name="category" 
                              id="category"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                          <option value="">Semua Kategori</option>
                          @foreach(\App\Models\Category::all() as $category)
                              <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                  {{ $category->name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  
                  <!-- Date Filter -->
                  <div>
                      <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                      <input type="date" 
                             name="date_from" 
                             id="date_from"
                             value="{{ request('date_from') }}"
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  
                  <div>
                      <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                      <input type="date" 
                             name="date_to" 
                             id="date_to"
                             value="{{ request('date_to') }}"
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  
                  <!-- Filter Button -->
                  <div class="flex items-end">
                      <button type="submit" 
                              class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                          <i class="fas fa-search mr-2"></i>
                          Filter
                      </button>
                  </div>
                  
                  <!-- Reset Button -->
                  <div class="flex items-end">
                      <a href="{{ route('admin.articles.pending') }}" 
                         class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center">
                          <i class="fas fa-times mr-2"></i>
                          Reset
                      </a>
                  </div>
              </div>
              
              <div class="flex items-center justify-end">
                  <div class="text-sm text-gray-500">
                      Menampilkan {{ $articles->count() }} dari {{ $articles->total() }} artikel
                  </div>
              </div>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Artikel Menunggu Persetujuan</h3>
            
            <!-- Bulk Actions -->
            <div class="flex items-center space-x-2">
                <button type="button" 
                        id="selectAllBtn"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Pilih Semua
                </button>
                <button type="button" 
                        id="bulkApproveBtn"
                        class="bg-green-600 text-white p-2 rounded text-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                        title="Setujui Terpilih">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" 
                        id="bulkRejectBtn"
                        class="bg-red-600 text-white p-2 rounded text-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                        title="Tolak Terpilih">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Artikel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penulis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Diajukan
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
                            <input type="checkbox" 
                                   class="article-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="{{ $article->id }}">
                        </td>
                        <td class="px-6 py-4">
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
                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">
                                        {{ $article->title }}
                                    </h4>
                                    <p class="text-sm text-gray-500 line-clamp-2">
                                        {{ Str::limit(strip_tags($article->content), 100) }}
                                    </p>
                                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                        <span class="flex items-center space-x-1">
                                            <i class="fas fa-eye"></i>
                                            <span>{{ number_format($article->views) }}</span>
                                        </span>
                                        @if($article->is_featured)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>
                                            Featured
                                        </span>
                                        @endif
                                        @if($article->is_breaking)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-bolt mr-1"></i>
                                            Breaking
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                                                <a href="{{ route('penulis.public-profile', $article->author->username) }}" class="text-blue-600 hover:text-blue-800 font-medium" target="_blank">
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $article->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $article->created_at->format('d-m-Y') }}</div>
                            <div class="text-xs">{{ $article->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.articles.detail', $article) }}" 
                               class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                               title="Review Artikel">
                                <i class="fas fa-eye mr-2"></i>
                                Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-newspaper text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Artikel</h3>
                            <p class="text-gray-500">Tidak ada artikel yang menunggu persetujuan saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($articles->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>


<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tolak Artikel</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="rejectForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan
                        </label>
                        <textarea id="rejectReason" 
                                  name="reason" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Berikan alasan mengapa artikel ini ditolak..."
                                  required></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" 
                            onclick="closeRejectModal()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fas fa-times mr-2"></i>
                        Tolak Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
let currentArticleId = null;

// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.article-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkButtons();
});

// Individual checkbox change
document.querySelectorAll('.article-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkButtons);
});

function updateBulkButtons() {
    const checkedBoxes = document.querySelectorAll('.article-checkbox:checked');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    
    if (checkedBoxes.length > 0) {
        bulkApproveBtn.disabled = false;
        bulkRejectBtn.disabled = false;
    } else {
        bulkApproveBtn.disabled = true;
        bulkRejectBtn.disabled = true;
    }
}

// Bulk actions
document.getElementById('bulkApproveBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.article-checkbox:checked');
    const articleIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (confirm(`Setujui ${articleIds.length} artikel terpilih?`)) {
        bulkAction('approve', articleIds);
    }
});

document.getElementById('bulkRejectBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.article-checkbox:checked');
    const articleIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (confirm(`Tolak ${articleIds.length} artikel terpilih?`)) {
        bulkAction('reject', articleIds);
    }
});

function bulkAction(action, articleIds) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('article_ids', JSON.stringify(articleIds));
    
    fetch(`/admin/articles/bulk-${action}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses permintaan');
    });
}

// Reject article
function rejectArticle(articleId) {
    console.log('rejectArticle called with ID:', articleId);
    currentArticleId = articleId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectReason').value = '';
    currentArticleId = null;
}

// Reject form submission
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch(`/admin/articles/${currentArticleId}/reject`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeRejectModal();
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menolak artikel');
    });
});

</script>
@endsection