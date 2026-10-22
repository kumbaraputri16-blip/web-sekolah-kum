<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $koneksi->prepare("SELECT foto FROM guru WHERE id = ?");
$stmt->execute([$id]);
$guru = $stmt->fetch();

if ($guru) {
    hapusFotoLama($guru['foto'], 'guru');
    $stmt = $koneksi->prepare("DELETE FROM guru WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: guru.php?status=dihapus');
exit;
