<?php

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Profil Sekolah';

$profil = $koneksi->query("
    SELECT *
    FROM profil_sekolah
    LIMIT 1
")->fetch();

$namaSekolah = $profil['nama_sekolah'] ?? 'SMK Infokom Bogor';

/*
|--------------------------------------------------------------------------
| DATA STRUKTUR ORGANISASI
|--------------------------------------------------------------------------
*/

$daftarStruktur = $koneksi->query("
    SELECT *
    FROM struktur_sekolah
    ORDER BY urutan ASC
")->fetchAll(PDO::FETCH_ASSOC);

$struktur = [];

foreach ($daftarStruktur as $item) {
    $struktur[$item['kode']] = $item;
}

/*
|--------------------------------------------------------------------------
| FUNGSI MENAMPILKAN KARTU JABATAN
|--------------------------------------------------------------------------
*/

function tampilkanKartuStruktur($struktur, $kode)
{
    if (!isset($struktur[$kode])) {
        return;
    }

    $item = $struktur[$kode];

    $nama = trim($item['nama'] ?? '');
    $jabatan = $item['jabatan'] ?? '';

    $foto = null;

    if (!empty($item['foto'])) {
        $namaFoto = basename($item['foto']);
        $pathFoto = __DIR__ . '/uploads/struktur/' . $namaFoto;

        if (is_file($pathFoto)) {
            $foto = BASE_URL . '/uploads/struktur/' . rawurlencode($namaFoto);
        }
    }
    ?>

    <div class="kartu-organisasi">

        <?php if ($foto): ?>

            <img
                class="foto-organisasi"
                src="<?= htmlspecialchars($foto) ?>"
                alt="<?= htmlspecialchars($jabatan) ?>"
                loading="lazy"
            >

        <?php else: ?>

            <div class="foto-organisasi foto-kosong">
                <span>👤</span>
            </div>

        <?php endif; ?>

        <div class="info-organisasi">

            <h3>
                <?= htmlspecialchars($nama !== '' ? $nama : 'Nama belum diisi') ?>
            </h3>

            <p>
                <?= htmlspecialchars($jabatan) ?>
            </p>

        </div>

    </div>

    <?php
}

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>


<!-- HEADER HALAMAN -->

<section class="header-halaman">

    <div class="container">

        <h1>Profil Sekolah</h1>

        <p>
            Mengenal lebih dekat <?= htmlspecialchars($namaSekolah) ?>.
        </p>

    </div>

</section>


<!-- SEJARAH -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">TENTANG KAMI</span>

            <h2>Sejarah Sekolah</h2>

        </div>

        <div class="isi-artikel">

            <p>
                <?= nl2br(
                    htmlspecialchars(
                        $profil['sejarah']
                        ?? 'Informasi sejarah sekolah belum tersedia.'
                    )
                ) ?>
            </p>

        </div>

    </div>

</section>


<!-- VISI & MISI -->
<section class="bagian bagian-kertas-gelap visi-misi-animasi">

    <div class="container">

        <div class="judul-bagian animasi-judul">
            <span class="kop">ARAH SEKOLAH</span>
            <h2>Visi &amp; Misi</h2>
        </div>

        <div class="grid-2">

            <!-- KARTU VISI -->
            <div class="kotak-info kartu-visi-animasi">

                <div class="ikon-visi-misi">✦</div>

                <h3>Visi</h3>

                <div class="garis-visi-misi"></div>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $profil['visi']
                            ?? 'Visi sekolah belum tersedia.'
                        )
                    ) ?>
                </p>

            </div>

            <!-- KARTU MISI -->
            <div class="kotak-info kartu-misi-animasi">

                <div class="ikon-visi-misi">✧</div>

                <h3>Misi</h3>

                <div class="garis-visi-misi"></div>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $profil['misi']
                            ?? 'Misi sekolah belum tersedia.'
                        )
                    ) ?>
                </p>

            </div>

        </div>

    </div>

</section>

</section>


<!-- INFORMASI SEKOLAH -->

<section class="bagian">

    <div class="container">

        <div class="judul-bagian">

           

</section>


<!-- STRUKTUR ORGANISASI -->

<section class="bagian struktur-sekolah">

    <div class="container">

        <div class="judul-bagian">

            <span class="kop">MANAJEMEN SEKOLAH</span>

            <h2>Struktur Organisasi Sekolah</h2>

            <p>
                Susunan pimpinan, tenaga pendidik, dan staf
                <?= htmlspecialchars($namaSekolah) ?>.
            </p>

        </div>


        <?php if (!empty($struktur)): ?>

            <div class="organisasi-wrapper">

                <!-- KEPALA SEKOLAH -->

                <div class="organisasi-kepala">

                    <?php tampilkanKartuStruktur($struktur, 'kepala'); ?>

                </div>


                <!-- GARIS PENGHUBUNG UTAMA -->

                <div class="garis-utama"></div>


                <!-- EMPAT WAKASEK -->

                <div class="organisasi-wakasek">

                    <div class="kolom-organisasi">

                        <?php tampilkanKartuStruktur($struktur, 'hubinmas'); ?>

                        <div class="garis-turun"></div>

                        <div class="kelompok-staf">

                            <?php tampilkanKartuStruktur($struktur, 'teller1'); ?>

                            <?php tampilkanKartuStruktur($struktur, 'teller2'); ?>

                        </div>

                    </div>


                    <div class="kolom-organisasi">

                        <?php tampilkanKartuStruktur($struktur, 'kesiswaan'); ?>

                        <div class="garis-turun"></div>

                        <div class="kelompok-staf">

                            <?php tampilkanKartuStruktur($struktur, 'bk1'); ?>

                            <?php tampilkanKartuStruktur($struktur, 'bk2'); ?>

                            <?php tampilkanKartuStruktur($struktur, 'bk3'); ?>

                        </div>

                    </div>


                    <div class="kolom-organisasi">

                        <?php tampilkanKartuStruktur($struktur, 'kurikulum'); ?>

                        <div class="garis-turun"></div>

                        <div class="kelompok-staf">

                            <?php tampilkanKartuStruktur($struktur, 'staf_kurikulum'); ?>

                            <?php tampilkanKartuStruktur($struktur, 'perpustakaan'); ?>

                        </div>

                    </div>


                    <div class="kolom-organisasi">

                        <?php tampilkanKartuStruktur($struktur, 'sarpras'); ?>

                        <div class="garis-turun"></div>

                        <div class="kelompok-staf">

                            <?php tampilkanKartuStruktur($struktur, 'tu1'); ?>

                            <?php tampilkanKartuStruktur($struktur, 'tu2'); ?>

                        </div>

                    </div>

                </div>

            </div>

        <?php else: ?>

            <div class="kotak-info">

                <h3>Struktur Organisasi Belum Tersedia</h3>

                <p>
                    Silakan tambahkan data struktur organisasi melalui
                    panel admin.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- CTA -->

<section class="hero">

    <div class="container">

        <span class="hero-kop">INFORMASI LEBIH LANJUT</span>

        <h1>Ingin Mengenal Kami Lebih Dekat?</h1>

        <p class="lead">
            Hubungi sekolah untuk mendapatkan informasi
            lebih lengkap mengenai kegiatan, program,
            dan penerimaan peserta didik baru.
        </p>

        <div class="hero-aksi">

            <a
                href="<?= BASE_URL ?>/kontak.php"
                class="tombol tombol-utama"
            >
                Hubungi Sekolah →
            </a>

            <a
                href="<?= BASE_URL ?>/ppdb.php"
                class="tombol tombol-luar"
            >
                Informasi PPDB
            </a>

        </div>

    </div>

</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>


<!-- CSS KHUSUS STRUKTUR ORGANISASI -->

<style>
/* ========================================
   STRUKTUR ORGANISASI SEKOLAH
======================================== */

.bagian-organisasi {
    overflow: hidden;
}

.organisasi-wrapper {
    width: 100%;
    max-width: 1100px;
    margin: 45px auto 0;
    padding: 20px 0;
    box-sizing: border-box;
}

/* KARTU JABATAN */

.kartu-organisasi {
    width: 100%;
    min-width: 0;
    height: 260px;
    box-sizing: border-box;
    padding: 20px 14px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;

    background: #ffffff;
    border: 1px solid #dfe8e3;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(20, 65, 45, 0.07);

    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kartu-organisasi:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 26px rgba(20, 65, 45, 0.13);
}

/* FOTO */

.foto-organisasi {
    width: 100px;
    height: 100px;
    min-width: 100px;
    flex-shrink: 0;

    display: flex;
    justify-content: center;
    align-items: center;

    object-fit: cover;
    object-position: center;

    border-radius: 50%;
    border: 3px solid #e0ebe5;
    background: #f1f5f3;

    margin: 0 auto 14px;
}

.foto-kosong span {
    font-size: 36px;
}

/* NAMA DAN JABATAN */

.info-organisasi {
    width: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
}

.info-organisasi h3 {
    width: 100%;
    margin: 0 0 8px;

    color: #183d2d;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.45;

    overflow-wrap: anywhere;
}

.info-organisasi p {
    width: 100%;
    margin: 0;

    color: #68796f;
    font-size: 12px;
    line-height: 1.6;

    overflow-wrap: anywhere;
}

/* KEPALA SEKOLAH */

.organisasi-kepala {
    width: 260px;
    max-width: 100%;
    margin: 0 auto;

    position: relative;
    z-index: 2;
}

.organisasi-kepala .kartu-organisasi {
    height: 260px;
    border: 2px solid #176b50;
    background: #f4fbf6;
}

.organisasi-kepala .foto-organisasi {
    border-color: #176b50;
}

/* GARIS DARI KEPALA SEKOLAH */

.garis-utama {
    width: 2px;
    height: 35px;
    margin: 0 auto;
    background: #176b50;
}

/* EMPAT WAKASEK
   Baris 1: Hubinmas dan Sarpras
   Baris 2: Kesiswaan dan Kurikulum
*/

.organisasi-wakasek {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 32px 34px;

    position: relative;
}

/* Setiap kolom adalah satu cabang wakasek */

.kolom-organisasi {
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

/* Semua kotak wakasek sama tinggi */

.kolom-organisasi > .kartu-organisasi {
    width: 100%;
    max-width: 320px;
    height: 260px;
}

/* Garis dari wakasek menuju staf */

.garis-turun {
    width: 2px;
    height: 28px;
    flex-shrink: 0;
    background: #176b50;
}

/* KELOMPOK STAF */

.kelompok-staf {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: stretch;
    gap: 14px;
}
/* Membuat kotak staf lebih kecil */
.kelompok-staf {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    width: 100%;
}

/* Kotak Guru BK: wajib 3 sejajar */
.kolom-organisasi:nth-child(2) .kelompok-staf {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 6px;
}

/* Ukuran kotak Guru BK */
.kolom-organisasi:nth-child(2) .kelompok-staf .kartu-organisasi {
    width: 100%;
    min-width: 0;
    height: 180px;
    padding: 10px 4px;
    box-sizing: border-box;
}

/* Foto Guru BK */
.kolom-organisasi:nth-child(2) .kelompok-staf .foto-organisasi {
    width: 48px;
    height: 48px;
    min-width: 48px;
    margin-bottom: 8px;
}

/* Tulisan Guru BK */
.kolom-organisasi:nth-child(2) .kelompok-staf .info-organisasi h3 {
    font-size: 10px;
    line-height: 1.3;
    overflow-wrap: anywhere;
}
/* Semua kartu staf sama ukuran */

.kelompok-staf .kartu-organisasi {
    width: 100%;
    height: 230px;
    padding: 16px 10px;
    border-radius: 13px;
}

.kelompok-staf .foto-organisasi {
    width: 65px;
    height: 65px;
    min-width: 65px;
    margin-bottom: 10px;
}

.kelompok-staf .info-organisasi h3 {
    font-size: 12px;
    line-height: 1.4;
}

.kelompok-staf .info-organisasi p {
    font-size: 10px;
    line-height: 1.4;
}

/* CABANG DENGAN TIGA STAF */

.kolom-organisasi:nth-child(3) .kelompok-staf {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

/* ========================================
   TABLET
======================================== */

@media (max-width: 900px) {

    .organisasi-wrapper {
        max-width: 720px;
    }

    .organisasi-wakasek {
        gap: 25px 20px;
    }

    .kolom-organisasi > .kartu-organisasi {
        max-width: 100%;
    }

    .kelompok-staf {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .kelompok-staf .kartu-organisasi {
        padding: 14px 7px;
    }

    .info-organisasi h3 {
        font-size: 14px;
    }

}

/* ========================================
   HP
======================================== */

@media (max-width: 560px) {

    .organisasi-wrapper {
        margin-top: 25px;
        padding: 10px 0;
    }

    .organisasi-kepala {
        width: 260px;
    }

    .organisasi-wakasek {
        grid-template-columns: 1fr;
        gap: 28px;
    }

    .kolom-organisasi > .kartu-organisasi {
        max-width: 300px;
    }

    .kelompok-staf {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: stretch;
    gap: 10px;
}

   .kelompok-staf .kartu-organisasi {
    width: 100%;
    height: 200px;
    padding: 12px 8px;
    border-radius: 12px;
}
/* Guru BK: tiga kartu dalam satu baris */
.kolom-organisasi:nth-child(2) .kelompok-staf {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
}

.kolom-organisasi:nth-child(2) .kelompok-staf .kartu-organisasi {
    min-width: 0;
    padding: 12px 5px;
}

.kolom-organisasi:nth-child(2) .kelompok-staf .foto-organisasi {
    width: 55px;
    height: 55px;
    min-width: 55px;
}
}

/* HP KECIL */

@media (max-width: 360px) {

    .kelompok-staf {
        grid-template-columns: 1fr;
    }

    .kelompok-staf .kartu-organisasi {
        height: 220px;
        max-width: 270px;
        margin: 0 auto;
    }
@media (max-width: 560px) {
    .kolom-organisasi:nth-child(2) .kelompok-staf {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
    }

    .kolom-organisasi:nth-child(2) .kelompok-staf .kartu-organisasi {
        height: 185px;
        padding: 10px 4px;
    }

    .kolom-organisasi:nth-child(2) .kelompok-staf .foto-organisasi {
        width: 45px;
        height: 45px;
        min-width: 45px;
    }

    .kolom-organisasi:nth-child(2) .kelompok-staf .info-organisasi h3 {
        font-size: 10px;
        overflow-wrap: anywhere;
    }
}
}
</style>