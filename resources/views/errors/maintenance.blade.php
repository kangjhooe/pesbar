<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perawatan — {{ $siteName ?? config('app.name') }}</title>
    <style>
        body { margin: 0; font-family: Georgia, 'Times New Roman', serif; background: #f5f2eb; color: #1a1a1a; }
        .wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .box { max-width: 28rem; text-align: center; }
        h1 { font-size: 1.75rem; margin: 0 0 0.75rem; }
        p { color: #555; line-height: 1.5; margin: 0 0 1.5rem; }
        a { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="box">
            <h1>{{ $siteName ?? 'Situs' }} sedang perawatan</h1>
            <p>Kami sedang melakukan pemeliharaan singkat. Silakan kembali beberapa saat lagi.</p>
            <p><a href="{{ route('login') }}">Masuk (staf)</a></p>
        </div>
    </div>
</body>
</html>
