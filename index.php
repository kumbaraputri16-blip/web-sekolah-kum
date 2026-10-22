<?php
require_once __DIR__ . '/config/koneksi.php';

$profil = $koneksi->query("
    SELECT * FROM profil_sekolah LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if (!$profil) {
    $profil = [];
}

$namaSekolah = $profil['nama_sekolah'] ?? 'SMK Infokom Kota Bogor';
/*
|--------------------------------------------------------------------------
| BERITA TERBARU
|--------------------------------------------------------------------------
*/

$beritaTerbaru = $koneksi->query("
    SELECT *
    FROM berita
    ORDER BY tanggal DESC, id DESC
    LIMIT 3
")->fetchAll();
$fotoPrestasi = $koneksi->query("
    SELECT *
    FROM foto_prestasi
    ORDER BY id DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| GALERI TERBARU
|--------------------------------------------------------------------------
*/

$galeriTerbaru = $koneksi->query("
    SELECT *
    FROM galeri
    ORDER BY tanggal DESC, id DESC
    LIMIT 6
")->fetchAll();

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<?php require_once __DIR__ . '/includes/navbar.php'; ?>


<!-- =========================================================
     HERO
========================================================= -->

<section class="hero hero-slider">

    <?php if (!empty($fotoPrestasi)): ?>

        <div class="hero-slider-foto">

            <?php foreach ($fotoPrestasi as $i => $foto): ?>

                <div class="hero-slide <?= $i === 0 ? 'aktif' : '' ?>">

                    <img
                        src="<?= BASE_URL ?>/uploads/prestasi/<?= rawurlencode(basename($foto['foto'])) ?>"
                        alt="<?= htmlspecialchars($foto['judul'], ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div class="hero-slide-overlay"></div>

                    <div class="container hero-slide-konten">

                        <span class="hero-kop">
                            WEBSITE RESMI SEKOLAH
                        </span>

                        <h1>
                            <?= htmlspecialchars($foto['judul'], ENT_QUOTES, 'UTF-8') ?>
                        </h1>

                        <p class="lead">
                            <?= htmlspecialchars(
                                $foto['deskripsi'] ?: 'Prestasi dan kegiatan siswa SMK Infokom Kota Bogor.',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <div class="hero-aksi">

                            <a
                                href="<?= BASE_URL ?>/profil.php"
                                class="tombol tombol-utama"
                            >
                                Kenali Sekolah →
                            </a>

                            <a
                                href="<?= BASE_URL ?>/ppdb.php"
                                class="tombol tombol-luar"
                            >
                                Informasi PPDB
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="container">

            <span class="hero-kop">WEBSITE RESMI SEKOLAH</span>

            <h1>
                Selamat Datang di<br>
                <?= htmlspecialchars($namaSekolah) ?>
            </h1>

            <p class="lead">
                Sekolah kejuruan yang membekali peserta didik
                dengan kompetensi, karakter, dan kesiapan
                menghadapi dunia kerja serta perkembangan teknologi.
            </p>

            <div class="hero-aksi">

                <a href="<?= BASE_URL ?>/profil.php" class="tombol tombol-utama">
                    Kenali Sekolah →
                </a>

                <a href="<?= BASE_URL ?>/ppdb.php" class="tombol tombol-luar">
                    Informasi PPDB
                </a>

            </div>

        </div>

    <?php endif; ?>

</section>

<?php if (!empty($fotoPrestasi) && count($fotoPrestasi) > 1): ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.hero-slide');

    if (slides.length < 2) return;

    let index = 0;

    setInterval(function () {
    slides[index].classList.remove('aktif');

    index = (index + 1) % slides.length;

    slides[index].classList.add('aktif');
}, 5000);
});
</script>

<?php endif; ?>

<!-- =========================================================
     STATISTIK
========================================================= -->

<section class="container">

    <div class="statistik statistik-baru">

        <!-- KOTAK 1: AKREDITASI -->
        <div class="item kartu-statistik kartu-akreditasi">
            <div class="statistik-ikon">🏫</div>
            <span class="angka akreditasi-huruf">A</span>
            <span class="label">Akreditasi Sekolah</span>
            <span class="label">SMK Infokom Kota Bogor</span>
        </div>

        <!-- KOTAK 2: JUMLAH SISWA -->
<div class="item kartu-statistik kartu-siswa">
    <div class="statistik-ikon">🎓</div>
    <span class="angka angka-hitung" data-target="1490">0</span>
    <span class="label">Siswa Aktif</span>
    <span class="label">Peserta didik sekolah</span>
</div>

        <!-- KOTAK 3: PROGRAM KEAHLIAN -->
        <div class="item kartu-statistik kartu-jurusan">
            <div class="statistik-ikon">💻</div>
            <span class="angka angka-hitung" data-target="4">0</span>
            <span class="label">Program Keahlian</span>
        </div>

        <!-- KOTAK 4: PRESTASI -->
        <a href="<?= BASE_URL ?>/prestasi.php"
           class="item kartu-statistik kartu-prestasi">
            <div class="statistik-ikon">🏆</div>
            <span class="angka">Prestasi</span>
            <span class="label">Lihat Foto & Kegiatan →</span>
        </a>

    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.angka-hitung').forEach(function (elemen) {
        const target = Number(elemen.dataset.target);

        if (!Number.isFinite(target) || target <= 0) return;

        const durasi = 1200;
        const mulai = performance.now();

        function animasi(waktu) {
            const progres = Math.min((waktu - mulai) / durasi, 1);
            const nilai = Math.floor(progres * target);

            elemen.textContent = nilai.toLocaleString('id-ID');

            if (progres < 1) {
                requestAnimationFrame(animasi);
            } else {
                elemen.textContent = target.toLocaleString('id-ID');
            }
        }

        requestAnimationFrame(animasi);
    });
});
</script>



<!-- =========================================================
     TENTANG SEKOLAH
========================================================= -->

<!-- TENTANG SEKOLAH -->
<section class="bagian tentang-sekolah">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">PROFIL SEKOLAH</span>

            <h2>
                Mengenal <?= htmlspecialchars($namaSekolah) ?>
            </h2>

            <p>
                <?= nl2br(htmlspecialchars(
                    $profil['sejarah'] ?? 'Sejarah sekolah belum tersedia.'
                )) ?>
            </p>

            <a
                href="<?= BASE_URL ?>/profil.php"
                class="tombol tombol-utama tombol-kecil"
            >
                Selengkapnya →
            </a>

        </div>

        <!-- SAMBUTAN KEPALA SEKOLAH -->
<section class="bagian sambutan-section">
    <div class="container">

        <div class="sambutan-kepala">

            <div class="isi-sambutan">

                <span class="kop">SAMBUTAN KEPALA SEKOLAH</span>

                <h2>Selamat Datang di Website Sekolah Kami</h2>

                <p>
                    Selamat datang di website resmi
                    <?= htmlspecialchars($namaSekolah) ?>.
                    Website ini menjadi sarana informasi mengenai
                    profil sekolah, program pendidikan, kegiatan,
                    prestasi, serta berbagai informasi lainnya.
                </p>

                <p>
                    Kami berharap website ini dapat membantu siswa,
                    orang tua, alumni, dan masyarakat untuk mengenal
                    lingkungan serta kegiatan sekolah dengan lebih baik.
                </p>

                <div class="identitas-kepala">
                    <strong>Kepala Sekolah</strong>

                    <p>
                        <?= htmlspecialchars(
                            $profil['nama_kepala_sekolah']
                            ?? 'Ir. Hj. Liliek Rahmaningsih, M.MPd'
                        ) ?>
                    </p>
                </div>

            </div>

            <div class="foto-kepala">
    <img
        src="<?= BASE_URL ?>/assets/images/kepala-sekolah.jpeg"
        alt="Foto Kepala Sekolah"
    >
</div>

        </div>

    </div>
</section>


<!-- =========================================================
     JURUSAN
========================================================= -->

<section class="bagian bagian-jurusan">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">PROGRAM KEAHLIAN</span>

            <h2>Kenali Jurusan Kami</h2>

            <p>
                Pilih program keahlian sesuai minat, bakat, dan potensi
                untuk mempersiapkan masa depan di dunia pendidikan maupun kerja.
            </p>

        </div>

        <div class="grid-jurusan">

            <!-- DKV -->
            <article class="kartu-jurusan">

                <div class="foto-jurusan">
                    <img
                        src="<?= BASE_URL ?>/assets/images/dkv.png"
                        alt="Desain Komunikasi Visual"
                    >
                </div>

                <div class="isi-jurusan">

                    <span class="label-jurusan">
                        PROGRAM KEAHLIAN
                    </span>

                    <h3>Desain Komunikasi Visual</h3>

                    <p>
                        Mempelajari desain grafis, multimedia,
                        komunikasi visual, fotografi, videografi,
                        dan kreativitas digital.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/jurusan.php"
                        class="link-jurusan"
                    >
                        Lihat Jurusan →
                    </a>

                </div>

            </article>


            <!-- PSPT -->
            <article class="kartu-jurusan">

                <div class="foto-jurusan">
                    <img
                        src="<?= BASE_URL ?>/assets/images/pspt.png"
                        alt="Produksi dan Siaran Program Tele"
                    >
                </div>

                <div class="isi-jurusan">

                    <span class="label-jurusan">
                        PROGRAM KEAHLIAN
                    </span>

                    <h3>Produksi dan Siaran Program Tele</h3>

                    <p>
                        Mempelajari produksi video, penyiaran,
                        pengambilan gambar, tata suara,
                        dan proses editing.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/jurusan.php"
                        class="link-jurusan"
                    >
                        Lihat Jurusan →
                    </a>

                </div>

            </article>


            <!-- RPL -->
            <article class="kartu-jurusan">

                <div class="foto-jurusan">
                    <img
                        src="<?= BASE_URL ?>/assets/images/rpl.png"
                        alt="Rekayasa Perangkat Lunak"
                    >
                </div>

                <div class="isi-jurusan">

                    <span class="label-jurusan">
                        PROGRAM KEAHLIAN
                    </span>

                    <h3>Rekayasa Perangkat Lunak</h3>

                    <p>
                        Mempelajari pemrograman, pembuatan website,
                        aplikasi, database, dan pengembangan sistem.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/jurusan.php"
                        class="link-jurusan"
                    >
                        Lihat Jurusan →
                    </a>

                </div>

            </article>


            <!-- TKJ -->
            <article class="kartu-jurusan">

                <div class="foto-jurusan">
                    <img
                        src="<?= BASE_URL ?>/assets/images/tkj.png"
                        alt="Teknik Komputer dan Jaringan"
                    >
                </div>

                <div class="isi-jurusan">

                    <span class="label-jurusan">
                        PROGRAM KEAHLIAN
                    </span>

                    <h3>Teknik Komputer dan Jaringan</h3>

                    <p>
                        Mempelajari perakitan komputer, sistem operasi,
                        jaringan komputer, konfigurasi perangkat,
                        dan troubleshooting.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/jurusan.php"
                        class="link-jurusan"
                    >
                        Lihat Jurusan →
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>


<!-- =========================================================
     PROGRAM UNGGULAN
========================================================= -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                PROGRAM SEKOLAH
            </span>

            <h2>
                Program Unggulan
            </h2>

            <p>
                Kegiatan dan layanan yang mendukung perkembangan
                kompetensi serta karakter peserta didik.
            </p>

        </div>


        <div class="grid-3">


            <article class="kartu">

                <div
                    class="foto"
                    style="display:flex;align-items:center;justify-content:center;font-size:3.5rem;background:var(--kertas-gelap);"
                >
                    🏢
                </div>

                <div class="isi">

                    <h3>
                        Praktik Kerja Lapangan
                    </h3>

                    <p>
                        Memberikan pengalaman kepada siswa
                        melalui praktik langsung di dunia kerja.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/program.php"
                        class="tautan"
                    >
                        Selengkapnya →
                    </a>

                </div>

            </article>


            <article class="kartu">

                <div
                    class="foto"
                    style="display:flex;align-items:center;justify-content:center;font-size:3.5rem;background:var(--kertas-gelap);"
                >
                    💼
                </div>

                <div class="isi">

                    <h3>
                        Bursa Kerja Khusus
                    </h3>

                    <p>
                        Mendukung siswa dan alumni memperoleh
                        informasi serta peluang kerja.
                    </p>

                   <a
    href="<?= BASE_URL ?>/program.php"
    class="tautan"
>
    Selengkapnya →
</a>

                </div>

            </article>


            <article class="kartu">

                <div
                    class="foto"
                    style="display:flex;align-items:center;justify-content:center;font-size:3.5rem;background:var(--kertas-gelap);"
                >
                    🏆
                </div>

                <div class="isi">

                    <h3>
                        Ekstrakurikuler
                    </h3>

                    <p>
                        Mengembangkan bakat, minat, kreativitas,
                        kepemimpinan, dan kerja sama siswa.
                    </p>

                    <a
                        href="<?= BASE_URL ?>/ekskul.php"
                        class="tautan"
                    >
                        Selengkapnya →
                    </a>

                </div>

            </article>


        </div>

    </div>

</section>


<!-- =========================================================
     BERITA TERBARU
========================================================= -->

<section class="bagian bagian-kertas-gelap">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                INFORMASI TERBARU
            </span>

            <h2>
                Berita Sekolah
            </h2>

            <p>
                Informasi dan kabar terbaru dari sekolah.
            </p>

        </div>


        <?php if (!empty($beritaTerbaru)): ?>

            <div class="grid-3">

                <?php foreach ($beritaTerbaru as $berita): ?>

                    <article class="kartu">

                        <?php if (!empty($berita['foto'])): ?>

                            <div
                                class="foto"
                                style="
                                    background-image:url('<?= BASE_URL ?>/uploads/berita/<?= htmlspecialchars($berita['foto']) ?>');
                                "
                            ></div>

                        <?php else: ?>

                            <div
                                class="foto"
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:3rem;
                                "
                            >
                                📰
                            </div>

                        <?php endif; ?>


                        <div class="isi">

                            <span class="tanggal">
                                <?= date(
                                    'd M Y',
                                    strtotime($berita['tanggal'])
                                ) ?>
                            </span>

                            <h3>
                                <?= htmlspecialchars($berita['judul']) ?>
                            </h3>

                            <p>

                                <?= htmlspecialchars(
                                    mb_substr(
                                        strip_tags(
                                            $berita['ringkasan']
                                            ?? $berita['isi']
                                        ),
                                        0,
                                        130
                                    )
                                ) ?>

                                ...

                            </p>

                            <a
                                href="<?= BASE_URL ?>/berita_detail.php?slug=<?= urlencode($berita['slug']) ?>"
                                class="tautan"
                            >
                                Baca Selengkapnya →
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="kotak-info">
                <p>
                    Belum ada berita sekolah.
                </p>
            </div>

        <?php endif; ?>


    </div>

</section>


<!-- =========================================================
     GALERI
========================================================= -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                KEGIATAN SEKOLAH
            </span>

            <h2>
                Galeri Terbaru
            </h2>

            <p>
                Dokumentasi kegiatan dan aktivitas
                <?= htmlspecialchars($namaSekolah) ?>.
            </p>

        </div>


        <?php if (!empty($galeriTerbaru)): ?>

            <div class="grid-galeri">

                <?php foreach ($galeriTerbaru as $foto): ?>

                    <div
                        class="item-galeri"
                        style="
                            background-image:url('<?= BASE_URL ?>/uploads/galeri/<?= htmlspecialchars($foto['foto']) ?>');
                        "
                    >

                        <div class="keterangan">

                            <?= htmlspecialchars(
                                $foto['judul']
                            ) ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="kotak-info">

                <p>
                    Belum ada foto galeri.
                </p>

            </div>

        <?php endif; ?>


        <div style="margin-top:28px;">

            <a
                href="<?= BASE_URL ?>/galeri.php"
                class="tombol tombol-utama tombol-kecil"
            >
                Lihat Semua Galeri →
            </a>

        </div>

    </div>

</section>


<!-- =========================================================
     PPDB
========================================================= -->

<section class="hero hero-ppdb">
    <div class="container">

        <span class="hero-kop">
            PENERIMAAN PESERTA DIDIK BARU
        </span>

        <h1>
            Saatnya Menjadi kami
        </h1>

        <p class="lead">
            Dapatkan informasi jurusan, persyaratan,
            dan alur pendaftaran peserta didik baru.
        </p>

        <div class="hero-aksi">

            <a
                href="<?= BASE_URL ?>/ppdb.php"
                class="tombol tombol-utama"
            >
                Lihat Informasi PPDB →
            </a>

            <a
                href="<?= BASE_URL ?>/kontak.php"
                class="tombol tombol-luar"
            >
                Hubungi Sekolah
            </a>

        </div>

    </div>

</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
