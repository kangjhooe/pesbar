@props([
    'article',
])

@php
    $reasons = \App\Models\ArticleReport::REASONS;
    $isGuest = !auth()->check();
@endphp

<div class="inline-flex" x-data="{ open: false }">
    <button
        type="button"
        @click="open = true"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-news-line bg-white text-news-ink hover:border-news-ink transition-colors"
    >
        <i class="fas fa-flag" aria-hidden="true"></i>
        <span>Lapor</span>
    </button>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="report-article-title"
    >
        <div class="absolute inset-0 bg-news-ink/40" @click="open = false"></div>
        <div class="relative w-full max-w-md bg-white border border-news-line p-5 shadow-lg">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h3 id="report-article-title" class="font-display text-lg font-bold text-news-ink">Laporkan Artikel</h3>
                <button type="button" class="text-news-muted hover:text-news-ink" @click="open = false" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="text-sm text-news-muted mb-4">
                Laporkan jika artikel ini bermasalah. Redaksi akan meninjau laporan Anda.
            </p>

            <form method="POST" action="{{ route('articles.report', $article) }}" class="space-y-3">
                @csrf
                {{-- Honeypot --}}
                <div class="hidden" aria-hidden="true">
                    <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                @if($isGuest)
                    <div>
                        <label for="report-name" class="block text-sm font-medium text-news-ink mb-1">Nama</label>
                        <input id="report-name" type="text" name="name" required maxlength="100"
                               value="{{ old('name') }}"
                               class="w-full border border-news-line px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label for="report-email" class="block text-sm font-medium text-news-ink mb-1">Email</label>
                        <input id="report-email" type="email" name="email" required maxlength="100"
                               value="{{ old('email') }}"
                               class="w-full border border-news-line px-3 py-2 text-sm">
                    </div>
                @endif

                <div>
                    <label for="report-reason" class="block text-sm font-medium text-news-ink mb-1">Alasan</label>
                    <select id="report-reason" name="reason" required class="w-full border border-news-line px-3 py-2 text-sm">
                        <option value="">Pilih alasan</option>
                        @foreach($reasons as $value => $label)
                            <option value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="report-details" class="block text-sm font-medium text-news-ink mb-1">Keterangan (opsional)</label>
                    <textarea id="report-details" name="details" rows="3" maxlength="1000"
                              class="w-full border border-news-line px-3 py-2 text-sm"
                              placeholder="Jelaskan singkat permasalahannya">{{ old('details') }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="open = false" class="px-3 py-2 text-sm border border-news-line text-news-ink hover:bg-news-paper">
                        Batal
                    </button>
                    <button type="submit" class="px-3 py-2 text-sm bg-news-ink text-white hover:bg-news-accent">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
