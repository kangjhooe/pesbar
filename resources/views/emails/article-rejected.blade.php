@component('mail::message')
# Artikel Ditolak

Halo **{{ $notifiable->name }}**!

Maaf, artikel Anda telah ditolak oleh tim redaksi. Silakan perbaiki artikel Anda sesuai dengan feedback yang diberikan.

@component('mail::panel')
**Judul Artikel:** {{ $article->title }}

**Tanggal Ditolak:** {{ now()->format('d F Y, H:i') }}
@endcomponent

@if($article->rejection_reason)
@component('mail::panel', ['color' => 'warning'])
**Alasan Penolakan:**

{{ $article->rejection_reason }}
@endcomponent
@endif

@component('mail::button', ['url' => route('penulis.articles.edit', $article), 'color' => 'error'])
Edit Artikel
@endcomponent

Silakan perbaiki artikel Anda sesuai dengan feedback yang diberikan. Setelah diperbaiki, artikel akan kembali masuk ke proses review.

Jika Anda memiliki pertanyaan tentang penolakan ini, silakan hubungi tim redaksi.

Terima kasih,<br>
{{ $siteName }}
@endcomponent
