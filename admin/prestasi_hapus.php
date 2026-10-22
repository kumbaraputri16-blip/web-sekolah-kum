<?php
require_once __DIR__ . '/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: prestasi.php?status=ID%20foto%20tidak%20valid');
    exit;
}

$stmt = $koneksi->prepare(
    "SELECT foto FROM foto_prestasi WHERE id = ?"
);
$stmt->execute([$id]);

$foto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$foto) {
    header('Location: prestasi.php?status=Foto%20tidak%20ditemukan');
    exit;
}

$stmt = $koneksi->prepare(
    "DELETE FROM foto_prestasi WHERE id = ?"
);
$stmt->execute([$id]);

$namaFile = basename($foto['foto']);
$lokasiFoto = __DIR__ . '/../uploads/prestasi/' . $namaFile;

if (is_file($lokasiFoto)) {
    unlink($lokasiFoto);
}

header('Location: prestasi.php?status=Foto%20berhasil%20dihapus');
exit;