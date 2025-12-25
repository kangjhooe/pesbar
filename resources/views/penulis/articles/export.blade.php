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
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .meta {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 30px;
            padding: 15px;
            background: #ecf0f1;
            border-left: 4px solid #3498db;
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
            border-top: 2px solid #ecf0f1;
            text-align: center;
            color: #95a5a6;
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
        <p>Dicetak dari Portal Berita Kabupaten Pesisir Barat</p>
        <p>{{ url('/articles/' . $article->slug) }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>
</html>

