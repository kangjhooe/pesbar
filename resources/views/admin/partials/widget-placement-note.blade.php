@props([
    'title' => 'Tampil di mana?',
    'items' => [],
])

@if(count($items))
<div class="rounded-lg border border-news-line bg-news-paper px-4 py-3 text-sm text-news-muted">
    <p class="font-medium text-news-ink mb-1.5">
        <i class="fas fa-map-marker-alt text-news-accent mr-1.5"></i>{{ $title }}
    </p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($items as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</div>
@endif
