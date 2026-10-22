<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $koneksi->prepare("SELECT foto FROM berita WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if ($item) {
    hapusFotoLama($item['foto'], 'berita');
    $stmt = $koneksi->prepare("DELETE FROM berita WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: berita.php?status=dihapus');
exit;
