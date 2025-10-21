<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Bupati Pesisir Barat Resmikan Pembangunan Jembatan Penghubung Antar Desa',
                'slug' => 'bupati-resmikan-jembatan-penghubung-antar-desa',
                'excerpt' => 'Pembangunan jembatan sepanjang 150 meter ini diharapkan dapat meningkatkan konektivitas antar desa dan mempermudah akses transportasi masyarakat.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Bupati telah meresmikan pembangunan jembatan penghubung antar desa yang akan menjadi solusi transportasi bagi masyarakat. Jembatan sepanjang 150 meter ini dibangun dengan anggaran sebesar 2,5 miliar rupiah dan diharapkan dapat meningkatkan konektivitas antar desa di wilayah Pesisir Barat.

Pembangunan jembatan ini merupakan bagian dari program pembangunan infrastruktur yang menjadi prioritas pemerintah daerah. Dengan adanya jembatan ini, masyarakat dapat dengan mudah mengakses berbagai layanan publik dan pusat ekonomi di wilayah tersebut.

"Pembangunan jembatan ini merupakan wujud komitmen kami untuk meningkatkan kesejahteraan masyarakat melalui pembangunan infrastruktur yang berkualitas," ujar Bupati dalam sambutannya.

Proyek ini diharapkan selesai dalam waktu 8 bulan dan akan memberikan dampak positif bagi perekonomian masyarakat sekitar.',
                'category_id' => 1, // Politik
                'author_id' => 1,
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => true,
                'is_breaking' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'UMKM Pesisir Barat Raih Penghargaan Nasional',
                'slug' => 'umkm-pesisir-barat-raih-penghargaan-nasional',
                'excerpt' => 'Tiga UMKM dari Kabupaten Pesisir Barat berhasil meraih penghargaan dalam ajang kompetisi nasional untuk kategori inovasi produk.',
                'content' => 'Tiga Usaha Mikro, Kecil, dan Menengah (UMKM) dari Kabupaten Pesisir Barat berhasil mengharumkan nama daerah dengan meraih penghargaan dalam ajang kompetisi nasional untuk kategori inovasi produk. Prestasi ini menunjukkan bahwa UMKM di Pesisir Barat memiliki potensi yang besar untuk bersaing di tingkat nasional.

Ketiga UMKM yang meraih penghargaan tersebut adalah:
1. CV Sari Laut - penghargaan untuk produk olahan ikan terbaik
2. UD Kerajinan Bambu - penghargaan untuk produk kerajinan inovatif
3. CV Kopi Pesisir - penghargaan untuk produk kopi berkualitas premium

Pemerintah daerah memberikan apresiasi tinggi atas prestasi yang diraih oleh ketiga UMKM tersebut. "Ini adalah bukti bahwa UMKM di Pesisir Barat memiliki kualitas yang tidak kalah dengan daerah lain," ujar Kepala Dinas Koperasi dan UKM.

Penghargaan ini diharapkan dapat memotivasi UMKM lain untuk terus berinovasi dan meningkatkan kualitas produknya.',
                'category_id' => 2, // Ekonomi
                'author_id' => 1,
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Sekolah Dasar Terbaik di Pesisir Barat',
                'slug' => 'sekolah-dasar-terbaik-di-pesisir-barat',
                'excerpt' => 'SD Negeri 1 Pesisir Barat meraih predikat sekolah terbaik tingkat provinsi dengan prestasi akademik dan non-akademik yang membanggakan.',
                'content' => 'SD Negeri 1 Pesisir Barat berhasil meraih predikat sebagai sekolah dasar terbaik tingkat provinsi Lampung. Prestasi ini diraih berkat pencapaian akademik dan non-akademik yang membanggakan selama tahun ajaran 2024.

Sekolah yang berlokasi di pusat kota Pesisir Barat ini berhasil mengungguli 1.200 sekolah dasar lainnya di seluruh provinsi Lampung. Prestasi yang diraih meliputi:

1. Nilai rata-rata Ujian Nasional tertinggi se-provinsi
2. Juara 1 lomba cerdas cermat tingkat provinsi
3. Juara 1 lomba paduan suara tingkat provinsi
4. Juara 2 lomba olahraga tingkat provinsi

Kepala Sekolah SD Negeri 1 Pesisir Barat mengungkapkan bahwa prestasi ini adalah hasil kerja keras seluruh warga sekolah, mulai dari guru, siswa, hingga orang tua murid. "Kami berkomitmen untuk terus meningkatkan kualitas pendidikan dan memberikan yang terbaik untuk anak-anak," ujarnya.

Pemerintah daerah memberikan apresiasi khusus atas prestasi yang diraih sekolah ini dan berharap dapat menjadi inspirasi bagi sekolah-sekolah lain di Pesisir Barat.',
                'category_id' => 7, // Pendidikan
                'author_id' => 1,
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(2),
            ],
            // Artikel
            [
                'title' => 'Tips Menjaga Kesehatan Mental di Era Digital',
                'slug' => 'tips-menjaga-kesehatan-mental-di-era-digital',
                'excerpt' => 'Panduan lengkap untuk menjaga kesehatan mental di tengah kemajuan teknologi dan media sosial yang semakin pesat.',
                'content' => 'Di era digital yang semakin maju, kesehatan mental menjadi aspek penting yang perlu diperhatikan. Teknologi dan media sosial yang berkembang pesat dapat memberikan dampak positif maupun negatif terhadap kondisi mental seseorang.

Berikut adalah beberapa tips yang dapat membantu menjaga kesehatan mental di era digital:

1. Batasi Waktu Penggunaan Gadget
   - Tetapkan waktu khusus untuk menggunakan smartphone atau komputer
   - Hindari penggunaan gadget sebelum tidur
   - Gunakan aplikasi yang dapat membatasi waktu penggunaan

2. Pilih Konten yang Positif
   - Ikuti akun-akun yang memberikan konten inspiratif dan positif
   - Hindari konten yang dapat memicu kecemasan atau stres
   - Berikan like dan komentar pada konten yang membangun

3. Jaga Interaksi Sosial Nyata
   - Luangkan waktu untuk bertemu dengan keluarga dan teman secara langsung
   - Lakukan aktivitas fisik bersama orang-orang terdekat
   - Jangan biarkan media sosial menggantikan interaksi sosial nyata

4. Praktikkan Mindfulness
   - Lakukan meditasi atau latihan pernapasan
   - Fokus pada momen saat ini tanpa distraksi gadget
   - Nikmati aktivitas sehari-hari tanpa terburu-buru

5. Cari Bantuan Profesional
   - Jangan ragu untuk berkonsultasi dengan psikolog atau konselor
   - Manfaatkan layanan konseling online jika diperlukan
   - Bergabung dengan komunitas yang mendukung kesehatan mental

Dengan menerapkan tips-tips di atas, kita dapat menjaga kesehatan mental di era digital dan tetap produktif dalam kehidupan sehari-hari.',
                'category_id' => 3, // Kesehatan
                'author_id' => 1,
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Mengenal Budaya dan Tradisi Masyarakat Pesisir Barat',
                'slug' => 'mengenal-budaya-dan-tradisi-masyarakat-pesisir-barat',
                'excerpt' => 'Eksplorasi mendalam tentang kekayaan budaya dan tradisi yang dimiliki oleh masyarakat Kabupaten Pesisir Barat.',
                'content' => 'Kabupaten Pesisir Barat memiliki kekayaan budaya dan tradisi yang sangat beragam. Sebagai daerah yang terletak di pesisir pantai barat Sumatera, masyarakat Pesisir Barat memiliki keunikan budaya yang dipengaruhi oleh berbagai faktor geografis dan historis.

1. Tradisi Nelayan
   Masyarakat Pesisir Barat memiliki tradisi nelayan yang kuat. Mereka memiliki ritual khusus sebelum melaut, seperti upacara selamatan dan doa bersama untuk keselamatan di laut. Tradisi ini mencerminkan kedekatan masyarakat dengan laut sebagai sumber kehidupan.

2. Seni Tari dan Musik
   Daerah ini memiliki berbagai jenis tarian tradisional yang menggambarkan kehidupan masyarakat pesisir. Tarian-tarian ini biasanya dipentaskan dalam acara-acara adat dan perayaan penting. Musik tradisional menggunakan alat musik seperti gendang, seruling, dan gong.

3. Kuliner Tradisional
   Makanan tradisional Pesisir Barat sangat kaya akan hasil laut. Ikan, udang, dan kerang menjadi bahan utama dalam berbagai hidangan tradisional. Teknik memasak yang unik dan bumbu-bumbu khas membuat kuliner daerah ini memiliki cita rasa yang khas.

4. Arsitektur Tradisional
   Rumah-rumah tradisional di Pesisir Barat memiliki ciri khas arsitektur yang disesuaikan dengan kondisi geografis. Rumah panggung dengan material kayu dan atap rumbia menjadi ciri khas yang masih dapat ditemui di beberapa daerah.

5. Upacara Adat
   Masyarakat Pesisir Barat masih mempertahankan berbagai upacara adat yang berkaitan dengan siklus kehidupan, seperti kelahiran, pernikahan, dan kematian. Upacara-upacara ini menjadi sarana untuk mempererat tali persaudaraan dan melestarikan nilai-nilai budaya.

Melestarikan budaya dan tradisi ini menjadi tanggung jawab bersama untuk memastikan bahwa kekayaan budaya Pesisir Barat dapat dinikmati oleh generasi mendatang.',
                'category_id' => 4, // Budaya
                'author_id' => 1,
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Panduan Wisata Alam di Pesisir Barat',
                'slug' => 'panduan-wisata-alam-di-pesisir-barat',
                'excerpt' => 'Jelajahi keindahan alam Pesisir Barat dengan panduan lengkap destinasi wisata yang wajib dikunjungi.',
                'content' => 'Kabupaten Pesisir Barat menawarkan berbagai destinasi wisata alam yang menakjubkan. Dari pantai yang indah hingga hutan yang hijau, daerah ini memiliki potensi wisata yang sangat besar.

1. Pantai Tanjung Setia
   Pantai ini terkenal dengan ombaknya yang cocok untuk surfing. Pantai Tanjung Setia menawarkan pemandangan sunset yang menakjubkan dan suasana yang tenang. Fasilitas yang tersedia meliputi area parkir, warung makan, dan penginapan sederhana.

2. Taman Nasional Bukit Barisan Selatan
   Taman nasional ini menjadi habitat bagi berbagai satwa liar, termasuk harimau sumatera dan gajah. Wisatawan dapat melakukan trekking dan bird watching di area ini. Pastikan untuk mendapatkan izin dan pemandu yang berpengalaman.

3. Air Terjun Way Lalaan
   Air terjun yang terletak di kawasan hutan ini menawarkan pemandangan yang menakjubkan. Air terjun setinggi 50 meter ini dikelilingi oleh hutan yang hijau dan udara yang sejuk. Perjalanan menuju air terjun membutuhkan trekking sekitar 2 jam.

4. Pantai Labuhan Jukung
   Pantai ini menawarkan pemandangan yang masih alami dengan pasir putih yang bersih. Pantai Labuhan Jukung cocok untuk kegiatan seperti berenang, snorkeling, dan camping. Fasilitas yang tersedia masih terbatas, sehingga wisatawan disarankan untuk membawa perlengkapan sendiri.

5. Gunung Pesagi
   Gunung Pesagi menawarkan pemandangan yang menakjubkan dari puncaknya. Pendakian ke gunung ini membutuhkan waktu sekitar 6-8 jam dan cocok untuk pendaki yang sudah berpengalaman. Dari puncak gunung, wisatawan dapat melihat pemandangan laut dan daratan yang luas.

Tips Wisata:
- Bawa perlengkapan yang cukup, terutama untuk destinasi yang masih alami
- Perhatikan cuaca dan kondisi jalan sebelum berangkat
- Hormati lingkungan dan jangan meninggalkan sampah
- Gunakan jasa pemandu lokal untuk destinasi yang membutuhkan keahlian khusus

Dengan mengikuti panduan ini, wisatawan dapat menikmati keindahan alam Pesisir Barat dengan aman dan nyaman.',
                'category_id' => 5, // Pariwisata
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(5),
            ],
            // Berita tambahan (ditulis oleh admin)
            [
                'title' => 'Pembangunan Jalan Tol Trans Sumatera Segera Dimulai di Pesisir Barat',
                'slug' => 'pembangunan-jalan-tol-trans-sumatera-segera-dimulai',
                'excerpt' => 'Pemerintah pusat mengumumkan dimulainya pembangunan jalan tol Trans Sumatera yang akan melewati Kabupaten Pesisir Barat.',
                'content' => 'Pemerintah pusat melalui Kementerian Pekerjaan Umum dan Perumahan Rakyat (PUPR) mengumumkan dimulainya pembangunan jalan tol Trans Sumatera yang akan melewati Kabupaten Pesisir Barat. Proyek ini merupakan bagian dari program strategis nasional untuk meningkatkan konektivitas antar daerah di Pulau Sumatera.

Pembangunan jalan tol sepanjang 45 kilometer yang akan melewati Pesisir Barat ini diharapkan dapat mengurangi waktu tempuh dari Bandar Lampung ke Bengkulu dari 8 jam menjadi 4 jam. Proyek ini juga akan membuka akses yang lebih mudah untuk transportasi barang dan jasa.

"Pembangunan jalan tol ini akan memberikan dampak positif yang sangat besar bagi perekonomian masyarakat Pesisir Barat," ujar Bupati dalam konferensi pers. "Kami berharap proyek ini dapat diselesaikan sesuai jadwal yang telah ditetapkan."

Proyek ini akan dimulai pada bulan depan dan diharapkan selesai dalam waktu 3 tahun. Pemerintah daerah telah mempersiapkan berbagai langkah untuk memastikan proyek ini berjalan lancar dan memberikan manfaat maksimal bagi masyarakat.',
                'category_id' => 1, // Politik
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => true,
                'is_breaking' => true,
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Festival Kuliner Pesisir Barat Sukses Digelar, Ribuan Pengunjung Hadir',
                'slug' => 'festival-kuliner-pesisir-barat-sukses-digelar',
                'excerpt' => 'Festival kuliner yang digelar selama 3 hari berhasil menarik ribuan pengunjung dan mempromosikan kuliner khas daerah.',
                'content' => 'Festival Kuliner Pesisir Barat yang digelar selama 3 hari berturut-turut berhasil menarik ribuan pengunjung dari berbagai daerah. Festival yang diselenggarakan di Lapangan Merdeka ini menampilkan berbagai kuliner khas Pesisir Barat yang lezat dan unik.

Lebih dari 50 stand kuliner berpartisipasi dalam festival ini, menampilkan berbagai hidangan seperti ikan bakar, sate kerang, kerupuk kulit, dan berbagai olahan laut lainnya. Festival ini juga menampilkan berbagai kuliner tradisional yang jarang ditemui di tempat lain.

"Festival ini sangat sukses dan melebihi ekspektasi kami," ujar Ketua Panitia Festival. "Kami berharap festival ini dapat menjadi agenda rutin tahunan yang dapat mempromosikan kuliner Pesisir Barat ke tingkat nasional."

Selain kuliner, festival ini juga menampilkan berbagai pertunjukan seni dan budaya, serta lomba masak yang diikuti oleh ibu-ibu rumah tangga dari berbagai desa. Festival ini diharapkan dapat meningkatkan pariwisata dan perekonomian masyarakat setempat.',
                'category_id' => 2, // Ekonomi
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Tim Sepak Bola Pesisir Barat Juara Turnamen Antar Kabupaten',
                'slug' => 'tim-sepak-bola-pesisir-barat-juara-turnamen',
                'excerpt' => 'Tim sepak bola Pesisir Barat berhasil meraih juara pertama dalam turnamen antar kabupaten se-provinsi Lampung.',
                'content' => 'Tim sepak bola Pesisir Barat berhasil mengharumkan nama daerah dengan meraih juara pertama dalam turnamen antar kabupaten se-provinsi Lampung. Prestasi ini diraih setelah mengalahkan tim dari Kabupaten Lampung Selatan dengan skor 2-1 di final yang digelar di Stadion Sumpah Pemuda, Bandar Lampung.

Turnamen yang diikuti oleh 15 kabupaten/kota se-provinsi Lampung ini berlangsung selama 2 minggu. Tim Pesisir Barat menunjukkan performa yang konsisten sepanjang turnamen, dengan catatan 6 menang dan 1 seri.

"Ini adalah prestasi yang membanggakan bagi kami semua," ujar Kapten Tim Pesisir Barat. "Kami berterima kasih kepada semua pihak yang telah mendukung tim ini, terutama pemerintah daerah dan masyarakat Pesisir Barat."

Prestasi ini diharapkan dapat memotivasi generasi muda Pesisir Barat untuk lebih giat berlatih olahraga dan meraih prestasi di tingkat yang lebih tinggi. Pemerintah daerah berencana untuk memberikan apresiasi khusus kepada tim yang telah mengharumkan nama daerah.',
                'category_id' => 4, // Olahraga
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Pembangunan Pusat Kesehatan Masyarakat Baru di Pesisir Barat',
                'slug' => 'pembangunan-pusat-kesehatan-masyarakat-baru',
                'excerpt' => 'Pemerintah daerah memulai pembangunan pusat kesehatan masyarakat baru untuk meningkatkan pelayanan kesehatan.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat memulai pembangunan pusat kesehatan masyarakat (puskesmas) baru yang akan menjadi pusat pelayanan kesehatan terpadu bagi masyarakat. Puskesmas baru ini dibangun dengan anggaran sebesar 5 miliar rupiah dan akan dilengkapi dengan berbagai fasilitas kesehatan modern.

Puskesmas baru ini akan memiliki 3 lantai dengan berbagai ruang pelayanan, termasuk ruang rawat inap, ruang operasi, laboratorium, dan apotek. Fasilitas ini juga akan dilengkapi dengan peralatan medis yang canggih untuk memberikan pelayanan kesehatan yang berkualitas.

"Pembangunan puskesmas ini merupakan komitmen kami untuk meningkatkan kualitas pelayanan kesehatan masyarakat," ujar Kepala Dinas Kesehatan. "Kami berharap dengan adanya fasilitas ini, masyarakat dapat memperoleh pelayanan kesehatan yang lebih baik dan mudah diakses."

Pembangunan puskesmas ini diharapkan selesai dalam waktu 18 bulan dan akan melayani lebih dari 50.000 penduduk di wilayah Pesisir Barat. Pemerintah daerah juga berencana untuk menambah tenaga medis dan paramedis untuk mendukung operasional puskesmas baru ini.',
                'category_id' => 6, // Kesehatan
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(9),
            ],
            [
                'title' => 'Program Beasiswa untuk Mahasiswa Berprestasi di Pesisir Barat',
                'slug' => 'program-beasiswa-untuk-mahasiswa-berprestasi',
                'excerpt' => 'Pemerintah daerah meluncurkan program beasiswa untuk mahasiswa berprestasi dari keluarga kurang mampu.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat meluncurkan program beasiswa untuk mahasiswa berprestasi dari keluarga kurang mampu. Program ini bertujuan untuk memberikan kesempatan yang sama bagi semua anak untuk memperoleh pendidikan tinggi yang berkualitas.

Program beasiswa ini akan memberikan bantuan biaya pendidikan sebesar 2 juta rupiah per semester untuk 100 mahasiswa terpilih. Seleksi dilakukan berdasarkan prestasi akademik, kondisi ekonomi keluarga, dan komitmen untuk kembali mengabdi di Pesisir Barat setelah lulus.

"Program ini merupakan investasi jangka panjang untuk membangun sumber daya manusia yang berkualitas di Pesisir Barat," ujar Bupati dalam peluncuran program. "Kami berharap program ini dapat membantu mahasiswa berprestasi untuk meraih cita-cita mereka."

Program beasiswa ini akan berjalan selama 4 tahun dan diharapkan dapat menghasilkan lulusan yang berkualitas dan siap berkontribusi untuk pembangunan daerah. Pemerintah daerah juga berencana untuk menambah kuota beasiswa di tahun-tahun berikutnya.',
                'category_id' => 7, // Pendidikan
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Pembangunan PLTS untuk Meningkatkan Pasokan Listrik di Pesisir Barat',
                'slug' => 'pembangunan-plts-untuk-meningkatkan-pasokan-listrik',
                'excerpt' => 'Pemerintah daerah memulai pembangunan pembangkit listrik tenaga surya untuk meningkatkan pasokan listrik.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat memulai pembangunan pembangkit listrik tenaga surya (PLTS) untuk meningkatkan pasokan listrik di daerah ini. Proyek ini merupakan bagian dari program transisi energi menuju energi terbarukan yang ramah lingkungan.

PLTS yang dibangun memiliki kapasitas 10 megawatt dan akan dapat memenuhi kebutuhan listrik untuk 5.000 rumah tangga. Proyek ini dibangun dengan anggaran sebesar 15 miliar rupiah dan diharapkan dapat mengurangi ketergantungan pada listrik dari PLN.

"Pembangunan PLTS ini merupakan langkah strategis untuk meningkatkan pasokan listrik dan mendukung program transisi energi," ujar Kepala Dinas Energi dan Sumber Daya Mineral. "Kami berharap proyek ini dapat memberikan manfaat yang besar bagi masyarakat."

Proyek ini diharapkan selesai dalam waktu 2 tahun dan akan menjadi model untuk pengembangan energi terbarukan di daerah lain. Pemerintah daerah juga berencana untuk mengembangkan PLTS skala kecil di desa-desa yang belum terjangkau listrik.',
                'category_id' => 5, // Teknologi
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(11),
            ],
            [
                'title' => 'Pembangunan Taman Kota untuk Meningkatkan Kualitas Lingkungan',
                'slug' => 'pembangunan-taman-kota-untuk-meningkatkan-kualitas-lingkungan',
                'excerpt' => 'Pemerintah daerah memulai pembangunan taman kota yang akan menjadi ruang hijau dan tempat rekreasi masyarakat.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat memulai pembangunan taman kota yang akan menjadi ruang hijau dan tempat rekreasi masyarakat. Taman kota ini dibangun dengan konsep eco-park yang ramah lingkungan dan berkelanjutan.

Taman kota seluas 5 hektar ini akan dilengkapi dengan berbagai fasilitas, termasuk jogging track, area bermain anak, kolam air mancur, dan berbagai jenis tanaman hias. Taman ini juga akan memiliki sistem pengelolaan air yang efisien dan ramah lingkungan.

"Pembangunan taman kota ini merupakan upaya kami untuk meningkatkan kualitas lingkungan dan memberikan ruang hijau bagi masyarakat," ujar Kepala Dinas Lingkungan Hidup. "Kami berharap taman ini dapat menjadi tempat yang nyaman untuk beraktivitas dan beristirahat."

Pembangunan taman kota ini diharapkan selesai dalam waktu 1 tahun dan akan menjadi ikon baru di Pesisir Barat. Pemerintah daerah juga berencana untuk mengembangkan taman-taman serupa di berbagai lokasi strategis di daerah ini.',
                'category_id' => 3, // Sosial
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Pembangunan Pasar Rakyat Modern di Pesisir Barat',
                'slug' => 'pembangunan-pasar-rakyat-modern-di-pesisir-barat',
                'excerpt' => 'Pemerintah daerah memulai pembangunan pasar rakyat modern yang akan menjadi pusat perekonomian masyarakat.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat memulai pembangunan pasar rakyat modern yang akan menjadi pusat perekonomian masyarakat. Pasar rakyat modern ini dibangun dengan konsep yang bersih, nyaman, dan modern untuk memberikan pengalaman berbelanja yang lebih baik.

Pasar rakyat modern seluas 3 hektar ini akan memiliki 500 kios yang dilengkapi dengan berbagai fasilitas, termasuk sistem pendingin, area parkir yang luas, dan sistem keamanan yang terintegrasi. Pasar ini juga akan memiliki area khusus untuk pedagang kaki lima dan area parkir yang memadai.

"Pembangunan pasar rakyat modern ini merupakan upaya kami untuk meningkatkan kualitas perekonomian masyarakat," ujar Kepala Dinas Perdagangan. "Kami berharap pasar ini dapat menjadi pusat perekonomian yang modern dan berkelanjutan."

Pembangunan pasar rakyat modern ini diharapkan selesai dalam waktu 2 tahun dan akan melayani lebih dari 100.000 penduduk di wilayah Pesisir Barat. Pemerintah daerah juga berencana untuk mengembangkan pasar-pasar serupa di berbagai kecamatan.',
                'category_id' => 2, // Ekonomi
                'author_id' => 1, // Admin
                'status' => 'published',
                'type' => 'berita',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(13),
            ],
            // Artikel tambahan (ditulis oleh penulis)
            [
                'title' => 'Mengenal Sejarah dan Perkembangan Pesisir Barat',
                'slug' => 'mengenal-sejarah-dan-perkembangan-pesisir-barat',
                'excerpt' => 'Eksplorasi mendalam tentang sejarah dan perkembangan Kabupaten Pesisir Barat dari masa ke masa.',
                'content' => 'Kabupaten Pesisir Barat memiliki sejarah yang panjang dan menarik. Sebagai daerah yang terletak di pesisir pantai barat Sumatera, daerah ini memiliki peran penting dalam sejarah perdagangan dan budaya di wilayah Sumatera.

1. Masa Pra-Kolonial
   Sebelum kedatangan kolonial Belanda, Pesisir Barat merupakan bagian dari Kerajaan Tulang Bawang yang berpusat di Lampung. Daerah ini menjadi pusat perdagangan rempah-rempah dan hasil laut yang sangat penting.

2. Masa Kolonial Belanda
   Pada masa kolonial Belanda, Pesisir Barat menjadi bagian dari Karesidenan Lampung. Belanda membangun berbagai infrastruktur, termasuk pelabuhan dan jalan raya, untuk memudahkan eksploitasi sumber daya alam.

3. Masa Kemerdekaan
   Setelah kemerdekaan Indonesia, Pesisir Barat menjadi bagian dari Provinsi Lampung. Daerah ini mengalami berbagai perubahan administrasi hingga akhirnya menjadi kabupaten sendiri pada tahun 2008.

4. Perkembangan Modern
   Sejak menjadi kabupaten sendiri, Pesisir Barat mengalami perkembangan yang pesat. Pembangunan infrastruktur, pendidikan, dan kesehatan menjadi prioritas utama pemerintah daerah.

5. Potensi Masa Depan
   Pesisir Barat memiliki potensi yang besar untuk berkembang menjadi daerah yang maju dan sejahtera. Sektor pariwisata, perikanan, dan pertanian menjadi andalan untuk pembangunan masa depan.

Dengan memahami sejarah dan perkembangan Pesisir Barat, kita dapat lebih menghargai perjalanan panjang yang telah dilalui daerah ini dan berharap untuk masa depan yang lebih baik.',
                'category_id' => 4, // Budaya
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'Panduan Budidaya Ikan Laut untuk Nelayan Pemula',
                'slug' => 'panduan-budidaya-ikan-laut-untuk-nelayan-pemula',
                'excerpt' => 'Panduan lengkap untuk memulai budidaya ikan laut yang cocok untuk nelayan pemula di Pesisir Barat.',
                'content' => 'Budidaya ikan laut menjadi salah satu alternatif yang menjanjikan untuk meningkatkan pendapatan nelayan di Pesisir Barat. Dengan kondisi geografis yang mendukung, daerah ini memiliki potensi yang besar untuk pengembangan budidaya ikan laut.

1. Pemilihan Lokasi
   - Pilih lokasi yang terlindung dari ombak besar
   - Pastikan kualitas air laut yang baik
   - Perhatikan kedalaman air yang sesuai
   - Pastikan akses transportasi yang mudah

2. Jenis Ikan yang Cocok
   - Ikan Kerapu: memiliki nilai jual tinggi dan permintaan yang stabil
   - Ikan Kakap: mudah dibudidayakan dan memiliki pasar yang luas
   - Ikan Bawal: tahan terhadap penyakit dan memiliki pertumbuhan yang cepat
   - Ikan Nila: cocok untuk pemula dan memiliki biaya produksi yang rendah

3. Teknik Budidaya
   - Gunakan keramba jaring apung (KJA) untuk efisiensi ruang
   - Perhatikan kepadatan tebar yang sesuai
   - Berikan pakan yang berkualitas dan sesuai kebutuhan
   - Lakukan monitoring kesehatan ikan secara rutin

4. Manajemen Kualitas Air
   - Perhatikan suhu air yang optimal (26-30°C)
   - Jaga kadar oksigen terlarut minimal 5 ppm
   - Monitor pH air antara 7.5-8.5
   - Lakukan pergantian air secara berkala

5. Pemasaran
   - Bangun jaringan pemasaran yang kuat
   - Manfaatkan teknologi digital untuk promosi
   - Jalin kerjasama dengan restoran dan hotel
   - Pertimbangkan ekspor untuk pasar yang lebih luas

Dengan mengikuti panduan ini, nelayan pemula dapat memulai budidaya ikan laut yang menguntungkan dan berkelanjutan.',
                'category_id' => 2, // Ekonomi
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Tips Menjaga Kesehatan di Musim Hujan',
                'slug' => 'tips-menjaga-kesehatan-di-musim-hujan',
                'excerpt' => 'Panduan lengkap untuk menjaga kesehatan dan mencegah penyakit di musim hujan.',
                'content' => 'Musim hujan seringkali membawa berbagai penyakit dan masalah kesehatan. Dengan mengikuti tips berikut, kita dapat menjaga kesehatan dan mencegah berbagai penyakit yang umum terjadi di musim hujan.

1. Menjaga Kebersihan Diri
   - Mandi minimal 2 kali sehari dengan sabun antiseptik
   - Cuci tangan dengan sabun sebelum makan dan setelah beraktivitas
   - Ganti pakaian yang basah atau lembab segera
   - Jaga kebersihan kuku dan rambut

2. Menjaga Kebersihan Lingkungan
   - Bersihkan genangan air di sekitar rumah
   - Pastikan saluran air berfungsi dengan baik
   - Buang sampah pada tempatnya dan tutup rapat
   - Bersihkan tempat penampungan air secara berkala

3. Meningkatkan Daya Tahan Tubuh
   - Konsumsi makanan bergizi dan seimbang
   - Perbanyak konsumsi buah dan sayuran
   - Minum air putih yang cukup (minimal 8 gelas sehari)
   - Konsumsi vitamin C dan suplemen jika diperlukan

4. Mencegah Penyakit Menular
   - Gunakan masker saat berada di tempat ramai
   - Hindari kontak langsung dengan penderita penyakit
   - Lakukan vaksinasi sesuai jadwal yang ditentukan
   - Jaga jarak dengan orang yang sedang sakit

5. Menjaga Kesehatan Mental
   - Lakukan aktivitas fisik ringan di dalam ruangan
   - Jaga pola tidur yang teratur
   - Lakukan hobi atau aktivitas yang menyenangkan
   - Jangan ragu untuk berkonsultasi dengan tenaga kesehatan jika mengalami gejala yang mengkhawatirkan

Dengan mengikuti tips ini, kita dapat menjaga kesehatan dan tetap produktif di musim hujan.',
                'category_id' => 6, // Kesehatan
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(16),
            ],
            [
                'title' => 'Mengenal Teknologi Digital untuk UMKM di Pesisir Barat',
                'slug' => 'mengenal-teknologi-digital-untuk-umkm-di-pesisir-barat',
                'excerpt' => 'Panduan untuk memanfaatkan teknologi digital dalam mengembangkan UMKM di Pesisir Barat.',
                'content' => 'Teknologi digital menjadi kunci penting dalam mengembangkan UMKM di era modern. Dengan memanfaatkan teknologi yang tepat, UMKM dapat meningkatkan efisiensi, menjangkau pasar yang lebih luas, dan meningkatkan pendapatan.

1. E-Commerce dan Marketplace
   - Manfaatkan platform e-commerce seperti Tokopedia, Shopee, dan Bukalapak
   - Buat website sendiri untuk menjual produk secara online
   - Gunakan media sosial untuk promosi dan penjualan
   - Pertimbangkan dropshipping untuk mengurangi risiko stok

2. Digital Marketing
   - Gunakan Google Ads dan Facebook Ads untuk promosi yang tepat sasaran
   - Manfaatkan SEO untuk meningkatkan visibilitas online
   - Buat konten yang menarik di media sosial
   - Gunakan influencer marketing untuk menjangkau audiens yang lebih luas

3. Sistem Manajemen
   - Gunakan aplikasi akuntansi untuk mengelola keuangan
   - Manfaatkan CRM untuk mengelola pelanggan
   - Gunakan sistem inventory untuk mengelola stok
   - Pertimbangkan ERP untuk mengintegrasikan berbagai proses bisnis

4. Pembayaran Digital
   - Terima pembayaran melalui e-wallet dan transfer bank
   - Gunakan QRIS untuk kemudahan transaksi
   - Pertimbangkan cryptocurrency untuk transaksi internasional
   - Manfaatkan layanan pembayaran online yang aman dan terpercaya

5. Analisis Data
   - Gunakan Google Analytics untuk menganalisis traffic website
   - Manfaatkan data dari media sosial untuk memahami pelanggan
   - Gunakan tools analisis untuk mengoptimalkan performa bisnis
   - Pertimbangkan AI dan machine learning untuk prediksi pasar

Dengan mengadopsi teknologi digital yang tepat, UMKM di Pesisir Barat dapat bersaing di pasar global dan mencapai pertumbuhan yang berkelanjutan.',
                'category_id' => 5, // Teknologi
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(17),
            ],
            [
                'title' => 'Panduan Olahraga untuk Pemula di Pesisir Barat',
                'slug' => 'panduan-olahraga-untuk-pemula-di-pesisir-barat',
                'excerpt' => 'Panduan lengkap untuk memulai olahraga yang cocok untuk pemula di Pesisir Barat.',
                'content' => 'Olahraga menjadi bagian penting dari gaya hidup sehat. Bagi pemula, memilih jenis olahraga yang tepat dan melakukannya dengan benar sangat penting untuk menghindari cedera dan mendapatkan manfaat maksimal.

1. Olahraga Kardio
   - Jogging: Mulai dengan 15-20 menit, tingkatkan secara bertahap
   - Bersepeda: Pilih rute yang aman dan sesuai kemampuan
   - Berenang: Manfaatkan kolam renang atau pantai yang aman
   - Senam aerobik: Ikuti kelas atau video tutorial online

2. Olahraga Kekuatan
   - Push-up: Mulai dengan 5-10 repetisi, tingkatkan secara bertahap
   - Squat: Lakukan dengan teknik yang benar untuk menghindari cedera
   - Plank: Mulai dengan 30 detik, tingkatkan durasi secara bertahap
   - Angkat beban: Gunakan beban ringan dan tingkatkan secara bertahap

3. Olahraga Fleksibilitas
   - Yoga: Ikuti kelas atau video tutorial online
   - Stretching: Lakukan sebelum dan setelah olahraga
   - Pilates: Fokus pada kekuatan inti dan fleksibilitas
   - Tai Chi: Olahraga yang lembut dan cocok untuk semua usia

4. Tips untuk Pemula
   - Mulai dengan intensitas rendah dan tingkatkan secara bertahap
   - Lakukan pemanasan sebelum olahraga dan pendinginan setelahnya
   - Gunakan perlengkapan yang sesuai dan nyaman
   - Dengarkan tubuh dan jangan memaksakan diri

5. Tempat Olahraga di Pesisir Barat
   - Lapangan Merdeka: Cocok untuk jogging dan senam
   - Pantai Tanjung Setia: Cocok untuk jogging dan bersepeda
   - Taman Kota: Cocok untuk berbagai jenis olahraga
   - Gym dan fitness center: Cocok untuk olahraga dalam ruangan

Dengan mengikuti panduan ini, pemula dapat memulai olahraga dengan aman dan efektif di Pesisir Barat.',
                'category_id' => 4, // Olahraga
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(18),
            ],
            [
                'title' => 'Mengenal Pendidikan Inklusif di Pesisir Barat',
                'slug' => 'mengenal-pendidikan-inklusif-di-pesisir-barat',
                'excerpt' => 'Eksplorasi tentang implementasi pendidikan inklusif di Pesisir Barat dan manfaatnya bagi masyarakat.',
                'content' => 'Pendidikan inklusif menjadi konsep penting dalam memberikan kesempatan yang sama bagi semua anak untuk memperoleh pendidikan yang berkualitas. Di Pesisir Barat, implementasi pendidikan inklusif telah memberikan dampak positif yang signifikan bagi masyarakat.

1. Konsep Pendidikan Inklusif
   - Pendidikan yang memberikan kesempatan yang sama bagi semua anak
   - Tidak membedakan berdasarkan kemampuan, latar belakang, atau kondisi khusus
   - Fokus pada pengembangan potensi dan bakat setiap anak
   - Menciptakan lingkungan belajar yang ramah dan mendukung

2. Implementasi di Pesisir Barat
   - Sekolah-sekolah telah mengadopsi kurikulum yang inklusif
   - Guru-guru telah dilatih untuk menangani siswa dengan kebutuhan khusus
   - Fasilitas sekolah telah disesuaikan untuk aksesibilitas
   - Program pendampingan untuk siswa dengan kebutuhan khusus

3. Manfaat bagi Siswa
   - Meningkatkan rasa percaya diri dan motivasi belajar
   - Mengembangkan kemampuan sosial dan komunikasi
   - Memberikan kesempatan untuk belajar dari keberagaman
   - Meningkatkan prestasi akademik dan non-akademik

4. Manfaat bagi Masyarakat
   - Meningkatkan kesadaran tentang keberagaman dan inklusi
   - Mengurangi stigma terhadap anak dengan kebutuhan khusus
   - Meningkatkan partisipasi masyarakat dalam pendidikan
   - Menciptakan masyarakat yang lebih inklusif dan toleran

5. Tantangan dan Solusi
   - Tantangan: Keterbatasan sumber daya dan tenaga pendidik
   - Solusi: Pelatihan berkelanjutan dan peningkatan fasilitas
   - Tantangan: Resistensi dari sebagian masyarakat
   - Solusi: Sosialisasi dan edukasi tentang manfaat pendidikan inklusif

Dengan implementasi pendidikan inklusif yang baik, Pesisir Barat dapat menciptakan generasi yang lebih inklusif, toleran, dan siap menghadapi tantangan masa depan.',
                'category_id' => 7, // Pendidikan
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(19),
            ],
            [
                'title' => 'Panduan Berkebun di Lahan Sempit untuk Masyarakat Pesisir Barat',
                'slug' => 'panduan-berkebun-di-lahan-sempit-untuk-masyarakat-pesisir-barat',
                'excerpt' => 'Panduan praktis untuk berkebun di lahan sempit yang cocok untuk masyarakat Pesisir Barat.',
                'content' => 'Berkebun di lahan sempit menjadi solusi yang tepat untuk masyarakat yang tinggal di perkotaan atau memiliki lahan terbatas. Dengan teknik yang tepat, kita dapat menanam berbagai jenis tanaman dan mendapatkan hasil yang memuaskan.

1. Teknik Vertikal Gardening
   - Gunakan dinding atau pagar untuk menanam tanaman
   - Buat rak bertingkat untuk memaksimalkan ruang
   - Gunakan pot gantung untuk tanaman yang tidak membutuhkan banyak tanah
   - Manfaatkan atap atau teras untuk menanam tanaman

2. Pilihan Tanaman yang Cocok
   - Sayuran daun: kangkung, bayam, selada, dan sawi
   - Herbal: kemangi, mint, dan ketumbar
   - Buah-buahan: tomat, cabai, dan terong
   - Tanaman hias: anggrek, kaktus, dan sukulen

3. Media Tanam yang Tepat
   - Gunakan campuran tanah, kompos, dan sekam padi
   - Pastikan drainase yang baik untuk mencegah busuk akar
   - Gunakan pot dengan lubang drainase yang cukup
   - Pertimbangkan hidroponik untuk efisiensi ruang

4. Perawatan yang Efektif
   - Siram tanaman secara teratur sesuai kebutuhan
   - Berikan pupuk organik secara berkala
   - Lakukan pemangkasan untuk menjaga bentuk tanaman
   - Perhatikan hama dan penyakit yang mungkin menyerang

5. Tips untuk Pemula
   - Mulai dengan tanaman yang mudah perawatannya
   - Pelajari kebutuhan spesifik setiap tanaman
   - Gunakan alat berkebun yang sesuai dan berkualitas
   - Jangan ragu untuk bertanya kepada ahli atau bergabung dengan komunitas berkebun

Dengan mengikuti panduan ini, masyarakat Pesisir Barat dapat menikmati berkebun di lahan sempit dan mendapatkan hasil yang memuaskan.',
                'category_id' => 3, // Sosial
                'author_id' => 3, // Penulis
                'status' => 'published',
                'type' => 'artikel',
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(20),
            ],
        ];

        // Get the first user (admin) as default author
        $adminUser = \App\Models\User::first();
        
        foreach ($articles as $article) {
            // Set author_id to admin user if not specified or if author doesn't exist
            if (!isset($article['author_id']) || !\App\Models\User::find($article['author_id'])) {
                $article['author_id'] = $adminUser->id;
            }
            
            \App\Models\Article::firstOrCreate(
                ['slug' => $article['slug']],
                $article
            );
        }
    }
}
