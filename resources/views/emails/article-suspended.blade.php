@component('mail::message')
# Penayangan Artikel Ditangguhkan

Halo **{{ $notifiable->name }}**!

Penayangan artikel Anda sementara ditangguhkan oleh redaksi. Artikel tidak tampil di situs publik sampai ditinjau ulang.

@component('mail::panel')
**Judul Artikel:** {{ $article->title }}

**Tanggal:** {{ now()->format('d F Y, H:i') }}
@endcomponent

@if($article->suspension_reason)
@component('mail::panel')
**Alasan:**

{{ $article->suspension_reason }}
@endcomponent
@endif

@component('mail::button', ['url' => route('penulis.articles.edit', $article), 'color' => 'error'])
Lihat Artikel
@endcomponent

Jika Anda memiliki pertanyaan, silakan hubungi tim redaksi.

Terima kasih,<br>
{{ $siteName }}
@endcomponent
