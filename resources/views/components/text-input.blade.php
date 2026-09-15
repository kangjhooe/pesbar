@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-news-line focus:border-news-accent focus:ring-news-accent rounded-sm shadow-none transition duration-150 ease-in-out']) }} style="pointer-events: auto; position: relative; z-index: 1;">
