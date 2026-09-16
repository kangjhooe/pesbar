@props([
    'name' => 'ids[]',
    'value' => null,
    'all' => false,
    'bulkId' => 'admin-bulk-form',
])

@php
    $tag = $all ? 'th' : 'td';
    $baseClass = $all
        ? 'px-4 py-3.5 whitespace-nowrap w-12'
        : 'px-4 py-4 whitespace-nowrap w-12';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $baseClass]) }}>
    <input
        type="checkbox"
        @if($all)
            id="{{ $bulkId }}-select-all"
            class="admin-select-all w-4 h-4 text-news-accent bg-white border-news-line rounded focus:ring-news-accent cursor-pointer"
            data-bulk-id="{{ $bulkId }}"
        @else
            name="{{ $name }}"
            value="{{ $value }}"
            class="admin-row-checkbox w-4 h-4 text-news-accent bg-white border-news-line rounded focus:ring-news-accent cursor-pointer"
            data-bulk-id="{{ $bulkId }}"
        @endif
    >
</{{ $tag }}>
