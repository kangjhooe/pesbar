@props([
    'href' => null,
    'icon' => 'fa-eye',
    'color' => 'accent',
    'title' => '',
    'method' => null,
    'confirm' => null,
    'type' => 'link', // link | button | form
])

@php
    $colors = [
        'accent' => 'text-news-accent hover:bg-news-paper hover:text-news-ink',
        'blue' => 'text-news-accent hover:bg-news-paper hover:text-news-ink',
        'indigo' => 'text-news-muted hover:bg-news-paper hover:text-news-accent',
        'yellow' => 'text-news-muted hover:bg-news-paper hover:text-news-ink',
        'amber' => 'text-news-muted hover:bg-news-paper hover:text-news-ink',
        'green' => 'text-news-ink hover:bg-news-paper hover:text-news-accent',
        'red' => 'text-news-accent hover:bg-red-50 hover:text-red-800',
        'orange' => 'text-news-muted hover:bg-news-paper hover:text-news-accent',
        'gray' => 'text-news-muted hover:bg-news-paper hover:text-news-ink',
        'purple' => 'text-news-muted hover:bg-news-paper hover:text-news-accent',
    ];
    $colorClass = $colors[$color] ?? $colors['accent'];
    $btnClass = "inline-flex items-center justify-center w-8 h-8 transition-colors duration-150 {$colorClass}";
    $iconClass = str_starts_with($icon, 'fa') ? $icon : "fas {$icon}";
@endphp

@if($href && !$method)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
        <i class="{{ $iconClass }} text-sm"></i>
    </a>
@elseif($href && $method)
    <form action="{{ $href }}" method="POST" class="inline">
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif
        @if($confirm)
            <button type="button"
                    onclick="window.pesbarConfirmSubmit(this.form, @js($confirm))"
                    {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
                <i class="{{ $iconClass }} text-sm"></i>
            </button>
        @else
            <button type="submit" {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
                <i class="{{ $iconClass }} text-sm"></i>
            </button>
        @endif
    </form>
@else
    <button type="{{ $type === 'submit' ? 'submit' : 'button' }}" {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
        <i class="{{ $iconClass }} text-sm"></i>
    </button>
@endif
