<?php

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Galeri';

$semuaGaleri = $koneksi->query("
    SELECT *
    FROM galeri
    ORDER BY dibuat_pada DESC
")->fetchAll();

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<section class="header-halaman">
    <div class="container">
        <h1>Galeri Kegiatan</h1>
        <p>Kumpulan dokumentasi kegiatan belajar, acara, dan aktivitas sekolah.</p>
    </div>
</section>

<section class="bagian">
    <div class="container">

        <div class="judul-bagian">
            <span class="kop">DOKUMENTASI SEKOLAH</span>
            <h2>Momen & Kegiatan Sekolah</h2>
            <p>
                Lihat berbagai kegiatan dan momen yang menjadi bagian
                dari perjalanan sekolah.
            </p>
        </div>

        <?php if (!empty($semuaGaleri)): ?>

            <div class="grid-galeri">

                <?php foreach ($semuaGaleri as $g): ?>

                    <?php
                    $foto = BASE_URL . '/uploads/galeri/' . htmlspecialchars($g['foto']);
                    ?>

                    <a
                        href="<?= $foto ?>"
                        target="_blank"
                        rel="noopener"
                        class="item-galeri"
                        style="background-image:url('<?= $foto ?>');"
                    >
                        <div class="keterangan">

                            <strong>
                                <?= htmlspecialchars($g['judul']) ?>
                            </strong>

                            <?php if (!empty($g['tanggal'])): ?>
                                <small>
                                    <?= date('d F Y', strtotime($g['tanggal'])) ?>
                                </small>
                            <?php endif; ?>

                            <?php if (!empty($g['deskripsi'])): ?>
                                <span>
                                    <?= htmlspecialchars($g['deskripsi']) ?>
                                </span>
                            <?php endif; ?>

                        </div>
                    </a>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="kotak-info">
                <h3>Galeri Belum Tersedia</h3>
                <p>
                    Belum ada foto kegiatan yang ditampilkan.
                    Silakan tambahkan dokumentasi melalui panel admin.
                </p>
            </div>

        <?php endif; ?>

    </div>
</section>

<section class="bagian bagian-kertas-gelap">
    <div class="container">

        <div class="judul-bagian">
            <span class="kop">KEGIATAN SEKOLAH</span>

            <h2>Aktif Berproses, Aktif Berkarya</h2>

            <p>
                Setiap kegiatan menjadi bagian dari pengalaman belajar
                dan perkembangan siswa di sekolah.
            </p>

            <a
                href="<?= BASE_URL ?>/berita.php"
                class="tombol tombol-utama"
            >
                Lihat Berita Sekolah →
            </a>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>