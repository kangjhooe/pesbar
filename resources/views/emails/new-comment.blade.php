@component('mail::message')
# Komentar Baru pada Artikel Anda

Halo **{{ $notifiable->name }}**!

Ada komentar baru pada artikel Anda yang telah disetujui oleh admin.

@component('mail::panel')
**Judul Artikel:** {{ $article->title }}

**Oleh:** {{ $comment->name ?? 'Anonymous' }}

**Tanggal Komentar:** {{ $comment->created_at->format('d F Y, H:i') }}
@endcomponent

@component('mail::panel')
"{{ Str::limit(strip_tags($comment->comment ?? $comment->content ?? ''), 200) }}"
@endcomponent

@component('mail::button', ['url' => route('articles.show', $article) . '#comments'])
Lihat Komentar
@endcomponent

Silakan review komentar ini dan balas jika diperlukan.

Komentar telah melalui proses moderasi dan disetujui oleh admin.

Terima kasih,<br>
{{ $siteName }}
@endcomponent
