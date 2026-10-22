<?php

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Jurusan';

$jurusan = [

    [
        'kode' => 'DKV',
        'nama' => 'Desain Komunikasi Visual',
        'gambar' => 'dkv.png',
        'deskripsi' => 'Program keahlian yang membekali siswa dengan kemampuan dalam desain grafis, ilustrasi, fotografi, videografi, branding, dan komunikasi visual digital.',
        'kompetensi' => [
            'Desain grafis',
            'Ilustrasi digital',
            'Fotografi',
            'Videografi',
            'Branding dan komunikasi visual'
        ],
        'prospek' => 'Desainer grafis, ilustrator, fotografer, videografer, content creator, dan bidang industri kreatif lainnya.'
    ],

    [
        'kode' => 'PSPT',
        'nama' => 'Produksi dan Siaran Program Tele',
        'gambar' => 'pspt.png',
        'deskripsi' => 'Program keahlian yang berfokus pada produksi konten audiovisual, penyiaran, pengambilan gambar, tata suara, hingga proses editing.',
        'kompetensi' => [
            'Produksi video',
            'Teknik pengambilan gambar',
            'Editing video',
            'Penyiaran',
            'Produksi konten audiovisual'
        ],
        'prospek' => 'Videografer, editor video, kamerawan, production crew, penyiar, dan bidang produksi media.'
    ],

    [
        'kode' => 'RPL',
        'nama' => 'Rekayasa Perangkat Lunak',
        'gambar' => 'rpl.png',
        'deskripsi' => 'Program keahlian yang mempelajari proses pembuatan perangkat lunak mulai dari pemrograman, website, aplikasi, database, hingga pengembangan sistem.',
        'kompetensi' => [
            'Pemrograman',
            'Pembuatan website',
            'Pengembangan aplikasi',
            'Database',
            'Analisis dan pengembangan sistem'
        ],
        'prospek' => 'Web developer, software developer, programmer, database administrator, dan bidang teknologi informasi.'
    ],

    [
        'kode' => 'TKJ',
        'nama' => 'Teknik Komputer dan Jaringan',
        'gambar' => 'tkj.png',
        'deskripsi' => 'Program keahlian yang membekali siswa dengan keterampilan komputer, jaringan, instalasi perangkat, konfigurasi jaringan, dan troubleshooting.',
        'kompetensi' => [
            'Perakitan komputer',
            'Instalasi sistem operasi',
            'Jaringan komputer',
            'Konfigurasi perangkat jaringan',
            'Troubleshooting'
        ],
        'prospek' => 'Teknisi komputer, network administrator, teknisi jaringan, IT support, dan bidang teknologi jaringan.'
    ]

];

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>


<!-- =========================================================
     HEADER HALAMAN
========================================================= -->

<section class="header-halaman">

    <div class="container">

        <span class="kop">
            PROGRAM KEAHLIAN
        </span>

        <h1>
            Jurusan di <?= htmlspecialchars($namaSekolah) ?>
        </h1>

        <p>
            Kenali program keahlian dan bidang kompetensi yang dapat menjadi
            bekal untuk melanjutkan pendidikan maupun memasuki dunia kerja.
        </p>

    </div>

</section>


<!-- =========================================================
     DAFTAR JURUSAN
========================================================= -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">
                PILIHAN PROGRAM KEAHLIAN
            </span>

            <h2>
                Belajar sesuai minat dan potensi
            </h2>

            <p>
                Setiap program keahlian memiliki karakteristik dan
                kompetensi yang berbeda.
            </p>

        </div>


        <div class="daftar-jurusan">


            <?php foreach ($jurusan as $index => $j): ?>

                <article class="detail-jurusan">


                    <!-- =================================================
                         FOTO JURUSAN
                    ================================================== -->

                    <div class="gambar-jurusan">

                        <img
                            src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($j['gambar']) ?>"
                            alt="<?= htmlspecialchars($j['nama']) ?>"
                        >

                    </div>


                    <!-- =================================================
                         NOMOR
                    ================================================== -->

                    <div class="nomor-jurusan">

                        <?= str_pad(
                            $index + 1,
                            2,
                            '0',
                            STR_PAD_LEFT
                        ) ?>

                    </div>


                    <!-- =================================================
                         ISI JURUSAN
                    ================================================== -->

                    <div class="isi-jurusan">


                        <div class="judul-jurusan">

                            <span class="kode-jurusan">

                                <?= htmlspecialchars($j['kode']) ?>

                            </span>

                            <h2>

                                <?= htmlspecialchars($j['nama']) ?>

                            </h2>

                        </div>


                        <p class="deskripsi-jurusan">

                            <?= htmlspecialchars($j['deskripsi']) ?>

                        </p>


                        <div class="detail-grid">


                            <!-- ================================
                                 KOMPETENSI
                            ================================= -->

                            <div>

                                <h3>
                                    Kompetensi yang Dipelajari
                                </h3>


                                <ul class="daftar-kompetensi">

                                    <?php foreach ($j['kompetensi'] as $kompetensi): ?>

                                        <li>

                                            <span>✓</span>

                                            <?= htmlspecialchars($kompetensi) ?>

                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                            </div>


                            <!-- ================================
                                 PROSPEK
                            ================================= -->

                            <div class="prospek-jurusan">

                                <h3>
                                    Prospek Bidang
                                </h3>

                                <p>

                                    <?= htmlspecialchars($j['prospek']) ?>

                                </p>

                            </div>


                        </div>

                    </div>

                </article>

            <?php endforeach; ?>


        </div>

    </div>

</section>


<!-- =========================================================
     CTA PPDB
========================================================= -->

<section class="bagian">

    <div class="container">

        <div class="kotak-ajakan">

            <div>

                <span class="kop">
                    TEMUKAN PILIHANMU
                </span>

                <h2>
                    Siap menentukan jurusan?
                </h2>

                <p>
                    Kenali lebih jauh program sekolah dan informasi
                    penerimaan peserta didik baru.
                </p>

            </div>


            <div class="aksi-ajakan">

                <a
                    href="<?= BASE_URL ?>/ppdb.php"
                    class="tombol tombol-utama"
                >
                    Informasi PPDB →
                </a>

                <a
                    href="<?= BASE_URL ?>/kontak.php"
                    class="tombol tombol-gelap"
                >
                    Hubungi Sekolah
                </a>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     STYLE KHUSUS HALAMAN JURUSAN
========================================================= -->

<style>

.daftar-jurusan {

    display: flex;

    flex-direction: column;

    gap: 28px;

}


/* =========================================================
   KARTU JURUSAN
========================================================= */

.detail-jurusan {

    display: grid;

    grid-template-columns: 70px 1fr;

    gap: 28px;

    padding: 32px;

    background: var(--putih);

    border: 1px solid var(--garis);

    border-radius: 20px;

    box-shadow:
        0 8px 28px rgba(28, 43, 57, 0.06);

    overflow: hidden;

}


/* =========================================================
   GAMBAR JURUSAN
========================================================= */

.gambar-jurusan {

    grid-column: 1 / -1;

    width: 100%;

    height: 300px;

    border-radius: 16px;

    overflow: hidden;

    background: var(--kertas-gelap);

    display: flex;

    align-items: center;

    justify-content: center;

}


.gambar-jurusan img {

    width: 100%;

    height: 100%;

    object-fit: contain;

    display: block;

}


/* =========================================================
   NOMOR
========================================================= */

.nomor-jurusan {

    font-family: var(--font-judul);

    font-size: 28px;

    font-weight: 700;

    color: var(--kapur);

    padding-top: 4px;

}


/* =========================================================
   JUDUL
========================================================= */

.judul-jurusan {

    margin-bottom: 18px;

}


.kode-jurusan {

    display: inline-block;

    padding: 6px 12px;

    margin-bottom: 10px;

    border-radius: 999px;

    background: #f5ead4;

    color: var(--papan);

    font-size: 0.78rem;

    font-weight: 800;

    letter-spacing: 1px;

}


.judul-jurusan h2 {

    margin: 0;

    color: var(--papan);

}


/* =========================================================
   DESKRIPSI
========================================================= */

.deskripsi-jurusan {

    color: var(--teks-muda);

    line-height: 1.8;

}


/* =========================================================
   DETAIL
========================================================= */

.detail-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 28px;

    margin-top: 28px;

}


.detail-grid h3 {

    margin-bottom: 14px;

    color: var(--papan);

    font-size: 1rem;

}


/* =========================================================
   KOMPETENSI
========================================================= */

.daftar-kompetensi {

    margin: 0;

    padding: 0;

    list-style: none;

}


.daftar-kompetensi li {

    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 10px;

    color: var(--teks-muda);

}


.daftar-kompetensi li span {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    width: 22px;

    height: 22px;

    flex-shrink: 0;

    border-radius: 50%;

    background: #e9f0e8;

    color: #47704d;

    font-size: 0.75rem;

    font-weight: 800;

}


/* =========================================================
   PROSPEK
========================================================= */

.prospek-jurusan {

    padding: 22px;

    border-radius: 16px;

    background: var(--kertas);

}


.prospek-jurusan h3 {

    margin-top: 0;

}


.prospek-jurusan p {

    margin: 0;

    color: var(--teks-muda);

    line-height: 1.8;

}


/* =========================================================
   CTA
========================================================= */

.kotak-ajakan {

    background: var(--kertas-gelap);

    border: 1px solid var(--garis);

    border-radius: 24px;

    padding: 40px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 30px;

}


.kotak-ajakan .judul-bagian {

    margin-bottom: 0;

}


.kotak-ajakan h2 {

    margin-top: 12px;

}


.kotak-ajakan p {

    margin-bottom: 0;

}


.aksi-ajakan {

    display: flex;

    gap: 12px;

    flex-wrap: wrap;

    flex-shrink: 0;

}


.tombol-gelap {

    background: var(--papan);

    color: var(--kertas);

    border: 2px solid var(--papan);

}


.tombol-gelap:hover {

    background: var(--papan-terang);

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .detail-jurusan {

        grid-template-columns: 1fr;

        gap: 15px;

        padding: 24px;

    }


    .gambar-jurusan {

        height: 220px;

    }


    .detail-grid {

        grid-template-columns: 1fr;

        gap: 20px;

    }


    .nomor-jurusan {

        font-size: 22px;

    }


    .kotak-ajakan {

        flex-direction: column;

        align-items: flex-start;

        padding: 28px;

    }

}


@media (max-width: 480px) {

    .gambar-jurusan {

        height: 180px;

    }


    .detail-jurusan {

        padding: 18px;

    }


    .kotak-ajakan {

        padding: 22px;

    }

}

</style>


<?php require_once __DIR__ . '/includes/footer.php'; ?>