<?php

$halamanSaatIni = basename($_SERVER['SCRIPT_NAME']);

function aktifJika($file, $halamanSaatIni)
{
    return $file === $halamanSaatIni ? 'aktif' : '';
}

?>

<header class="navbar">

    <div class="container">

        <!-- BRAND / LOGO -->
        <a
            href="<?= BASE_URL ?>/index.php"
            class="navbar-brand"
        >

            <span class="lambang">
                <img
                    src="<?= BASE_URL ?>/assets/images/logo-sekolah.png"
                    alt="Logo <?= htmlspecialchars($namaSekolah) ?>"
                >
            </span>

            <span class="teks-brand">

                <strong>
                    <?= htmlspecialchars($namaSekolah) ?>
                </strong>

                <span>
                    KOTA BOGOR
                </span>

            </span>

        </a>


        <!-- TOMBOL MOBILE -->
        <button
            class="navbar-toggle"
            aria-label="Buka menu"
            type="button"
        >
            &#9776;
        </button>


        <!-- MENU -->
        <nav>

            <ul class="navbar-menu">

                <!-- BERANDA -->
                <li>
                    <a
                        class="<?= aktifJika('index.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/index.php"
                    >
                        Beranda
                    </a>
                </li>


                <!-- PROFIL -->
                <li>
                    <a
                        class="<?= aktifJika('profil.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/profil.php"
                    >
                        Profil
                    </a>
                </li>


                <!-- JURUSAN -->
                <li>
                    <a
                        class="<?= aktifJika('jurusan.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/jurusan.php"
                    >
                        Jurusan
                    </a>
                </li>


                <!-- PROGRAM -->
                <li>
                    <a
                        class="<?= aktifJika('program.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/program.php"
                    >
                        Program
                    </a>
                </li>
<!-- EKSKUL -->
<li>
    <a
        class="<?= aktifJika('ekskul.php', $halamanSaatIni) ?>"
        href="<?= BASE_URL ?>/ekskul.php"
    >
        Ekskul
    </a>
</li>

<!-- SARANA DAN PRASARANA -->
<li>
    <a
        class="<?= aktifJika('sarana-prasarana.php', $halamanSaatIni) ?>"
        href="<?= BASE_URL ?>/sarana-prasarana.php"
    >
        Sarana & Prasarana
    </a>
</li>

<!-- MITRA INDUSTRI -->
<li>
    <a
        class="<?= aktifJika('mitra-industri.php', $halamanSaatIni) ?>"
        href="<?= BASE_URL ?>/mitra-industri.php"
    >
        Mitra Industri
    </a>
</li>

                <!-- GURU -->
                <li>
                    <a
                        class="<?= aktifJika('guru.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/guru.php"
                    >
                        Guru & Staf
                    </a>
                </li>


                <!-- GALERI -->
                <li>
                    <a
                        class="<?= aktifJika('galeri.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/galeri.php"
                    >
                        Galeri
                    </a>
                </li>


                <!-- BERITA -->
                <li>
                    <a
                        class="<?= aktifJika('berita.php', $halamanSaatIni) ?> <?= aktifJika('berita_detail.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/berita.php"
                    >
                        Berita
                    </a>
                </li>


                <!-- PPDB -->
                <li>
                    <a
                        class="<?= aktifJika('ppdb.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/ppdb.php"
                    >
                        PPDB
                    </a>
                </li>


                <!-- KONTAK -->
                <li>
                    <a
                        class="<?= aktifJika('kontak.php', $halamanSaatIni) ?>"
                        href="<?= BASE_URL ?>/kontak.php"
                    >
                        Kontak
                    </a>
                </li>

            </ul>

        </nav>

    </div>

</header>