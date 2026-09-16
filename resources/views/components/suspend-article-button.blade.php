@props([
    'article',
])

@can('suspend', $article)
    @if(in_array($article->status, ['published', 'archived'], true))
        <div class="inline-flex" x-data="{ open: false }">
            <button
                type="button"
                @click="open = true"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-orange-300 bg-orange-50 text-orange-900 hover:border-orange-500 transition-colors"
            >
                <i class="fas fa-pause-circle" aria-hidden="true"></i>
                <span>Tangguhkan</span>
            </button>

            <div
                x-show="open"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="suspend-article-title-{{ $article->id }}"
            >
                <div class="absolute inset-0 bg-news-ink/40" @click="open = false"></div>
                <div class="relative w-full max-w-md bg-white border border-news-line p-5 shadow-lg">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <h3 id="suspend-article-title-{{ $article->id }}" class="font-display text-lg font-bold text-news-ink">
                            Tangguhkan Penayangan
                        </h3>
                        <button type="button" class="text-news-muted hover:text-news-ink" @click="open = false" aria-label="Tutup">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <p class="text-sm text-news-muted mb-4">
                        Artikel tidak lagi tampil ke publik. Konten penulis tidak diubah.
                    </p>

                    <form method="POST" action="{{ route('admin.articles.suspend', $article) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label for="suspend-reason-{{ $article->id }}" class="block text-sm font-medium text-news-ink mb-1">Alasan</label>
                            <textarea
                                id="suspend-reason-{{ $article->id }}"
                                name="reason"
                                rows="3"
                                required
                                maxlength="1000"
                                class="w-full border border-news-line px-3 py-2 text-sm"
                                placeholder="Jelaskan alasan penangguhan…"
                            ></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="open = false" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary">Tangguhkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($article->status === 'suspended')
        <form method="POST" action="{{ route('admin.articles.unsuspend', $article) }}" class="inline"
              onsubmit="return confirm('Pulihkan penayangan artikel ini?')">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-emerald-300 bg-emerald-50 text-emerald-900 hover:border-emerald-500 transition-colors"
            >
                <i class="fas fa-play-circle" aria-hidden="true"></i>
                <span>Pulihkan</span>
            </button>
        </form>
    @endif
@endcan
