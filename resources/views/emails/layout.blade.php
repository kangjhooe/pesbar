<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notifikasi' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px 20px;
        }
        .email-content {
            margin-bottom: 20px;
        }
        .email-content p {
            margin: 10px 0;
            color: #555;
        }
        .email-content strong {
            color: #333;
        }
        .email-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #667eea;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .email-button:hover {
            background-color: #5568d3;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #e9ecef;
        }
        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }
        .email-info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-info-box p {
            margin: 5px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-body {
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ $siteName ?? config('app.name', 'Portal Berita') }}</h1>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>
                <strong>{{ $siteName ?? config('app.name', 'Portal Berita') }}</strong><br>
                {{ $siteDescription ?? 'Portal berita resmi Kabupaten Pesisir Barat' }}
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ config('app.url', url('/')) }}">Kunjungi Website</a> | 
                <a href="{{ config('app.url', url('/')) }}/contact">Kontak Kami</a>
            </p>
            <p style="margin-top: 10px; font-size: 11px; color: #999;">
                Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>

