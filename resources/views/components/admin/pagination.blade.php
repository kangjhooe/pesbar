@props(['paginator'])

@if($paginator && method_exists($paginator, 'total'))
<div class="px-4 py-3.5 bg-news-paper border-t border-news-line flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-sm text-news-muted">
        @if($paginator->total() > 0)
            Menampilkan
            <span class="font-semibold text-news-ink">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-semibold text-news-ink">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-news-ink">{{ number_format($paginator->total()) }}</span>
        @else
            Tidak ada data
        @endif
    </p>
    @if($paginator->hasPages())
        <div class="admin-table-pagination">
            {{ $paginator->withQueryString()->links() }}
        </div>
    @endif
</div>
@endif
