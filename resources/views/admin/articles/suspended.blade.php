@extends('layouts.admin-simple')

@section('title', 'Artikel Ditangguhkan')
@section('page-title', 'Artikel Ditangguhkan')
@section('page-subtitle', 'Kumpulan artikel yang penayangannya ditangguhkan redaksi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Artikel Ditangguhkan</h3>
            <p class="text-sm text-news-muted">
                Total {{ $articles->total() }} artikel ditangguhkan
            </p>
        </div>
    </div>

    <div class="bg-white border border-news-line p-4">
        <form method="GET" action="{{ route('admin.articles.suspended') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul, konten, penulis…"
                       class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.articles.suspended') }}" class="bg-news-ink text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <x-admin.table :paginator="$articles">
        <x-slot:head>
            <x-admin.th :sortable="false" label="#" align="center" class="w-14 admin-col-optional" />
            <x-admin.th column="title" label="Artikel" />
            <x-admin.th :sortable="false" label="Penulis" class="admin-col-optional" />
            <x-admin.th :sortable="false" label="Alasan" />
            <x-admin.th column="suspended_at" label="Ditangguhkan" class="admin-col-optional" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($articles as $article)
            <tr class="hover:bg-news-paper transition-colors">
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
                            </div>
                            <div class="text-sm text-news-muted line-clamp-1">
                                {{ $article->category->name ?? 'Tanpa kategori' }}
                            </div>
                            <div class="mt-1 text-xs text-news-muted md:hidden">
                                {{ $article->author->name ?? 'Sistem' }}
                                · {{ $article->suspended_at?->format('d-m-Y H:i') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-news-ink">{{ $article->author->name ?? 'Sistem' }}</span>
                        @if($article->author)
                            <x-user-role-badge :user="$article->author" size="xs" />
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 text-sm text-news-muted max-w-xs">
                    @if($article->suspension_reason)
                        <span class="text-orange-700" title="{{ $article->suspension_reason }}">
                            {{ Str::limit($article->suspension_reason, 60) }}
                        </span>
                    @else
                        <span class="text-news-muted">—</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted admin-col-optional">
                    {{ $article->suspended_at?->format('d-m-Y H:i') ?? '—' }}
                    @if($article->suspendedBy)
                        <div class="text-xs text-news-muted mt-0.5">oleh {{ $article->suspendedBy->name }}</div>
                    @endif
                </td>
                <x-admin.actions>
                    <x-admin.action-icon
                        :href="route('admin.articles.detail', $article)"
                        icon="fas fa-eye"
                        color="blue"
                        title="Lihat Detail"
                    />
                    <x-admin.action-icon
                        :href="route('admin.articles.unsuspend', $article)"
                        method="POST"
                        icon="fas fa-play-circle"
                        color="green"
                        title="Pulihkan Penayangan"
                        confirm="Pulihkan penayangan artikel ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-pause-circle text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Tidak ada artikel ditangguhkan</p>
                    <p class="text-sm">Artikel yang ditangguhkan akan muncul di sini</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
