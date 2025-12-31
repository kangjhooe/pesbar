@extends('layouts.public')

@section('title', 'Syarat dan Ketentuan - ' . \App\Helpers\SettingsHelper::siteName())
@section('description', 'Syarat dan Ketentuan penggunaan Portal Berita Kabupaten Pesisir Barat')

@section('content')
<div class="container-responsive py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg p-8 text-white">
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-file-contract text-2xl"></i>
                </div>
                <div>
                    <h1 class="heading-responsive font-bold">Syarat dan Ketentuan</h1>
                    <p class="text-primary-100 text-responsive">Ketentuan penggunaan Portal Berita Kabupaten Pesisir Barat</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <div class="prose prose-lg max-w-none">
            <p class="text-sm text-gray-500 mb-6">
                <strong>Terakhir diperbarui:</strong> {{ date('d F Y') }}
            </p>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Penerimaan Syarat</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Dengan mengakses dan menggunakan Portal Berita Kabupaten Pesisir Barat, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak setuju dengan bagian mana pun dari syarat ini, maka Anda tidak boleh menggunakan layanan kami.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Penggunaan Layanan</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Anda setuju untuk menggunakan layanan kami hanya untuk tujuan yang sah dan sesuai dengan ketentuan berikut:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>Anda tidak akan menggunakan layanan untuk tujuan yang melanggar hukum atau melanggar hak pihak lain</li>
                        <li>Anda tidak akan mengirimkan konten yang mengandung virus, malware, atau kode berbahaya lainnya</li>
                        <li>Anda tidak akan mencoba mendapatkan akses tidak sah ke sistem atau jaringan kami</li>
                        <li>Anda tidak akan menggunakan layanan untuk mengirim spam, phishing, atau aktivitas penipuan lainnya</li>
                        <li>Anda akan menjaga kerahasiaan informasi akun Anda dan bertanggung jawab atas semua aktivitas yang terjadi di bawah akun Anda</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">3. Akun Pengguna</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Untuk menggunakan beberapa fitur layanan, Anda harus membuat akun. Saat membuat akun, Anda setuju untuk:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>Memberikan informasi yang akurat, terkini, dan lengkap</li>
                        <li>Memelihara dan memperbarui informasi akun Anda secara berkala</li>
                        <li>Menjaga keamanan kata sandi dan informasi akun Anda</li>
                        <li>Memberitahu kami segera jika ada penggunaan tidak sah atas akun Anda</li>
                        <li>Bertanggung jawab atas semua aktivitas yang terjadi di bawah akun Anda</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">4. Konten Pengguna</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Dengan mengirimkan konten ke platform kami (termasuk artikel, komentar, gambar, dll), Anda memberikan kami hak non-eksklusif, bebas royalti, dan dapat dialihkan untuk menggunakan, mereproduksi, memodifikasi, dan menampilkan konten tersebut.</p>
                    <p class="mb-4">Anda bertanggung jawab untuk memastikan bahwa konten yang Anda kirimkan:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>Tidak melanggar hak cipta, merek dagang, atau hak kekayaan intelektual pihak lain</li>
                        <li>Tidak mengandung materi yang melanggar hukum, menyesatkan, atau merugikan</li>
                        <li>Tidak mengandung informasi pribadi orang lain tanpa izin</li>
                        <li>Mematuhi semua hukum dan peraturan yang berlaku</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Hak Kekayaan Intelektual</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Semua konten di Portal Berita Kabupaten Pesisir Barat, termasuk teks, grafik, logo, ikon, gambar, klip audio, unduhan digital, dan kompilasi data, adalah milik kami atau pemberi lisensi kami dan dilindungi oleh undang-undang hak cipta dan kekayaan intelektual lainnya.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Pembatasan Tanggung Jawab</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Kami berusaha menyediakan informasi yang akurat dan terkini, namun kami tidak menjamin:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>Keakuratan, kelengkapan, atau ketepatan waktu informasi</li>
                        <li>Layanan akan selalu tersedia atau bebas dari gangguan</li>
                        <li>Hasil yang akan diperoleh dari penggunaan layanan</li>
                    </ul>
                    <p class="mb-4">Kami tidak bertanggung jawab atas kerugian langsung, tidak langsung, insidental, atau konsekuensial yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan kami.</p>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Penghentian</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Kami berhak untuk menghentikan atau menangguhkan akses Anda ke layanan kami, tanpa pemberitahuan sebelumnya, karena alasan apa pun, termasuk jika Anda melanggar syarat dan ketentuan ini.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Perubahan Syarat</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Kami berhak untuk memodifikasi syarat dan ketentuan ini kapan saja. Perubahan akan berlaku efektif setelah dipublikasikan di halaman ini. Anda disarankan untuk meninjau halaman ini secara berkala untuk mengetahui perubahan terbaru.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Hukum yang Berlaku</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Syarat dan ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum Republik Indonesia. Setiap sengketa yang timbul dari atau terkait dengan syarat ini akan diselesaikan melalui pengadilan yang berwenang di Indonesia.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Kontak</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Jika Anda memiliki pertanyaan tentang Syarat dan Ketentuan ini, silakan hubungi kami melalui halaman kontak atau email yang tersedia di website kami.
                </p>
            </section>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Dengan menggunakan layanan kami, Anda mengakui bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat dan Ketentuan ini.
                </p>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="max-w-4xl mx-auto mt-6">
        <a href="{{ route('register') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Halaman Pendaftaran
        </a>
    </div>
</div>
@endsection

