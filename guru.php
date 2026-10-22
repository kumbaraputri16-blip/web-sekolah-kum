<?php

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Guru & Staf';

$daftarGuru = $koneksi->query("
    SELECT *
    FROM guru
    ORDER BY
        CASE
            WHEN jabatan = 'Kepala Sekolah' THEN 0
            ELSE 1
        END,
        nama ASC
")->fetchAll();

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>


<!-- HEADER -->

<section class="header-halaman">

    <div class="container">

        <h1>
            Guru & Staf
        </h1>

        <p>
            Tenaga pendidik dan kependidikan yang
            mendampingi proses belajar di sekolah.
        </p>

    </div>

</section>


<!-- DAFTAR GURU -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                TENAGA PENDIDIK
            </span>

            <h2>
                Guru & Staf Sekolah
            </h2>

            <p>
                Kenali tenaga pendidik dan kependidikan
                yang mendukung kegiatan belajar di sekolah.
            </p>

        </div>


        <?php if (!empty($daftarGuru)): ?>

            <div class="grid-3">

                <?php foreach ($daftarGuru as $g): ?>

                    <article class="kartu kartu-guru">

                        <?php if (!empty($g['foto'])): ?>

                            <div
                                class="foto"
                                style="
                                    background-image:
                                    url('<?= BASE_URL ?>/uploads/guru/<?= htmlspecialchars($g['foto']) ?>');
                                "
                            ></div>

                        <?php else: ?>

                            <div
                                class="foto"
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-family:var(--font-judul);
                                    font-size:2.4rem;
                                    color:var(--papan);
                                "
                            >
                                <?= htmlspecialchars(
                                    mb_strtoupper(
                                        mb_substr($g['nama'], 0, 1)
                                    )
                                ) ?>
                            </div>

                        <?php endif; ?>


                        <h3>
                            <?= htmlspecialchars($g['nama']) ?>
                        </h3>


                        <div class="jabatan">
                            <?= htmlspecialchars($g['jabatan']) ?>
                        </div>


                        <?php if (!empty($g['mata_pelajaran'])): ?>

                            <div class="mapel">
                                Mengajar
                                <?= htmlspecialchars($g['mata_pelajaran']) ?>
                            </div>

                        <?php endif; ?>


                        <?php if (!empty($g['nip'])): ?>

                            <div
                                style="
                                    margin-top:10px;
                                    font-size:0.78rem;
                                    color:var(--teks-muda);
                                "
                            >
                                NIP:
                                <?= htmlspecialchars($g['nip']) ?>
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
                    Data guru dan staf belum tersedia.
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
                lebih lanjut mengenai kegiatan dan layanan sekolah.
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