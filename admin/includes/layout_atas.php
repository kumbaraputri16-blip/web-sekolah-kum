<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/koneksi.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/sekolah-website-kum');
}

$namaAdmin = $_SESSION['admin_nama'] ?? 'Administrator';

$halamanSaatIni = basename($_SERVER['SCRIPT_NAME']);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Panel Admin — SMK Infokom Bogor
    </title>


    <!-- GOOGLE FONT -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- CSS ADMIN -->

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/admin/assets/css/admin.css"
    >
    <!-- FAVICON LOGO SEKOLAH -->

<link
    rel="icon"
    type="image/png"
    href="<?= BASE_URL ?>/assets/images/logo-sekolah.png"
>

</head>


<body>


<div class="admin-layout">


    <!-- =====================================================
         SIDEBAR
         ===================================================== -->

    <aside class="sidebar">


        <!-- MEREK -->

        <div class="merek">

            <div
                style="
                    display:flex;
                    align-items:center;
                    gap:12px;
                "
            >

                <img
                    src="<?= BASE_URL ?>/assets/images/logo-sekolah.png"
                    alt="Logo Sekolah"
                    style="
                        width:46px;
                        height:46px;
                        object-fit:contain;
                        flex-shrink:0;
                    "
                >

                <div>

                    <strong>
                        SMK Infokom Bogor
                    </strong>

                    <span>
                        Panel Admin
                    </span>

                </div>

            </div>

        </div>


        <!-- MENU -->

        <nav>


            <a
                href="<?= BASE_URL ?>/admin/dashboard.php"
                class="tautan-menu <?= $halamanSaatIni === 'dashboard.php' ? 'aktif' : '' ?>"
            >
                🏠
                <span>
                    Dashboard
                </span>
            </a>


         <!-- MENU GURU & STAF -->
<a
    href="<?= BASE_URL ?>/admin/guru.php"
    class="tautan-menu <?= in_array($halamanSaatIni, [
        'guru.php',
        'guru_tambah.php',
        'guru_edit.php',
        'guru_hapus.php',
        'struktur-sekolah.php'
    ]) ? 'aktif' : '' ?>"
>
    👨‍🏫
    <span>Guru &amp; Staf</span>
</a>


            <a
                href="<?= BASE_URL ?>/admin/galeri.php"
                class="tautan-menu <?= $halamanSaatIni === 'galeri.php' || $halamanSaatIni === 'galeri_tambah.php' ? 'aktif' : '' ?>"
            >
                🖼️
                <span>
                    Galeri
                </span>
            </a>

<a href="<?= BASE_URL ?>/admin/fasilitas.php"
   class="tautan-menu <?= $halamanSaatIni === 'fasilitas.php' ? 'aktif' : '' ?>">
    🏫 <span>Sarana &amp; Prasarana</span>
</a>

            <a
                href="<?= BASE_URL ?>/admin/berita.php"
                class="tautan-menu <?= $halamanSaatIni === 'berita.php' || $halamanSaatIni === 'berita_tambah.php' || $halamanSaatIni === 'berita_edit.php' ? 'aktif' : '' ?>"
            >
                📰
                <span>
                    Berita
                </span>
            </a>


                    <a
                href="<?= BASE_URL ?>/admin/program.php"
                class="tautan-menu <?= $halamanSaatIni === 'program.php' ? 'aktif' : '' ?>"
            >
                📚
                <span>Program Sekolah</span>
            </a>

            <a
                href="<?= BASE_URL ?>/admin/edit-ekskul.php"
                class="tautan-menu <?= $halamanSaatIni === 'edit-ekskul.php' ? 'aktif' : '' ?>"
            >
                🎭
                <span>Ekstrakurikuler</span>
            </a>

           <a
    href="<?= BASE_URL ?>/admin/alumni-jepang.php"
    class="tautan-menu <?= $halamanSaatIni === 'alumni-jepang.php' ? 'aktif' : '' ?>"
>
    🇯🇵
    <span>Alumni Jepang</span>
</a>
            

           <a
    href="<?= BASE_URL ?>/admin/pesan.php"
    class="tautan-menu <?= $halamanSaatIni === 'pesan.php' ? 'aktif' : '' ?>"
>
    ✉️
    <span>Pesan Masuk</span>
</a>

<a
    href="<?= BASE_URL ?>/admin/pendaftaran.php"
    class="tautan-menu <?= in_array($halamanSaatIni, ['pendaftaran.php', 'cetak-pendaftaran.php']) ? 'aktif' : '' ?>"
>
    📄
    <span>Data Pendaftaran PPDB</span>
</a>
<a
    href="<?= BASE_URL ?>/admin/edit-mitra.php"
    class="tautan-menu <?= $halamanSaatIni === 'edit-mitra.php' ? 'aktif' : '' ?>"
>
    🤝
    <span>Mitra Industri</span>
</a>

        </nav>


        <!-- BAGIAN BAWAH -->

        <div class="keluar">


            <a
                href="<?= BASE_URL ?>/index.php"
                class="tautan-menu"
                target="_blank"
            >
                🌐
                <span>
                    Lihat Website
                </span>
            </a>


            <a
                href="<?= BASE_URL ?>/admin/logout.php"
                class="tautan-menu"
            >
                ↪
                <span>
                    Keluar
                </span>
            </a>


        </div>


    </aside>



    <!-- =====================================================
         KONTEN UTAMA
         ===================================================== -->

    <div class="konten-utama">


        <!-- TOPBAR -->

        <header class="topbar">


            <div>

                <strong>
                    Panel Admin
                </strong>

            </div>


            <div>

                <span
                    style="
                        color:#5C574C;
                        font-size:0.9rem;
                    "
                >
                    👤 <?= htmlspecialchars($namaAdmin) ?>
                </span>

            </div>


        </header>


        <!-- AREA KONTEN -->

        <main class="area-konten">
            