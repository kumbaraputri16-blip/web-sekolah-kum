<?php

require_once __DIR__ . '/config/koneksi.php';

$slug = trim($_GET['slug'] ?? '');

$stmt = $koneksi->prepare("
    SELECT *
    FROM berita
    WHERE slug = ?
    LIMIT 1
");

$stmt->execute([$slug]);
$berita = $stmt->fetch();

if (!$berita) {

    $judul_halaman = 'Berita Tidak Ditemukan';

    require_once __DIR__ . '/includes/header.php';
    require_once __DIR__ . '/includes/navbar.php';
    ?>

    <section class="header-halaman">
        <div class="container">
            <h1>Berita Tidak Ditemukan</h1>
            <p>Artikel yang Anda cari tidak tersedia.</p>
        </div>
    </section>

    <section class="bagian">
        <div class="container">

            <div class="kotak-info">
                <h3>Artikel Tidak Ditemukan</h3>

                <p>
                    Artikel yang Anda cari mungkin sudah dihapus,
                    belum dipublikasikan, atau alamat yang digunakan salah.
                </p>

                <a
                    href="<?= BASE_URL ?>/berita.php"
                    class="tombol tombol-utama"
                >
                    ← Kembali ke Berita
                </a>
            </div>

        </div>
    </section>

    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$judul_halaman = $berita['judul'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

?>

<section class="header-halaman">
    <div class="container">

        <span class="kop">BERITA SEKOLAH</span>

        <h1>
            <?= htmlspecialchars($berita['judul']) ?>
        </h1>

        <p>
            Dipublikasikan
            <?= date('d F Y', strtotime($berita['tanggal'])) ?>

            <?php if (!empty($berita['penulis'])): ?>
                · oleh <?= htmlspecialchars($berita['penulis']) ?>
            <?php endif; ?>
        </p>

    </div>
</section>

<section class="bagian">
    <div class="container">

        <article class="isi-artikel">

            <?php if (!empty($berita['foto'])): ?>

                <img
                    src="<?= BASE_URL ?>/uploads/berita/<?= htmlspecialchars($berita['foto']) ?>"
                    alt="<?= htmlspecialchars($berita['judul']) ?>"
                >

            <?php endif; ?>

            <?php if (!empty($berita['ringkasan'])): ?>

                <p style="font-size:1.1rem;font-weight:600;color:var(--teks-muda);">
                    <?= htmlspecialchars($berita['ringkasan']) ?>
                </p>

            <?php endif; ?>

            <div>
                <?= nl2br(htmlspecialchars($berita['isi'])) ?>
            </div>

            <hr class="garis-pemisah">

            <a
                href="<?= BASE_URL ?>/berita.php"
                class="tombol tombol-utama tombol-kecil"
            >
                ← Kembali ke Daftar Berita
            </a>

        </article>

    </div>
</section>

<section class="bagian bagian-kertas-gelap">
    <div class="container">

        <div class="judul-bagian">

            <span class="kop">INFORMASI LAINNYA</span>

            <h2>Jangan Lewatkan Berita Sekolah</h2>

            <p>
                Lihat berita dan informasi terbaru lainnya
                seputar kegiatan sekolah.
            </p>

            <a
                href="<?= BASE_URL ?>/berita.php"
                class="tombol tombol-utama"
            >
                Lihat Semua Berita →
            </a>

        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>