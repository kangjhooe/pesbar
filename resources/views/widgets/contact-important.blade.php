@php
    $contacts = \App\Models\ContactImportant::active()->ordered()->get();
@endphp

@if($contacts->count() > 0)
<div class="border border-news-line">
    <div class="bg-news-ink text-white px-4 py-2.5">
        <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Kontak Penting</h2>
    </div>

    <ul class="divide-y divide-news-line max-h-80 overflow-y-auto">
        @foreach($contacts as $contact)
        <li class="px-4 py-3">
            <div class="flex items-start justify-between gap-2 mb-1">
                <strong class="text-sm text-news-ink leading-snug">{{ $contact->name }}</strong>
                <span class="text-[10px] font-bold uppercase tracking-wider text-news-accent shrink-0">
                    {{ ucwords(str_replace('_', ' ', $contact->type)) }}
                </span>
            </div>

            @if($contact->phone)
                <a href="tel:{{ $contact->phone }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-news-ink hover:text-news-accent transition-colors">
                    <i class="fas fa-phone text-[10px] text-news-accent" aria-hidden="true"></i>
                    {{ $contact->formatted_phone }}
                </a>
            @endif

            @if($contact->address)
                <p class="mt-1 text-[11px] text-news-muted flex items-start gap-1.5">
                    <i class="fas fa-map-marker-alt text-[10px] mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ Str::limit($contact->address, 55) }}</span>
                </p>
            @endif

            @if($contact->description)
                <p class="mt-1 text-[11px] text-news-muted">{{ Str::limit($contact->description, 80) }}</p>
            @endif
        </li>
        @endforeach
    </ul>

    <div class="border-t border-news-line px-4 py-2.5">
        <p class="text-[10px] text-news-muted">
            Hubungi nomor di atas untuk keperluan darurat.
        </p>
    </div>
</div>
@endif
