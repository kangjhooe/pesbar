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
        'accent' => 'text-news-accent hover:bg-red-50 hover:text-red-800',
        'blue' => 'text-news-accent hover:bg-red-50 hover:text-red-800',
        'indigo' => 'text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700',
        'yellow' => 'text-amber-600 hover:bg-amber-50 hover:text-amber-700',
        'amber' => 'text-amber-600 hover:bg-amber-50 hover:text-amber-700',
        'green' => 'text-green-600 hover:bg-green-50 hover:text-green-700',
        'red' => 'text-red-600 hover:bg-red-50 hover:text-red-700',
        'orange' => 'text-orange-600 hover:bg-orange-50 hover:text-orange-700',
        'gray' => 'text-gray-600 hover:bg-gray-50 hover:text-gray-700',
        'purple' => 'text-purple-600 hover:bg-purple-50 hover:text-purple-700',
    ];
    $colorClass = $colors[$color] ?? $colors['accent'];
    $btnClass = "inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-150 {$colorClass}";
    $iconClass = str_starts_with($icon, 'fa') ? $icon : "fas {$icon}";
@endphp

@if($href && !$method)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
        <i class="{{ $iconClass }} text-sm"></i>
    </a>
@elseif($href && $method)
    <form action="{{ $href }}" method="POST" class="inline"
          @if($confirm) onsubmit="return confirm(@js($confirm))" @endif>
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif
        <button type="submit" {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
            <i class="{{ $iconClass }} text-sm"></i>
        </button>
    </form>
@else
    <button type="{{ $type === 'submit' ? 'submit' : 'button' }}" {{ $attributes->merge(['class' => $btnClass, 'title' => $title]) }}>
        <i class="{{ $iconClass }} text-sm"></i>
    </button>
@endif
