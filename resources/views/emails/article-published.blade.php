@component('mail::message')
# Selamat! Artikel Anda Telah Dipublikasikan

Halo **{{ $notifiable->name }}**!

Kami senang memberitahu Anda bahwa artikel Anda telah berhasil dipublikasikan dan sekarang dapat dilihat oleh pembaca.

@component('mail::panel')
**Judul Artikel:** {{ $article->title }}

**Kategori:** {{ $article->category->name ?? 'Tidak ada kategori' }}

**Tanggal Publikasi:** {{ $article->published_at ? $article->published_at->format('d F Y, H:i') : 'Sekarang' }}
@endcomponent

@component('mail::button', ['url' => $articleUrl])
Lihat Artikel
@endcomponent

Terima kasih telah berkontribusi untuk **{{ $siteName }}**!

Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi tim redaksi kami.

Terima kasih,<br>
{{ $siteName }}
@endcomponent
