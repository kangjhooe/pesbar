@extends('layouts.admin-simple')

@section('title', 'Kontak - Admin Panel')

@section('page-title', 'Kontak')
@section('page-subtitle', 'Informasi kontak dan alamat Pesisir Barat Hub')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white border border-news-line p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-news-paper rounded-lg flex items-center justify-center">
                <i class="fas fa-phone text-news-accent"></i>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-news-ink">Informasi Kontak</h2>
                <p class="text-sm text-news-muted">Pesisir Barat Hub</p>
            </div>
        </div>
        <p class="text-news-ink leading-relaxed">
            Pesisir Barat Hub menyediakan informasi terkini seputar kegiatan, program, dan perkembangan di Kabupaten Pesisir Barat. 
            Kami berkomitmen untuk memberikan informasi yang akurat, terpercaya, dan bermanfaat bagi masyarakat.
        </p>
    </div>

    <!-- Contact Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Alamat Kantor -->
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-news-ink">Alamat Kantor</h3>
            </div>
            <div class="space-y-2 text-news-ink">
                <p><strong>Pemerintah Kabupaten Pesisir Barat</strong></p>
                <p>Jl. Raya Krui - Liwa KM 5</p>
                <p>Kecamatan Pesisir Tengah</p>
                <p>Kabupaten Pesisir Barat</p>
                <p>Lampung 34875</p>
            </div>
        </div>

        <!-- Kontak Telepon -->
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                    <i class="fas fa-phone text-news-accent"></i>
                </div>
                <h3 class="text-lg font-semibold text-news-ink">Kontak Telepon</h3>
            </div>
            <div class="space-y-2 text-news-ink">
                <p><strong>Telepon:</strong> (0728) 123456</p>
                <p><strong>Fax:</strong> (0728) 123457</p>
                <p><strong>WhatsApp:</strong> +62 812-3456-7890</p>
                <p class="text-sm text-news-muted mt-3">
                    <i class="fas fa-clock mr-1"></i>
                    Jam Operasional: Senin - Jumat, 08:00 - 16:00 WIB
                </p>
            </div>
        </div>

        <!-- Email & Website -->
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-news-ink"></i>
                </div>
                <h3 class="text-lg font-semibold text-news-ink">Email & Website</h3>
            </div>
            <div class="space-y-2 text-news-ink">
                <p><strong>Email Umum:</strong></p>
                <p class="text-news-accent">info@pesisirbaratkab.go.id</p>
                <p><strong>Email Redaksi:</strong></p>
                <p class="text-news-accent">redaksi@pesisirbaratkab.go.id</p>
                <p><strong>Website Resmi:</strong></p>
                <p class="text-news-accent">www.pesisirbaratkab.go.id</p>
            </div>
        </div>

        <!-- Media Sosial -->
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-share-alt text-orange-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-news-ink">Media Sosial</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <i class="fab fa-facebook text-news-accent w-5"></i>
                    <span class="text-news-ink">Facebook: @PesisirBaratKab</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fab fa-instagram text-pink-600 w-5"></i>
                    <span class="text-news-ink">Instagram: @pesisirbaratkab</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fab fa-twitter text-blue-400 w-5"></i>
                    <span class="text-news-ink">Twitter: @PesisirBaratKab</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fab fa-youtube text-red-600 w-5"></i>
                    <span class="text-news-ink">YouTube: Pesisir Barat TV</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="mailto:info@pesisirbaratkab.go.id" class="flex items-center justify-center px-4 py-3 bg-news-paper text-news-accent rounded-lg hover:bg-news-paper transition-colors">
                <i class="fas fa-envelope mr-2"></i>
                <span>Kirim Email</span>
            </a>
            <a href="tel:+62728123456" class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-phone mr-2"></i>
                <span>Hubungi Kami</span>
            </a>
            <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fab fa-whatsapp mr-2"></i>
                <span>WhatsApp</span>
            </a>
            <a href="{{ route('home') }}" class="flex items-center justify-center px-4 py-3 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                <i class="fas fa-external-link-alt mr-2"></i>
                <span>Lihat Website</span>
            </a>
        </div>
    </div>

    <!-- Map Section -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Lokasi Kantor</h3>
        <div class="bg-news-paper rounded-lg p-8 text-center">
            <i class="fas fa-map-marked-alt text-4xl text-news-muted mb-4"></i>
            <p class="text-news-muted mb-4">Peta interaktif akan ditampilkan di sini</p>
            <p class="text-sm text-news-muted">
                Koordinat: -5.123456, 103.123456<br>
                <a href="https://maps.google.com/?q=-5.123456,103.123456" target="_blank" class="text-news-accent hover:text-news-ink">
                    Buka di Google Maps
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
