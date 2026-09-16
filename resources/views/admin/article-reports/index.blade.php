@extends('layouts.admin-simple')

@section('title', 'Laporan Artikel')
@section('page-title', 'Laporan Artikel')
@section('page-subtitle', 'Tinjau laporan dari pembaca (login & tamu)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.article-reports.index', ['status' => 'open']) }}"
           class="px-3 py-1.5 text-sm border {{ $status === 'open' ? 'bg-news-ink text-white border-news-ink' : 'border-news-line text-news-ink hover:bg-news-paper' }}">
            Terbuka @if($openCount > 0)({{ $openCount }})@endif
        </a>
        <a href="{{ route('admin.article-reports.index', ['status' => 'resolved']) }}"
           class="px-3 py-1.5 text-sm border {{ $status === 'resolved' ? 'bg-news-ink text-white border-news-ink' : 'border-news-line text-news-ink hover:bg-news-paper' }}">
            Selesai
        </a>
        <a href="{{ route('admin.article-reports.index', ['status' => 'dismissed']) }}"
           class="px-3 py-1.5 text-sm border {{ $status === 'dismissed' ? 'bg-news-ink text-white border-news-ink' : 'border-news-line text-news-ink hover:bg-news-paper' }}">
            Diabaikan
        </a>
    </div>

    <x-admin.table :paginator="$reports">
        <x-slot:head>
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th :sortable="false" label="Artikel" />
            <x-admin.th :sortable="false" label="Pelapor" />
            <x-admin.th :sortable="false" label="Alasan" />
            <x-admin.th column="created_at" label="Dilaporkan" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($reports as $report)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.td-number :index="$reports->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    @if($report->article)
                        <a href="{{ route('admin.articles.show', $report->article) }}" class="text-sm font-medium text-news-accent hover:text-news-ink">
                            {{ Str::limit($report->article->title, 60) }}
                        </a>
                        <div class="text-xs text-news-muted mt-0.5">
                            Status: {{ $report->article->status }}
                            @if($report->article->author)
                                · {{ $report->article->author->name }}
                            @endif
                        </div>
                    @else
                        <span class="text-sm text-news-muted">Artikel dihapus</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-news-ink">
                    {{ $report->reporterLabel() }}
                </td>
                <td class="px-4 py-4 text-sm text-news-ink max-w-xs">
                    <div class="font-medium">{{ $report->reasonLabel() }}</div>
                    @if($report->details)
                        <div class="text-news-muted mt-1">{{ Str::limit($report->details, 120) }}</div>
                    @endif
                    @if($report->resolution_note && $report->status !== 'open')
                        <div class="text-xs text-emerald-700 mt-1">Catatan: {{ $report->resolution_note }}</div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
                    {{ $report->created_at?->format('d M Y, H:i') }}
                </td>
                <td class="px-4 py-4 text-right">
                    @if($report->status === 'open' && $report->article)
                        <div class="inline-flex flex-col items-end gap-2">
                            <form action="{{ route('admin.article-reports.resolve', $report) }}" method="POST" class="flex flex-col items-end gap-1">
                                @csrf
                                <input type="hidden" name="action" value="suspend">
                                <input type="text" name="reason" required maxlength="1000"
                                       placeholder="Alasan penangguhan"
                                       class="w-56 text-xs border border-news-line px-2 py-1">
                                <button type="submit"
                                        class="text-xs px-2 py-1 bg-news-accent text-white hover:bg-news-ink"
                                        onclick="return confirm('Tangguhkan penayangan artikel ini?')">
                                    Tangguhkan
                                </button>
                            </form>
                            <form action="{{ route('admin.article-reports.resolve', $report) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="resolve">
                                <button type="submit" class="text-xs text-emerald-700 hover:underline"
                                        onclick="return confirm('Tandai laporan selesai tanpa menangguhkan?')">
                                    Selesai
                                </button>
                            </form>
                            <form action="{{ route('admin.article-reports.dismiss', $report) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-news-muted hover:underline"
                                        onclick="return confirm('Abaikan laporan ini?')">
                                    Abaikan
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-xs text-news-muted uppercase">{{ $report->status }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-flag text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Tidak ada laporan</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
