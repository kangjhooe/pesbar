@props(['paginator'])

@if($paginator && method_exists($paginator, 'total'))
<div class="px-4 py-3.5 bg-slate-50/80 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-sm text-gray-600">
        @if($paginator->total() > 0)
            Menampilkan
            <span class="font-semibold text-gray-800">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-semibold text-gray-800">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-800">{{ number_format($paginator->total()) }}</span>
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
