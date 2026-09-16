<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>{{ $article->title }} - Export</title>
 <style>
 body {
 font-family: 'Times New Roman', serif;
 max-width: 800px;
 margin: 40px auto;
 padding: 20px;
 line-height: 1.8;
 color: #333;
 }
 h1 {
 color: #0a0a0a;
 border-bottom: 3px solid #b91c1c;
 padding-bottom: 10px;
 margin-bottom: 20px;
 }
 .meta {
 color: #5c5c5c;
 font-size: 14px;
 margin-bottom: 30px;
 padding: 15px;
 background: #e5e5e5;
 border-left: 4px solid #b91c1c;
 }
 .meta-item {
 margin: 5px 0;
 }
 .content {
 text-align: justify;
 margin-top: 30px;
 }
 .content img {
 max-width: 100%;
 height: auto;
 margin: 20px 0;
 }
 .footer {
 margin-top: 50px;
 padding-top: 20px;
 border-top: 2px solid #e5e5e5;
 text-align: center;
 color: #5c5c5c;
 font-size: 12px;
 }
 @media print {
 body {
 margin: 0;
 padding: 20px;
 }
 }
 </style>
</head>
<body>
 <h1>{{ $article->title }}</h1>
 
 <div class="meta">
 <div class="meta-item"><strong>Kategori:</strong> {{ $article->category->name ?? 'Tidak ada kategori' }}</div>
 <div class="meta-item"><strong>Penulis:</strong> {{ $article->author->name ?? 'Tidak diketahui' }}</div>
 <div class="meta-item"><strong>Tanggal:</strong> {{ $article->created_at->format('d F Y, H:i') }}</div>
 @if($article->published_at)
 <div class="meta-item"><strong>Diterbitkan:</strong> {{ $article->published_at->format('d F Y, H:i') }}</div>
 @endif
 @if($article->tags->count() > 0)
 <div class="meta-item"><strong>Tag:</strong> {{ $article->tags->pluck('name')->join(', ') }}</div>
 @endif
 </div>

 <div class="content">
 {!! $article->content !!}
 </div>

 <div class="footer">
 <p>Dicetak dari {{ \App\Helpers\SettingsHelper::siteName() }}</p>
 <p>{{ $article->publicUrl() }}</p>
 <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
 </div>
</body>
</html>

