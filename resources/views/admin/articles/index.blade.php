@extends('layouts.admin-simple')

@section('title', 'Manajemen Artikel - Admin Panel')
@section('page-title', 'Manajemen Artikel')
@section('page-subtitle', 'Kelola artikel berita dan konten website')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Artikel</h3>
            <p class="text-sm text-news-muted">
                Total {{ $articles->total() }} artikel
                @if(request('status'))
                    - Filter: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.articles.create') }}"
               class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Artikel
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white border border-news-line">
        <div class="border-b border-news-line">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="{{ route('admin.articles.index', array_merge(request()->except('tab'), ['tab' => 'my'])) }}"
                   class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ ($tab ?? 'all') === 'my' ? 'border-news-accent text-news-accent' : 'border-transparent text-news-muted hover:text-news-ink hover:border-news-line' }}">
                    <i class="fas fa-user-edit mr-2"></i>
                    Artikel Saya
                </a>
                <a href="{{ route('admin.articles.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}"
                   class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ ($tab ?? 'all') === 'all' ? 'border-news-accent text-news-accent' : 'border-transparent text-news-muted hover:text-news-ink hover:border-news-line' }}">
                    <i class="fas fa-list mr-2"></i>
                    Semua Artikel
                </a>
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-news-line p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Status</label>
                <select name="status" class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasi</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Featured</label>
                <select name="featured" class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Non-Featured</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Kategori</label>
                <select name="category" class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul artikel..."
                       class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.articles.index', ['tab' => $tab ?? 'all']) }}" class="bg-news-ink text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors">
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
                :options="['publish' => 'Publikasi (draft)', 'draft' => 'Ubah ke Draft', 'featured' => 'Tandai Featured', 'delete' => 'Hapus']"
            >
                <label class="inline-flex items-center gap-2 text-xs text-news-muted">
                    <input type="checkbox" name="fact_checked" value="1"
                           class="rounded border-news-line text-news-accent focus:ring-news-accent/30">
                    <span>Sudah cek fakta (wajib untuk Publikasi)</span>
                </label>
            </x-admin.bulk-bar>
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="articles-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14 admin-col-optional" />
            <x-admin.th column="title" label="Artikel" />
            <x-admin.th :sortable="false" label="Kategori" class="admin-col-optional" />
            <x-admin.th column="status" label="Status" />
            <x-admin.th :sortable="false" label="Penulis" class="admin-col-optional" />
            <x-admin.th column="created_at" label="Dibuat" class="admin-col-optional" />
            <x-admin.th column="is_featured" label="Featured/Breaking" class="admin-col-optional" />
            <x-admin.th :sortable="false" label="Catatan" class="admin-col-optional" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($articles as $article)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$article->id" bulk-id="articles-bulk" name="articles[]" />
                <x-admin.td-number :index="$articles->firstItem() + $loop->index" class="admin-col-optional" />
                <td class="px-4 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-lg object-cover"
                                 src="{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg') }}"
                                 alt="{{ $article->title }}">
                        </div>
                        <div class="ml-4 min-w-0">
                            <div class="text-sm font-medium text-news-ink line-clamp-2">
                                {{ $article->title }}
                                @if($article->is_featured)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-800">
                                        <i class="fas fa-star mr-1"></i>Featured
                                    </span>
                                @endif
                                @if($article->is_breaking)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-news-accent">
                                        <i class="fas fa-bolt mr-1"></i>Breaking
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-news-muted line-clamp-1">
                                {{ Str::limit($article->excerpt, 60) }}
                            </div>
                            <div class="mt-1 text-xs text-news-muted md:hidden">
                                {{ $article->author->name ?? 'Sistem' }}
                                · {{ $article->category->name ?? 'Tanpa kategori' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                        {{ $article->category->name ?? 'Tidak ada kategori' }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($article->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Dipublikasi
                        </span>
                    @elseif($article->status === 'suspended')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-800">
                            <i class="fas fa-pause-circle mr-1"></i>
                            Ditangguhkan
                        </span>
                    @elseif($article->status === 'archived')
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-news-paper text-news-ink">
                            <i class="fas fa-archive mr-1"></i>
                            Diarsipkan
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            <i class="fas fa-edit mr-1"></i>
                            Draft
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-news-ink">{{ $article->author->name ?? 'Sistem' }}</span>
                        @if($article->author)
                            <x-user-role-badge :user="$article->author" size="xs" />
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted admin-col-optional">
                    {{ $article->created_at->format('d-m-Y H:i') }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                    <div class="flex items-center space-x-2">
                        @can('curate', $article)
                        <form action="{{ route('admin.articles.toggle-featured', $article) }}" method="POST" class="inline toggle-featured-form" data-article-id="{{ $article->id }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all {{ $article->is_featured ? 'text-amber-600 hover:bg-amber-50' : 'text-news-muted hover:bg-news-paper' }}"
                                    title="{{ $article->is_featured ? 'Hapus Featured' : 'Tandai Featured' }}">
                                <i class="fas fa-star text-sm"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST" class="inline toggle-breaking-form" data-article-id="{{ $article->id }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all {{ $article->is_breaking ? 'text-red-600 hover:bg-red-50' : 'text-news-muted hover:bg-news-paper' }}"
                                    title="{{ $article->is_breaking ? 'Hapus Breaking' : 'Tandai Breaking' }}">
                                <i class="fas fa-bolt text-sm"></i>
                            </button>
                        </form>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs text-news-muted" title="Tidak punya izin mengubah status featured/breaking">
                                @if($article->is_featured)<i class="fas fa-star text-amber-500"></i>@endif
                                @if($article->is_breaking)<i class="fas fa-bolt text-red-500"></i>@endif
                                @if(!$article->is_featured && !$article->is_breaking)—@endif
                            </span>
                        @endcan
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted admin-col-optional">
                    @if($article->status === 'suspended' && $article->suspension_reason)
                        <span class="text-orange-700" title="{{ $article->suspension_reason }}">
                            {{ Str::limit($article->suspension_reason, 30) }}
                        </span>
                    @else
                        <span class="text-news-muted">—</span>
                    @endif
                </td>
                <x-admin.actions>
                    @php $isMyArticle = (int) $article->author_id === (int) auth()->id(); @endphp

                    <x-admin.action-icon
                        :href="route('admin.articles.show', $article)"
                        icon="fas fa-eye"
                        color="blue"
                        title="Lihat Detail"
                    />

                    @can('suspend', $article)
                        @if($article->status === 'published')
                            <x-admin.action-icon
                                :href="route('admin.articles.detail', $article)"
                                icon="fas fa-pause-circle"
                                color="orange"
                                title="Tangguhkan / Moderasi"
                            />
                        @elseif($article->status === 'suspended')
                            <x-admin.action-icon
                                :href="route('admin.articles.unsuspend', $article)"
                                method="POST"
                                icon="fas fa-play-circle"
                                color="green"
                                title="Pulihkan Penayangan"
                                confirm="Pulihkan penayangan artikel ini?"
                            />
                        @endif
                    @endcan

                    @if($isMyArticle)
                        <x-admin.action-icon
                            :href="route('admin.articles.edit', $article)"
                            icon="fas fa-edit"
                            color="green"
                            title="Edit"
                        />
                        @if($article->status !== 'archived')
                            <x-admin.action-icon
                                :href="route('admin.articles.archive', $article)"
                                method="POST"
                                icon="fas fa-archive"
                                color="purple"
                                title="Arsipkan"
                                confirm="Apakah Anda yakin ingin mengarsipkan artikel ini?"
                            />
                        @endif
                        <x-admin.action-icon
                            :href="route('admin.articles.destroy', $article)"
                            method="DELETE"
                            icon="fas fa-trash"
                            color="red"
                            title="Hapus"
                            confirm="Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan."
                        />
                    @endif
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-newspaper text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada artikel</p>
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
