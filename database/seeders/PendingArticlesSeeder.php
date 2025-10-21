<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PendingArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get penulis users
        $penulisUsers = User::where('role', 'penulis')->get();
        
        if ($penulisUsers->isEmpty()) {
            $this->command->info('No penulis users found. Please create penulis users first.');
            return;
        }

        // Get categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get tags
        $tags = Tag::all();
        if ($tags->isEmpty()) {
            $this->command->info('No tags found. Please run TagSeeder first.');
            return;
        }

        // Create 10 pending articles
        $pendingArticles = [
            [
                'title' => 'Pembangunan Jalan Raya Pesisir Barat Capai 75% Pengerjaan',
                'excerpt' => 'Proyek pembangunan jalan raya sepanjang 25 km di Kabupaten Pesisir Barat telah mencapai 75% pengerjaan dan diharapkan selesai pada akhir tahun ini.',
                'content' => 'Pembangunan jalan raya yang menghubungkan Kecamatan Pesisir Tengah dengan Pesisir Selatan di Kabupaten Pesisir Barat telah mencapai 75% pengerjaan. Proyek senilai 15 miliar rupiah ini diharapkan dapat meningkatkan konektivitas antar wilayah dan mendukung pertumbuhan ekonomi daerah.

Menurut laporan dari Dinas Pekerjaan Umum dan Penataan Ruang (PUPR) Kabupaten Pesisir Barat, pembangunan jalan sepanjang 25 kilometer ini dimulai pada awal tahun 2024 dan ditargetkan selesai pada Desember 2024. Jalan ini akan memiliki lebar 7 meter dengan konstruksi perkerasan aspal yang berkualitas tinggi.

"Pembangunan jalan ini merupakan prioritas utama pemerintah daerah untuk meningkatkan aksesibilitas masyarakat ke berbagai layanan publik dan pusat ekonomi," ujar Kepala Dinas PUPR Pesisir Barat.

Proyek ini juga meliputi pembangunan jembatan kecil dan gorong-gorong untuk mengatasi masalah banjir yang sering terjadi di musim hujan. Dengan adanya infrastruktur yang memadai, diharapkan dapat mendorong pertumbuhan sektor pariwisata dan pertanian di wilayah tersebut.

Masyarakat setempat menyambut baik pembangunan jalan ini karena akan mempermudah akses transportasi dan mengurangi waktu tempuh antar kecamatan. Selain itu, pembangunan ini juga akan menciptakan lapangan kerja bagi warga lokal selama masa konstruksi.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Politik')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Festival Budaya Pesisir Barat 2024 Siap Digelar',
                'excerpt' => 'Festival Budaya Pesisir Barat 2024 akan digelar selama 3 hari dengan menampilkan berbagai kesenian tradisional dan kuliner khas daerah.',
                'content' => 'Kabupaten Pesisir Barat akan menggelar Festival Budaya Pesisir Barat 2024 yang akan berlangsung selama 3 hari berturut-turut. Acara yang akan digelar di Lapangan Merdeka ini akan menampilkan berbagai kesenian tradisional, kuliner khas daerah, dan pameran produk unggulan lokal.

Festival yang mengusung tema "Melestarikan Budaya, Memajukan Pariwisata" ini diharapkan dapat menjadi ajang promosi budaya dan pariwisata Pesisir Barat kepada masyarakat luas. Acara akan dimulai dengan parade budaya yang melibatkan berbagai sanggar seni dari seluruh kecamatan.

"Festival ini merupakan wujud komitmen kami untuk melestarikan budaya lokal sekaligus mendorong pertumbuhan pariwisata di Pesisir Barat," kata Kepala Dinas Pariwisata dan Kebudayaan.

Selain pertunjukan seni, festival juga akan menampilkan berbagai kuliner khas seperti ikan bakar, sate ikan, dan berbagai olahan seafood yang menjadi ciri khas daerah pesisir. Pengunjung juga dapat menikmati pameran kerajinan tangan dan produk unggulan lokal.

Acara ini juga akan diisi dengan berbagai lomba tradisional seperti lomba perahu layar, lomba memancing, dan lomba kuliner tradisional. Festival diharapkan dapat menarik minat wisatawan dari berbagai daerah untuk berkunjung ke Pesisir Barat.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Budaya')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Program Beasiswa Pendidikan Tinggi untuk Putra Putri Pesisir Barat',
                'excerpt' => 'Pemerintah Kabupaten Pesisir Barat membuka program beasiswa pendidikan tinggi untuk putra putri daerah yang berprestasi.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Dinas Pendidikan membuka program beasiswa pendidikan tinggi untuk putra putri daerah yang berprestasi. Program ini bertujuan untuk meningkatkan kualitas sumber daya manusia dan memberikan kesempatan pendidikan yang lebih baik bagi generasi muda Pesisir Barat.

Beasiswa ini akan diberikan kepada 50 siswa berprestasi yang telah lulus SMA/SMK dan berhasil diterima di perguruan tinggi negeri maupun swasta terakreditasi. Setiap penerima beasiswa akan mendapatkan bantuan biaya pendidikan sebesar 5 juta rupiah per semester selama 8 semester.

"Program beasiswa ini merupakan investasi jangka panjang untuk masa depan Pesisir Barat. Kami berharap dengan pendidikan yang baik, putra putri daerah dapat kembali membangun daerahnya," ujar Kepala Dinas Pendidikan Pesisir Barat.

Syarat untuk mengikuti program beasiswa ini antara lain memiliki IPK minimal 3.0, aktif dalam kegiatan kemahasiswaan, dan berkomitmen untuk kembali berkontribusi di Pesisir Barat setelah lulus. Seleksi akan dilakukan melalui tes tertulis, wawancara, dan penilaian prestasi akademik.

Program ini juga dilengkapi dengan mentoring dan pembinaan khusus untuk memastikan penerima beasiswa dapat menyelesaikan pendidikan dengan baik. Pemerintah daerah juga akan memfasilitasi magang dan pelatihan kerja untuk mempersiapkan mereka memasuki dunia kerja.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Pendidikan')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Peningkatan Produksi Ikan Tuna di Perairan Pesisir Barat',
                'excerpt' => 'Produksi ikan tuna di perairan Pesisir Barat mengalami peningkatan signifikan berkat program pemberdayaan nelayan dan modernisasi alat tangkap.',
                'content' => 'Produksi ikan tuna di perairan Kabupaten Pesisir Barat mengalami peningkatan signifikan sebesar 40% dibandingkan tahun sebelumnya. Peningkatan ini berkat program pemberdayaan nelayan dan modernisasi alat tangkap yang dilakukan oleh Dinas Kelautan dan Perikanan.

Program yang dimulai sejak awal tahun 2024 ini meliputi pelatihan teknik penangkapan ikan yang ramah lingkungan, pemberian bantuan alat tangkap modern, dan pembentukan kelompok nelayan yang lebih terorganisir. Hasilnya, produksi ikan tuna mencapai 2.500 ton per bulan.

"Peningkatan produksi ini tidak hanya meningkatkan pendapatan nelayan, tetapi juga mendukung ketahanan pangan nasional," kata Kepala Dinas Kelautan dan Perikanan Pesisir Barat.

Modernisasi alat tangkap meliputi penggunaan GPS, fish finder, dan jaring yang lebih selektif untuk mengurangi tangkapan sampingan. Nelayan juga dilatih untuk menerapkan praktik penangkapan yang berkelanjutan dan ramah lingkungan.

Program ini juga dilengkapi dengan pembentukan cold storage dan unit pengolahan ikan untuk meningkatkan nilai tambah produk. Hasilnya, harga jual ikan tuna di pasar lokal dan ekspor mengalami peningkatan yang signifikan.

Pemerintah daerah juga memfasilitasi akses permodalan melalui koperasi nelayan dan program kredit usaha rakyat (KUR) untuk mendukung pengembangan usaha perikanan. Dengan dukungan ini, nelayan dapat mengembangkan usaha mereka secara berkelanjutan.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Ekonomi')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Pembangunan Pusat Kesehatan Masyarakat di Desa Terpencil',
                'excerpt' => 'Pemerintah Kabupaten Pesisir Barat membangun pusat kesehatan masyarakat di desa-desa terpencil untuk meningkatkan akses layanan kesehatan.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Dinas Kesehatan membangun pusat kesehatan masyarakat (puskesmas) di 5 desa terpencil yang selama ini kesulitan mengakses layanan kesehatan. Pembangunan ini merupakan bagian dari program pemerataan layanan kesehatan di seluruh wilayah Pesisir Barat.

Puskesmas yang dibangun akan dilengkapi dengan fasilitas lengkap termasuk ruang periksa, laboratorium sederhana, apotek, dan ruang rawat inap. Setiap puskesmas akan didukung oleh 2 dokter umum, 3 perawat, dan 2 bidan yang akan bertugas secara bergiliran.

"Pembangunan puskesmas di desa terpencil ini merupakan upaya kami untuk memastikan seluruh masyarakat Pesisir Barat dapat mengakses layanan kesehatan yang berkualitas," ujar Kepala Dinas Kesehatan Pesisir Barat.

Program ini juga dilengkapi dengan pelatihan kader kesehatan desa dan program posyandu yang lebih intensif. Masyarakat desa juga akan dilatih untuk melakukan pertolongan pertama dan pencegahan penyakit menular.

Pembangunan puskesmas ini menggunakan dana dari APBD dan dukungan dari pemerintah pusat melalui program desa tertinggal. Setiap puskesmas dibangun dengan standar bangunan tahan gempa dan ramah lingkungan.

Dengan adanya puskesmas di desa terpencil, diharapkan dapat mengurangi angka kematian ibu dan bayi, meningkatkan cakupan imunisasi, dan memberikan layanan kesehatan dasar yang lebih baik bagi masyarakat. Program ini juga akan dilengkapi dengan sistem rujukan yang terintegrasi dengan rumah sakit kabupaten.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Kesehatan')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Inovasi Teknologi Pertanian di Pesisir Barat',
                'excerpt' => 'Petani di Pesisir Barat mulai menggunakan teknologi modern untuk meningkatkan produktivitas pertanian dan mengatasi tantangan iklim.',
                'content' => 'Petani di Kabupaten Pesisir Barat mulai mengadopsi teknologi modern dalam kegiatan pertanian mereka. Inovasi ini meliputi penggunaan sistem irigasi otomatis, sensor kelembaban tanah, dan aplikasi mobile untuk monitoring tanaman. Teknologi ini diharapkan dapat meningkatkan produktivitas pertanian sekaligus mengatasi tantangan perubahan iklim.

Program pengenalan teknologi pertanian ini dilakukan oleh Dinas Pertanian bekerja sama dengan perguruan tinggi dan perusahaan teknologi. Petani dilatih untuk menggunakan aplikasi mobile yang dapat memberikan informasi cuaca, jadwal tanam, dan rekomendasi pemupukan yang tepat.

"Teknologi ini sangat membantu kami dalam mengoptimalkan hasil panen dan mengurangi kerugian akibat cuaca ekstrem," kata salah satu petani yang telah menggunakan teknologi tersebut.

Selain itu, petani juga diperkenalkan dengan sistem hidroponik dan aquaponik untuk budidaya sayuran dan ikan. Sistem ini memungkinkan budidaya di lahan terbatas dan menghasilkan produk yang lebih berkualitas. Program ini juga dilengkapi dengan pelatihan pemasaran online untuk membantu petani menjual produk mereka secara langsung ke konsumen.

Dengan adanya inovasi teknologi ini, diharapkan sektor pertanian di Pesisir Barat dapat berkembang lebih pesat dan memberikan kontribusi yang lebih besar terhadap perekonomian daerah.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Teknologi')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Peningkatan Keamanan Maritim di Perairan Pesisir Barat',
                'excerpt' => 'Pemerintah meningkatkan keamanan maritim di perairan Pesisir Barat dengan penambahan patroli dan teknologi monitoring modern.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Dinas Perhubungan dan Keamanan Maritim meningkatkan keamanan di perairan daerah dengan penambahan patroli rutin dan teknologi monitoring modern. Program ini bertujuan untuk melindungi nelayan dan menjaga keamanan perairan dari aktivitas ilegal.

Penambahan patroli dilakukan dengan melibatkan TNI AL, Polisi Air, dan Satuan Polisi Pamong Praja. Patroli dilakukan secara rutin 24 jam dengan menggunakan kapal patroli yang dilengkapi dengan teknologi GPS dan komunikasi satelit. Selain itu, juga dibangun pos-pos pengamatan di sepanjang pantai untuk monitoring aktivitas di perairan.

"Keamanan maritim sangat penting untuk melindungi nelayan dan menjaga kedaulatan perairan kita," ujar Kepala Dinas Perhubungan Pesisir Barat.

Program ini juga meliputi pelatihan keamanan maritim bagi nelayan dan masyarakat pesisir. Mereka dilatih untuk mengenali tanda-tanda aktivitas mencurigakan dan cara melaporkan ke pihak berwenang. Selain itu, juga dibentuk kelompok nelayan yang bertugas sebagai mata-mata keamanan maritim.

Teknologi monitoring yang digunakan meliputi radar pantai, kamera CCTV, dan sistem komunikasi terintegrasi. Semua data dari sistem monitoring ini akan dikirim ke pusat komando keamanan maritim untuk dianalisis dan diambil tindakan yang diperlukan.

Dengan adanya peningkatan keamanan maritim ini, diharapkan nelayan dapat beraktivitas dengan aman dan perairan Pesisir Barat dapat terjaga dari berbagai ancaman keamanan.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Keamanan')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Pengembangan Wisata Bahari Berkelanjutan di Pesisir Barat',
                'excerpt' => 'Pemerintah mengembangkan wisata bahari berkelanjutan dengan fokus pada konservasi lingkungan dan pemberdayaan masyarakat lokal.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat mengembangkan program wisata bahari berkelanjutan yang fokus pada konservasi lingkungan dan pemberdayaan masyarakat lokal. Program ini bertujuan untuk mengembangkan pariwisata yang ramah lingkungan sekaligus meningkatkan kesejahteraan masyarakat pesisir.

Program wisata bahari berkelanjutan meliputi pengembangan ekowisata mangrove, wisata snorkeling terumbu karang, dan wisata memancing yang bertanggung jawab. Semua aktivitas wisata dirancang untuk tidak merusak ekosistem laut dan memberikan manfaat ekonomi bagi masyarakat lokal.

"Wisata berkelanjutan adalah kunci untuk menjaga keindahan alam Pesisir Barat untuk generasi mendatang," kata Kepala Dinas Pariwisata Pesisir Barat.

Masyarakat lokal dilatih untuk menjadi pemandu wisata, operator kapal wisata, dan pengelola homestay. Mereka juga diajarkan tentang konservasi lingkungan dan cara memberikan layanan wisata yang berkualitas. Program ini juga meliputi pembentukan koperasi wisata yang dikelola oleh masyarakat lokal.

Untuk mendukung program ini, pemerintah juga melakukan rehabilitasi terumbu karang dan penanaman mangrove di area yang telah rusak. Masyarakat lokal dilibatkan dalam kegiatan konservasi ini dan diberikan insentif ekonomi untuk menjaga kelestarian lingkungan.

Program wisata bahari berkelanjutan ini diharapkan dapat menarik wisatawan yang peduli lingkungan dan memberikan dampak positif bagi perekonomian masyarakat lokal tanpa merusak ekosistem laut.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Pariwisata')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Digitalisasi Layanan Publik di Pesisir Barat',
                'excerpt' => 'Pemerintah Kabupaten Pesisir Barat meluncurkan aplikasi mobile untuk mempermudah akses layanan publik bagi masyarakat.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat meluncurkan aplikasi mobile "Pesisir Barat Digital" untuk mempermudah akses layanan publik bagi masyarakat. Aplikasi ini memungkinkan masyarakat untuk mengakses berbagai layanan pemerintah secara online tanpa harus datang ke kantor.

Aplikasi "Pesisir Barat Digital" menyediakan berbagai fitur seperti pengajuan surat keterangan, pembayaran pajak, laporan pengaduan, dan informasi terkini tentang program pemerintah. Masyarakat juga dapat mengakses jadwal layanan, lokasi kantor, dan kontak petugas yang bertugas.

"Digitalisasi layanan publik adalah upaya kami untuk memberikan pelayanan yang lebih baik dan efisien kepada masyarakat," ujar Sekretaris Daerah Pesisir Barat.

Aplikasi ini dilengkapi dengan sistem keamanan yang ketat untuk melindungi data pribadi masyarakat. Semua transaksi dilakukan dengan enkripsi dan verifikasi identitas yang ketat. Masyarakat dapat mengakses aplikasi ini melalui smartphone dengan sistem operasi Android dan iOS.

Program digitalisasi ini juga meliputi pelatihan bagi petugas pemerintah untuk mengoperasikan sistem digital dan melayani masyarakat secara online. Selain itu, juga dibentuk tim customer service yang siap membantu masyarakat yang mengalami kesulitan menggunakan aplikasi.

Dengan adanya aplikasi digital ini, diharapkan dapat mengurangi antrian di kantor pemerintah dan memberikan kemudahan akses layanan publik bagi masyarakat Pesisir Barat.',
                'type' => 'berita',
                'category_id' => $categories->where('name', 'Teknologi')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Pemberdayaan Perempuan di Sektor Kelautan Pesisir Barat',
                'excerpt' => 'Program pemberdayaan perempuan di sektor kelautan memberikan pelatihan dan akses permodalan untuk meningkatkan peran perempuan dalam ekonomi maritim.',
                'content' => 'Pemerintah Kabupaten Pesisir Barat melalui Dinas Pemberdayaan Perempuan dan Perlindungan Anak meluncurkan program pemberdayaan perempuan di sektor kelautan. Program ini bertujuan untuk meningkatkan peran perempuan dalam ekonomi maritim dan memberikan kesempatan yang sama untuk berpartisipasi dalam pembangunan daerah.

Program pemberdayaan perempuan meliputi pelatihan pengolahan hasil laut, budidaya rumput laut, dan pengembangan produk olahan seafood. Perempuan dilatih untuk mengolah hasil tangkapan nelayan menjadi produk bernilai tambah tinggi seperti kerupuk ikan, abon ikan, dan berbagai olahan seafood lainnya.

"Perempuan memiliki peran penting dalam mengembangkan ekonomi maritim dan meningkatkan kesejahteraan keluarga nelayan," kata Kepala Dinas Pemberdayaan Perempuan Pesisir Barat.

Program ini juga memberikan akses permodalan melalui koperasi perempuan dan program kredit usaha rakyat (KUR) khusus perempuan. Mereka juga dilatih untuk mengelola keuangan, pemasaran, dan pengembangan usaha. Selain itu, juga dibentuk kelompok usaha bersama yang dikelola oleh perempuan.

Pelatihan yang diberikan meliputi teknik pengolahan yang higienis, pengemasan yang menarik, dan strategi pemasaran online. Perempuan juga dilatih untuk menggunakan teknologi digital dalam mengembangkan usaha mereka dan menjangkau pasar yang lebih luas.

Dengan adanya program pemberdayaan ini, diharapkan dapat meningkatkan kontribusi perempuan dalam perekonomian daerah dan memberikan kesempatan yang sama untuk berpartisipasi dalam pembangunan Pesisir Barat.',
                'type' => 'artikel',
                'category_id' => $categories->where('name', 'Sosial')->first()->id ?? $categories->first()->id,
            ]
        ];

        foreach ($pendingArticles as $index => $articleData) {
            // Get random penulis user
            $author = $penulisUsers->random();
            
            // Get random category if specific category not found
            if (!isset($articleData['category_id'])) {
                $articleData['category_id'] = $categories->random()->id;
            }

            // Create article with pending_review status
            $article = Article::create([
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'excerpt' => $articleData['excerpt'],
                'content' => $articleData['content'],
                'category_id' => $articleData['category_id'],
                'author_id' => $author->id,
                'status' => 'pending_review',
                'type' => $articleData['type'],
                'is_featured' => false,
                'is_breaking' => false,
                'views' => 0,
                'created_at' => Carbon::now()->subDays(rand(1, 7)), // Random date within last week
                'updated_at' => Carbon::now()->subDays(rand(1, 7)),
            ]);

            // Attach random tags
            $randomTags = $tags->random(rand(2, 4));
            $article->tags()->attach($randomTags->pluck('id'));

            $this->command->info("Created pending article: {$article->title} by {$author->name}");
        }

        $this->command->info('Successfully created 10 pending articles!');
    }
}
