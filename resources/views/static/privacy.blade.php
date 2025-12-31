@extends('layouts.public')

@section('title', 'Kebijakan Privasi - ' . \App\Helpers\SettingsHelper::siteName())
@section('description', 'Kebijakan Privasi Pesisir Barat Hub')

@section('content')
<div class="container-responsive py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg p-8 text-white">
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <div>
                    <h1 class="heading-responsive font-bold">Kebijakan Privasi</h1>
                    <p class="text-primary-100 text-responsive">Bagaimana kami melindungi dan menggunakan informasi Anda</p>
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
                <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Pendahuluan</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pesisir Barat Hub menghormati privasi Anda dan berkomitmen untuk melindungi informasi pribadi yang Anda berikan kepada kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi Anda saat menggunakan layanan kami.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Informasi yang Kami Kumpulkan</h2>
                <div class="text-gray-700 leading-relaxed">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">2.1 Informasi yang Anda Berikan</h3>
                    <p class="mb-4">Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami, termasuk:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li><strong>Informasi Akun:</strong> Nama, username, alamat email, dan kata sandi saat Anda mendaftar</li>
                        <li><strong>Informasi Profil:</strong> Foto profil, bio, dan informasi lain yang Anda pilih untuk dibagikan</li>
                        <li><strong>Konten:</strong> Artikel, komentar, dan konten lain yang Anda kirimkan</li>
                        <li><strong>Komunikasi:</strong> Pesan yang Anda kirimkan kepada kami melalui formulir kontak atau email</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-800 mb-3 mt-6">2.2 Informasi yang Dikumpulkan Secara Otomatis</h3>
                    <p class="mb-4">Saat Anda menggunakan layanan kami, kami secara otomatis mengumpulkan informasi tertentu, termasuk:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li><strong>Data Log:</strong> Alamat IP, jenis browser, halaman yang dikunjungi, waktu kunjungan</li>
                        <li><strong>Cookies dan Teknologi Serupa:</strong> Informasi yang dikumpulkan melalui cookies dan teknologi pelacakan lainnya</li>
                        <li><strong>Data Perangkat:</strong> Jenis perangkat, sistem operasi, dan pengidentifikasi perangkat</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">3. Bagaimana Kami Menggunakan Informasi</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Kami menggunakan informasi yang dikumpulkan untuk:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>Menyediakan, memelihara, dan meningkatkan layanan kami</li>
                        <li>Memproses pendaftaran dan mengelola akun Anda</li>
                        <li>Mengirimkan notifikasi tentang aktivitas akun dan layanan</li>
                        <li>Menanggapi pertanyaan dan permintaan Anda</li>
                        <li>Menganalisis penggunaan layanan untuk meningkatkan pengalaman pengguna</li>
                        <li>Mendeteksi, mencegah, dan mengatasi masalah teknis atau keamanan</li>
                        <li>Mematuhi kewajiban hukum dan melindungi hak kami</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">4. Bagaimana Kami Membagikan Informasi</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Kami tidak menjual informasi pribadi Anda. Kami dapat membagikan informasi Anda dalam situasi berikut:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li><strong>Dengan Persetujuan Anda:</strong> Ketika Anda secara eksplisit memberikan izin untuk membagikan informasi</li>
                        <li><strong>Penyedia Layanan:</strong> Dengan pihak ketiga yang membantu kami mengoperasikan layanan (dengan kewajiban kerahasiaan)</li>
                        <li><strong>Kewajiban Hukum:</strong> Ketika diwajibkan oleh hukum atau untuk melindungi hak, properti, atau keamanan kami dan pengguna lain</li>
                        <li><strong>Transfer Bisnis:</strong> Dalam kasus merger, akuisisi, atau penjualan aset (dengan pemberitahuan sebelumnya)</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Cookies dan Teknologi Pelacakan</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Kami menggunakan cookies dan teknologi pelacakan serupa untuk:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>Mengingat preferensi Anda dan menyediakan pengalaman yang dipersonalisasi</li>
                        <li>Menganalisis bagaimana layanan digunakan</li>
                        <li>Meningkatkan keamanan dan mencegah penipuan</li>
                    </ul>
                    <p class="mb-4">Anda dapat mengontrol cookies melalui pengaturan browser Anda, namun ini dapat mempengaruhi fungsionalitas layanan.</p>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Keamanan Data</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang wajar untuk melindungi informasi pribadi Anda dari akses, penggunaan, atau pengungkapan yang tidak sah. Namun, tidak ada metode transmisi melalui internet atau penyimpanan elektronik yang 100% aman, dan kami tidak dapat menjamin keamanan absolut.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Retensi Data</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Kami menyimpan informasi pribadi Anda selama diperlukan untuk memenuhi tujuan yang dijelaskan dalam kebijakan ini, kecuali periode retensi yang lebih lama diperlukan atau diizinkan oleh hukum. Ketika informasi tidak lagi diperlukan, kami akan menghapus atau menganonimkan data tersebut.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Hak Anda</h2>
                <div class="text-gray-700 leading-relaxed">
                    <p class="mb-4">Anda memiliki hak-hak berikut terkait informasi pribadi Anda:</p>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li><strong>Akses:</strong> Meminta akses ke informasi pribadi yang kami miliki tentang Anda</li>
                        <li><strong>Perbaikan:</strong> Meminta koreksi informasi yang tidak akurat atau tidak lengkap</li>
                        <li><strong>Penghapusan:</strong> Meminta penghapusan informasi pribadi Anda dalam keadaan tertentu</li>
                        <li><strong>Pembatasan:</strong> Meminta pembatasan pemrosesan informasi pribadi Anda</li>
                        <li><strong>Portabilitas:</strong> Meminta transfer informasi pribadi Anda kepada penyedia layanan lain</li>
                        <li><strong>Keberatan:</strong> Menolak pemrosesan informasi pribadi Anda untuk tujuan tertentu</li>
                    </ul>
                    <p class="mb-4">Untuk menggunakan hak-hak ini, silakan hubungi kami melalui informasi kontak yang tersedia.</p>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Privasi Anak-Anak</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Layanan kami tidak ditujukan untuk anak-anak di bawah 13 tahun. Kami tidak secara sengaja mengumpulkan informasi pribadi dari anak-anak di bawah 13 tahun. Jika kami mengetahui bahwa kami telah mengumpulkan informasi dari anak di bawah 13 tahun tanpa persetujuan orang tua, kami akan mengambil langkah untuk menghapus informasi tersebut.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Perubahan Kebijakan Privasi</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Kami akan memberitahu Anda tentang perubahan material dengan memposting kebijakan baru di halaman ini dan memperbarui tanggal "Terakhir diperbarui". Anda disarankan untuk meninjau kebijakan ini secara berkala.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">11. Kontak</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Jika Anda memiliki pertanyaan, kekhawatiran, atau permintaan terkait Kebijakan Privasi ini atau praktik privasi kami, silakan hubungi kami melalui halaman kontak atau email yang tersedia di website kami.
                </p>
            </section>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Dengan menggunakan layanan kami, Anda mengakui bahwa Anda telah membaca dan memahami Kebijakan Privasi ini.
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

