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
               class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-red-800 transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Artikel
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="{{ route('admin.articles.index', array_merge(request()->except('tab'), ['tab' => 'my'])) }}"
                   class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ ($tab ?? 'all') === 'my' ? 'border-news-accent text-news-accent' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-user-edit mr-2"></i>
                    Artikel Saya
                </a>
                <a href="{{ route('admin.articles.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}"
                   class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ ($tab ?? 'all') === 'all' ? 'border-news-accent text-news-accent' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-list mr-2"></i>
                    Semua Artikel
                </a>
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
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
                <select name="featured" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Non-Featured</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
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
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-red-800 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.articles.index', ['tab' => $tab ?? 'all']) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Bulk uses articles[] (not ids[]) — matches AdminArticleController@bulkAction --}}
    <x-admin.table :paginator="$articles" :bulk="true" bulk-id="articles-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.articles.bulk')"
                bulk-id="articles-bulk"
                :options="['publish' => 'Publikasi', 'draft' => 'Ubah ke Draft', 'featured' => 'Tandai Featured', 'delete' => 'Hapus']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="articles-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="title" label="Artikel" />
            <x-admin.th :sortable="false" label="Kategori" />
            <x-admin.th column="status" label="Status" />
            <x-admin.th :sortable="false" label="Penulis" />
            <x-admin.th column="created_at" label="Dibuat" />
            <x-admin.th column="is_featured" label="Featured/Breaking" />
            <x-admin.th :sortable="false" label="Alasan Penolakan" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($articles as $article)
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox :value="$article->id" bulk-id="articles-bulk" name="articles[]" />
                <x-admin.td-number :index="$articles->firstItem() + $loop->index" />
                <td class="px-4 py-4">
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
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $article->category->name ?? 'Tidak ada kategori' }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
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
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">{{ $article->author->name ?? 'Sistem' }}</span>
                        @if($article->author)
                            <x-user-role-badge :user="$article->author" size="xs" />
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $article->created_at->format('d-m-Y H:i') }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('admin.articles.toggle-featured', $article) }}" method="POST" class="inline toggle-featured-form" data-article-id="{{ $article->id }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all {{ $article->is_featured ? 'text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:bg-gray-50' }}"
                                    title="{{ $article->is_featured ? 'Hapus Featured' : 'Tandai Featured' }}">
                                <i class="fas fa-star text-sm"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST" class="inline toggle-breaking-form" data-article-id="{{ $article->id }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all {{ $article->is_breaking ? 'text-red-600 hover:bg-red-50' : 'text-gray-400 hover:bg-gray-50' }}"
                                    title="{{ $article->is_breaking ? 'Hapus Breaking' : 'Tandai Breaking' }}">
                                <i class="fas fa-bolt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($article->status === 'rejected' && $article->rejection_reason)
                        <span class="text-red-600" title="{{ $article->rejection_reason }}">
                            {{ Str::limit($article->rejection_reason, 30) }}
                        </span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <x-admin.actions>
                    @php $isMyArticle = $article->author_id === auth()->id(); @endphp

                    <x-admin.action-icon
                        :href="route('admin.articles.show', $article)"
                        icon="fas fa-eye"
                        color="blue"
                        title="Lihat Detail"
                    />

                    @if($isMyArticle)
                        <x-admin.action-icon
                            :href="route('admin.articles.edit', $article)"
                            icon="fas fa-edit"
                            color="green"
                            title="Edit"
                        />
                    @else
                        @if($article->status === 'pending_review')
                            <x-admin.action-icon
                                :href="route('admin.articles.detail', $article)"
                                icon="fas fa-clipboard-check"
                                color="amber"
                                title="Review Artikel"
                            />
                        @endif
                        @if($article->status !== 'archived')
                            <x-admin.action-icon
                                :href="route('admin.articles.archive', $article)"
                                method="POST"
                                icon="fas fa-archive"
                                color="purple"
                                title="Arsipkan"
                                confirm="Apakah Anda yakin ingin mengarsipkan artikel ini? Penulis akan diberi kesempatan untuk mereview kembali tulisannya."
                            />
                        @endif
                    @endif

                    <x-admin.action-icon
                        :href="route('admin.articles.destroy', $article)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan."
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-newspaper text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Belum ada artikel</p>
                    <p class="text-sm">Mulai buat artikel pertama Anda</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-featured-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const button = form.querySelector('button');
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            })
            .then(response => {
                if (response.ok) return response.json().catch(() => ({ success: true }));
                throw new Error('Network response was not ok');
            })
            .then(() => location.reload())
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Terjadi kesalahan saat memperbarui status featured');
            });
        });
    });

    document.querySelectorAll('.toggle-breaking-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const button = form.querySelector('button');
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            })
            .then(response => {
                if (response.ok) return response.json().catch(() => ({ success: true }));
                throw new Error('Network response was not ok');
            })
            .then(() => location.reload())
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Terjadi kesalahan saat memperbarui status breaking');
            });
        });
    });
});
</script>
@endpush
@endsection
