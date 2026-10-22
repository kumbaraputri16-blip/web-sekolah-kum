<?php

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Berita';

$semuaBerita = $koneksi->query("
    SELECT *
    FROM berita
    ORDER BY tanggal DESC, id DESC
")->fetchAll();

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<section class="header-halaman">
    <div class="container">
        <h1>Berita & Pengumuman</h1>
        <p>Informasi terbaru seputar kegiatan dan pengumuman resmi sekolah.</p>
    </div>
</section>

<section class="bagian">
    <div class="container">

        <div class="judul-bagian">
            <span class="kop">INFORMASI TERBARU</span>
            <h2>Berita Sekolah</h2>
            <p>
                Ikuti informasi, kegiatan, dan pengumuman terbaru
                dari sekolah.
            </p>
        </div>

        <?php if (!empty($semuaBerita)): ?>

            <div class="grid-3">

                <?php foreach ($semuaBerita as $b): ?>

                    <a
                        href="<?= BASE_URL ?>/berita_detail.php?slug=<?= urlencode($b['slug']) ?>"
                        class="kartu"
                    >

                        <?php if (!empty($b['foto'])): ?>

                            <div
                                class="foto"
                                style="background-image:url('<?= BASE_URL ?>/uploads/berita/<?= htmlspecialchars($b['foto']) ?>');"
                            ></div>

                        <?php else: ?>

                            <div
                                class="foto"
                                style="display:flex;align-items:center;justify-content:center;background:var(--papan);color:var(--kertas);font-family:var(--font-judul);font-size:2rem;"
                            >
                                BERITA
                            </div>

                        <?php endif; ?>

                        <div class="isi">

                            <span class="tanggal">
                                <?= date('d F Y', strtotime($b['tanggal'])) ?>
                            </span>

                            <h3>
                                <?= htmlspecialchars($b['judul']) ?>
                            </h3>

                            <?php if (!empty($b['penulis'])): ?>
                                <small style="display:block;margin-bottom:12px;color:var(--teks-muda);">
                                    Oleh <?= htmlspecialchars($b['penulis']) ?>
                                </small>
                            <?php endif; ?>

                            <p>
                                <?= htmlspecialchars(
                                    $b['ringkasan']
                                    ?: mb_strimwidth(
                                        strip_tags($b['isi']),
                                        0,
                                        110,
                                        '...'
                                    )
                                ) ?>
                            </p>

                            <span class="tautan">
                                Baca selengkapnya &rarr;
                            </span>

                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="kotak-info">
                <h3>Belum Ada Berita</h3>
                <p>
                    Belum ada berita atau pengumuman yang dipublikasikan.
                    Silakan tambahkan melalui panel admin.
                </p>
            </div>

        <?php endif; ?>

    </div>
</section>

<section class="bagian bagian-kertas-gelap">
    <div class="container">

        <div class="judul-bagian">
            <span class="kop">TETAP TERHUBUNG</span>

            <h2>Butuh Informasi Lebih Lanjut?</h2>

            <p>
                Hubungi sekolah jika membutuhkan informasi mengenai
                kegiatan, program, maupun layanan sekolah.
            </p>

            <a
                href="<?= BASE_URL ?>/kontak.php"
                class="tombol tombol-utama"
            >
                Hubungi Sekolah →
            </a>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>