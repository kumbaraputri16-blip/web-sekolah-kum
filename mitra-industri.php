<?php

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Mitra Industri';

$daftarMitra = $koneksi->query("
    SELECT *
    FROM mitra_industri
    ORDER BY nama_perusahaan ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>


<!-- HEADER -->

<section class="header-halaman">

    <div class="container">

        <h1>
            Mitra Industri
        </h1>

        <p>
            Perusahaan dan mitra industri yang mendukung
            pengembangan kompetensi serta kesiapan siswa
            SMK Infokom Kota Bogor menghadapi dunia kerja.
        </p>

    </div>

</section>


<!-- DAFTAR MITRA -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                KERJA SAMA INDUSTRI
            </span>

            <h2>
                Perusahaan Mitra Sekolah
            </h2>

            <p>
                Mengenal perusahaan yang menjalin kerja sama
                dengan SMK Infokom Kota Bogor.
            </p>

        </div>


        <?php if (!empty($daftarMitra)): ?>

            <div class="grid-3">

                <?php foreach ($daftarMitra as $m): ?>

                    <article class="kartu kartu-mitra">

                        <?php if (!empty($m['logo'])): ?>

                            <div
                                class="foto foto-mitra"
                                style="
                                    background-image:
                                    url('<?= BASE_URL ?>/unggah/mitra/<?= rawurlencode(basename($m['logo'])) ?>');
                                "
                                role="img"
                                aria-label="Logo <?= htmlspecialchars($m['nama_perusahaan']) ?>"
                            ></div>

                        <?php else: ?>

                            <div
                                class="foto foto-mitra"
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-family:var(--font-judul);
                                    font-size:2.8rem;
                                    color:var(--papan);
                                    background:#ffffff;
                                "
                            >
                                🏢
                            </div>

                        <?php endif; ?>


                        <h3>
                            <?= htmlspecialchars($m['nama_perusahaan']) ?>
                        </h3>


                        <?php if (!empty($m['deskripsi'])): ?>

                            <div class="deskripsi-mitra">
                                <?= nl2br(htmlspecialchars($m['deskripsi'])) ?>
                            </div>

                        <?php else: ?>

                            <div class="deskripsi-mitra">
                                Mitra industri SMK Infokom Kota Bogor.
                            </div>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="kotak-info">

                <h3>
                    Data Belum Tersedia
                </h3>

                <p>
                    Data perusahaan mitra industri belum tersedia.
                    Informasi akan ditampilkan setelah ditambahkan
                    melalui panel admin.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- CTA -->

<section class="bagian bagian-kertas-gelap">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                INFORMASI SEKOLAH
            </span>

            <h2>
                Ingin Mengetahui Lebih Banyak?
            </h2>

            <p>
                Hubungi sekolah untuk mendapatkan informasi
                lebih lanjut mengenai kerja sama industri
                dan program sekolah.
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
