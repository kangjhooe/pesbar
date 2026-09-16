@extends('layouts.admin-simple')

@section('title', 'Tangguhkan Artikel')
@section('page-title', 'Tangguhkan Artikel')
@section('page-subtitle', 'Cari artikel tayang dan tangguhkan tanpa menunggu laporan')

@section('content')
<div class="space-y-6">
    <div class="border border-news-line border-t-2 border-t-orange-500 bg-white px-4 py-4 sm:px-5">
        <p class="text-sm text-news-muted">
            Cari judul / penulis, lalu tangguhkan. Atau buka halaman publik artikel — tombol <strong>Tangguhkan</strong> muncul jika Anda login sebagai admin/editor.
        </p>
    </div>

    <form method="GET" action="{{ route('admin.articles.moderate') }}" class="border border-news-line bg-white p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-news-muted mb-1">Cari artikel tayang</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full border border-news-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-news-accent focus:border-news-accent"
                       placeholder="Judul, slug, atau nama penulis…"
                       autofocus>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">Cari</button>
                <a href="{{ route('admin.articles.moderate') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="border border-news-line bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-news-line">
                <thead>
                    <tr class="bg-news-paper">
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Judul</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Penulis</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Terbit</th>
                        <th class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-news-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-news-line">
                    @forelse($articles as $article)
                        <tr class="hover:bg-news-paper/60">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.articles.detail', $article) }}" class="text-sm font-medium text-news-ink hover:text-news-accent line-clamp-2">
                                    {{ $article->title }}
                                </a>
                                @if($article->category)
                                    <p class="text-xs text-news-muted mt-0.5">{{ $article->category->name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-news-ink">
                                {{ $article->author->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-news-muted">
                                {{ $article->published_at?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2 justify-end flex-wrap">
                                    <a href="{{ $article->publicUrl() }}" target="_blank" class="text-xs font-semibold text-news-muted hover:text-news-ink">Publik</a>
                                    <x-suspend-article-button :article="$article" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-news-muted">
                                @if(request('search'))
                                    <p class="text-sm font-medium text-news-ink">Tidak ada artikel tayang yang cocok</p>
                                @else
                                    <p class="text-sm font-medium text-news-ink">Ketik kata kunci untuk mencari artikel tayang</p>
                                    <p class="text-xs mt-1">Menampilkan 20 artikel terbaru jika tanpa filter</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
            <div class="px-4 py-3 border-t border-news-line">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
