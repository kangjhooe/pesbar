@component('mail::message')
# Artikel Baru Menunggu Review

Halo **{{ $notifiable->name }}**!

Ada artikel baru yang menunggu review Anda. Silakan review artikel ini segera.

@component('mail::panel')
**Judul Artikel:** {{ $article->title }}

**Penulis:** {{ $article->author->name ?? 'Unknown' }}

**Kategori:** {{ $article->category->name ?? 'Tidak ada kategori' }}

**Tanggal Dibuat:** {{ $article->created_at->format('d F Y, H:i') }}
@endcomponent

@if($article->excerpt)
@component('mail::panel')
{{ Str::limit(strip_tags($article->excerpt), 200) }}
@endcomponent
@endif

@component('mail::button', ['url' => route('admin.articles.detail', $article)])
Review Artikel
@endcomponent

Pastikan untuk melakukan review dengan teliti sebelum mempublikasikan artikel.

Terima kasih,<br>
{{ $siteName }}
@endcomponent
