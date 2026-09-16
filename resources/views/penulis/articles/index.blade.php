@extends('layouts.penulis')

@section('title', 'Artikel Saya')
@section('page-title', 'Artikel Saya')
@section('page-subtitle', 'Kelola semua artikel Anda')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">
                Artikel Saya
            </h1>
            <p class="text-news-muted mt-1">
                Total: <span class="font-semibold text-news-ink">{{ $articles->total() }}</span> artikel
            </p>
        </div>
        <a href="{{ route('penulis.articles.create') }}"
           class="inline-flex items-center bg-news-ink hover:bg-news-accent text-white px-4 py-2.5 text-sm font-semibold transition-colors shrink-0">
            <i class="fas fa-plus mr-2 text-xs"></i>
            Buat Artikel
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-news-line p-4 sm:p-5">
        <h2 class="text-sm font-bold uppercase tracking-wider text-news-ink mb-4">Filter & Pencarian</h2>
        <form method="GET" action="{{ route('penulis.articles.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif

            <div>
                <label for="search" class="block text-sm font-medium text-news-muted mb-1.5">Cari Artikel</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul artikel..."
                    class="w-full border border-news-line px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"
                >
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-news-muted mb-1.5">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full border border-news-line px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent bg-white"
                >
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Terbit</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-news-muted mb-1.5">Kategori</label>
                <select
                    id="category"
                    name="category"
                    class="w-full border border-news-line px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent bg-white"
                >
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-news-ink hover:bg-news-accent text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'category', 'sort', 'direction']))
                    <a href="{{ route('penulis.articles.index') }}"
                       class="inline-flex items-center border border-news-line px-4 py-2.5 text-sm font-semibold text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <x-admin.table :paginator="$articles" :bulk="true" bulk-id="penulis-articles-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('penulis.articles.bulk')"
                bulk-id="penulis-articles-bulk"
                :options="[
                    'submit' => auth()->user()->isVerified() ? 'Terbitkan' : 'Ajukan Review',
                    'draft' => 'Ubah ke Draft',
                    'delete' => 'Hapus',
                ]"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="penulis-articles-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="title" label="Judul" />
            <x-admin.th :sortable="false" label="Kategori" />
            <x-admin.th column="status" label="Status" />
            <x-admin.th column="views" label="Views" />
            <x-admin.th column="comments_count" label="Komentar" />
            <x-admin.th column="created_at" label="Tanggal" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($articles as $article)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$article->id" bulk-id="penulis-articles-bulk" name="articles[]" />
                <x-admin.td-number :index="$articles->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-news-ink line-clamp-2 max-w-xs" title="{{ $article->title }}">
                        {{ $article->title }}
                    </div>
                    @if($article->tags->count() > 0)
                        <div class="mt-1.5 flex flex-wrap gap-1">
                            @foreach($article->tags->take(3) as $tag)
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] font-medium text-news-muted bg-news-paper rounded">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                            @if($article->tags->count() > 3)
                                <span class="text-[11px] text-news-muted">+{{ $article->tags->count() - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                        {{ $article->category->name ?? 'Tanpa kategori' }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($article->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            Terbit
                        </span>
                    @elseif($article->status === 'suspended')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-800">
                            Ditangguhkan
                        </span>
                    @elseif($article->status === 'draft')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            Draft
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-news-ink tabular-nums">
                    {{ number_format($article->views) }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <a href="{{ route('penulis.articles.comments', $article) }}"
                       class="text-sm font-semibold text-news-accent hover:text-news-ink transition-colors tabular-nums">
                        {{ $article->comments_count ?? 0 }}
                    </a>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
                    {{ $article->created_at->format('d M Y') }}
                </td>
                <x-admin.actions>
                    <x-admin.action-icon
                        :href="route('penulis.articles.show', $article)"
                        icon="fas fa-eye"
                        color="blue"
                        title="Lihat Detail"
                    />
                    <x-admin.action-icon
                        :href="route('penulis.articles.edit', $article)"
                        icon="fas fa-edit"
                        color="green"
                        title="Edit"
                    />
                    <x-admin.action-icon
                        :href="route('penulis.articles.duplicate', $article)"
                        method="POST"
                        icon="fas fa-copy"
                        color="indigo"
                        title="Duplicate"
                    />
                    <x-admin.action-icon
                        :href="route('penulis.articles.export', $article)"
                        icon="fas fa-download"
                        color="gray"
                        title="Export"
                        target="_blank"
                    />
                    <x-admin.action-icon
                        :href="route('penulis.articles.destroy', $article)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus artikel ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-newspaper text-4xl mb-4 text-news-line"></i>
                    <p class="text-lg font-medium text-news-ink mb-1">
                        @if(request()->hasAny(['search', 'status', 'category']))
                            Tidak ada artikel yang sesuai filter
                        @else
                            Belum ada artikel
                        @endif
                    </p>
                    <p class="text-sm mb-4">
                        @if(request()->hasAny(['search', 'status', 'category']))
                            <a href="{{ route('penulis.articles.index') }}" class="text-news-accent hover:text-news-ink font-medium">Reset filter</a>
                            untuk melihat semua artikel
                        @else
                            Mulai menulis artikel pertama Anda.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('penulis.articles.create') }}"
                           class="inline-flex items-center bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Artikel Pertama
                        </a>
                    @endif
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
