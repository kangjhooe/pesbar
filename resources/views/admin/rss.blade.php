@extends('layouts.admin-simple')

@section('title', 'RSS Feed - Admin Panel')

@section('page-title', 'RSS Feed')
@section('page-subtitle', 'Informasi dan pengaturan RSS Feed')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white border border-news-line p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-rss text-orange-600"></i>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-news-ink">RSS Feed</h2>
                <p class="text-sm text-news-muted">Pesisir Barat Hub</p>
            </div>
        </div>
        <p class="text-news-ink leading-relaxed">
            RSS (Really Simple Syndication) Feed memungkinkan pengguna untuk berlangganan konten terbaru dari Pesisir Barat Hub. 
            Dengan RSS Feed, pembaca dapat menerima update otomatis setiap kali ada artikel baru yang diterbitkan.
        </p>
    </div>

    <!-- RSS Feed URLs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Main RSS Feed -->
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                    <i class="fas fa-newspaper text-news-accent"></i>
                </div>
                <h3 class="text-lg font-semibold text-news-ink">RSS Feed Utama</h3>
            </div>
            <div class="space-y-3">
                <div class="bg-news-paper rounded-lg p-4">
                    <p class="text-sm text-news-muted mb-2">URL RSS Feed:</p>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="{{ url('/rss') }}" readonly class="flex-1 px-3 py-2 bg-white border border-news-line rounded-lg text-sm font-mono">
                        <button onclick="copyToClipboard('{{ url('/rss') }}')" class="px-3 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-news-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Berisi semua artikel terbaru yang diterbitkan
                </p>
            </div>
        </div>

        <!-- Category RSS Feeds -->
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-news-ink">RSS Feed Kategori</h3>
            </div>
            <div class="space-y-3">
                <div class="bg-news-paper rounded-lg p-4">
                    <p class="text-sm text-news-muted mb-2">URL RSS Feed Kategori:</p>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="{{ url('/rss/category/{category}') }}" readonly class="flex-1 px-3 py-2 bg-white border border-news-line rounded-lg text-sm font-mono">
                        <button onclick="copyToClipboard('{{ url('/rss/category/{category}') }}')" class="px-3 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-news-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ganti {category} dengan nama kategori yang diinginkan
                </p>
            </div>
        </div>
    </div>

    <!-- RSS Feed Information -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Informasi RSS Feed</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-news-paper rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-check text-news-accent text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-news-ink">Format Standar</h4>
                        <p class="text-sm text-news-muted">Menggunakan format RSS 2.0 yang kompatibel dengan semua pembaca RSS</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-news-paper rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-check text-news-accent text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-news-ink">Update Otomatis</h4>
                        <p class="text-sm text-news-muted">Feed diperbarui setiap kali ada artikel baru yang diterbitkan</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-news-paper rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-check text-news-accent text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-news-ink">Konten Lengkap</h4>
                        <p class="text-sm text-news-muted">Menyertakan judul, ringkasan, dan link ke artikel lengkap</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-news-paper rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-check text-news-accent text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-news-ink">Metadata Lengkap</h4>
                        <p class="text-sm text-news-muted">Menyertakan tanggal publikasi, penulis, dan kategori</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-news-paper rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-check text-news-accent text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-news-ink">Optimasi SEO</h4>
                        <p class="text-sm text-news-muted">Struktur yang SEO-friendly untuk mesin pencari</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-news-paper rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-check text-news-accent text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-news-ink">Validasi XML</h4>
                        <p class="text-sm text-news-muted">Format XML yang valid dan terstruktur dengan baik</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How to Use RSS -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Cara Menggunakan RSS Feed</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-news-accent text-white rounded-full flex items-center justify-center flex-shrink-0 font-semibold text-sm">1</div>
                <div>
                    <h4 class="font-medium text-news-ink mb-2">Salin URL RSS Feed</h4>
                    <p class="text-sm text-news-muted">Klik tombol copy di atas untuk menyalin URL RSS Feed ke clipboard</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-news-accent text-white rounded-full flex items-center justify-center flex-shrink-0 font-semibold text-sm">2</div>
                <div>
                    <h4 class="font-medium text-news-ink mb-2">Buka Pembaca RSS</h4>
                    <p class="text-sm text-news-muted">Gunakan aplikasi pembaca RSS seperti Feedly, Inoreader, atau browser modern</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-news-accent text-white rounded-full flex items-center justify-center flex-shrink-0 font-semibold text-sm">3</div>
                <div>
                    <h4 class="font-medium text-news-ink mb-2">Tambahkan Feed</h4>
                    <p class="text-sm text-news-muted">Paste URL RSS Feed ke aplikasi pembaca RSS untuk berlangganan</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-news-accent text-white rounded-full flex items-center justify-center flex-shrink-0 font-semibold text-sm">4</div>
                <div>
                    <h4 class="font-medium text-news-ink mb-2">Nikmati Update</h4>
                    <p class="text-sm text-news-muted">Terima notifikasi otomatis setiap kali ada artikel baru yang diterbitkan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular RSS Readers -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Pembaca RSS Populer</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="https://feedly.com" target="_blank" class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-rss mr-2"></i>
                <span>Feedly</span>
            </a>
            <a href="https://www.inoreader.com" target="_blank" class="flex items-center justify-center px-4 py-3 bg-news-paper text-news-accent rounded-lg hover:bg-news-paper transition-colors">
                <i class="fas fa-rss mr-2"></i>
                <span>Inoreader</span>
            </a>
            <a href="https://newsblur.com" target="_blank" class="flex items-center justify-center px-4 py-3 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                <i class="fas fa-rss mr-2"></i>
                <span>NewsBlur</span>
            </a>
            <a href="https://theoldreader.com" target="_blank" class="flex items-center justify-center px-4 py-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                <i class="fas fa-rss mr-2"></i>
                <span>The Old Reader</span>
            </a>
        </div>
    </div>

    <!-- RSS Feed Status -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Status RSS Feed</h3>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm text-news-ink">Aktif</span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-clock text-news-muted"></i>
                <span class="text-sm text-news-muted">Terakhir diperbarui: {{ now()->format('d-m-Y H:i') }} WIB</span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-newspaper text-news-muted"></i>
                <span class="text-sm text-news-muted">Total artikel: {{ \App\Models\Article::where('status', 'published')->count() }}</span>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        button.classList.remove('bg-news-accent', 'hover:bg-news-ink');
        
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-news-accent', 'hover:bg-news-ink');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Gagal menyalin URL. Silakan salin manual: ' + text);
    });
}
</script>
@endsection
