@props([
    'column' => null,
    'label' => '',
    'sortable' => true,
    'align' => 'left',
])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
    $base = "px-4 py-3.5 {$alignClass} text-xs font-semibold text-news-muted uppercase tracking-wider whitespace-nowrap";
@endphp

@if($sortable && $column)
    @php
        $isActive = \App\Helpers\AdminTableHelper::isSortedBy($column);
        $direction = \App\Helpers\AdminTableHelper::sortDirection($column);
        $url = \App\Helpers\AdminTableHelper::sortUrl($column);
    @endphp
    <th {{ $attributes->merge(['class' => $base]) }}>
        <a href="{{ $url }}"
           class="inline-flex items-center gap-1.5 group hover:text-news-accent transition-colors {{ $isActive ? 'text-news-accent' : 'text-news-muted' }}">
            <span>{{ $label ?: $slot }}</span>
            <span class="inline-flex flex-col leading-none text-[10px] opacity-60 group-hover:opacity-100">
                @if($isActive && $direction === 'asc')
                    <i class="fas fa-sort-up text-news-accent"></i>
                @elseif($isActive && $direction === 'desc')
                    <i class="fas fa-sort-down text-news-accent"></i>
                @else
                    <i class="fas fa-sort text-news-muted"></i>
                @endif
            </span>
        </a>
    </th>
@else
    <th {{ $attributes->merge(['class' => $base]) }}>
        {{ $label ?: $slot }}
    </th>
@endif
