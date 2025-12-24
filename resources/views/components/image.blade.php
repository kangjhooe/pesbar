@props([
    'src',
    'alt' => '',
    'class' => '',
    'lazy' => true,
    'fallback' => 'images/default-news.jpg'
])

@php
    $imageSrc = $src ? asset('storage/' . $src) : asset($fallback);
    $loading = $lazy ? 'lazy' : 'eager';
@endphp

<img 
    src="{{ $imageSrc }}" 
    alt="{{ $alt }}"
    class="{{ $class }}"
    loading="{{ $loading }}"
    decoding="async"
    {{ $attributes }}
>

