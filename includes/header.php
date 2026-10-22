<?php
/**
 * Header bersama untuk semua halaman publik.
 * Variabel opsional yang bisa diisi sebelum include file ini:
 *   $judul_halaman  -> judul tab browser
 */
if (!isset($koneksi)) {
    require_once __DIR__ . '/../config/koneksi.php';
}

// Ambil profil sekolah sekali saja untuk dipakai di navbar & footer
$stmt = $koneksi->query("SELECT * FROM profil_sekolah LIMIT 1");
$profil = $stmt->fetch();

$namaSekolah = $profil['nama_sekolah'] ?? 'SMK Infokom Kota Bogor';
$judul_halaman = ($judul_halaman ?? 'Beranda') . ' — ' . $namaSekolah;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($judul_halaman) ?></title>
<meta name="description" content="Website resmi <?= htmlspecialchars($namaSekolah) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link
    rel="icon"
    type="image/png"
    href="<?= BASE_URL ?>/assets/images/logo-sekolah.png"
>
</head>
<body>
