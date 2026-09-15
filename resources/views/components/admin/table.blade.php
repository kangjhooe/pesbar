@props([
    'paginator' => null,
    'bulk' => false,
    'bulkId' => 'admin-bulk-form',
    'emptyIcon' => 'fas fa-inbox',
    'emptyTitle' => 'Belum ada data',
    'emptyText' => 'Data akan muncul di sini',
    'colspan' => 6,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden']) }}>
    @if($bulk)
        <div id="{{ $bulkId }}-bar" class="hidden px-4 py-3 bg-slate-50 border-b border-gray-200">
            {{ $bulkBar ?? '' }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-slate-50 to-gray-50">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
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
