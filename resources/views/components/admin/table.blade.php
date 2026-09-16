@props([
    'paginator' => null,
    'bulk' => false,
    'bulkId' => 'admin-bulk-form',
    'emptyIcon' => 'fas fa-inbox',
    'emptyTitle' => 'Belum ada data',
    'emptyText' => 'Data akan muncul di sini',
    'colspan' => 6,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-news-line overflow-hidden']) }}>
    @if($bulk)
        <div id="{{ $bulkId }}-bar" class="hidden px-4 py-3 bg-news-paper border-b border-news-line">
            {{ $bulkBar ?? '' }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-news-line">
            <thead class="bg-news-paper">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-news-line [&_tr]:hover:bg-news-paper">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if($paginator)
        <x-admin.pagination :paginator="$paginator" />
    @endif
</div>

@if($bulk)
    <x-admin.table-scripts :bulk-id="$bulkId" />
@endif
