<?php
/**
 * Menangani unggah foto dengan aman.
 *
 * @param string $fieldName nama input file, misal 'foto'
 * @param string $folderTujuan folder di dalam /uploads, misal 'guru', 'galeri', 'berita'
 * @return array ['sukses' => bool, 'namaFile' => string|null, 'pesan' => string|null]
 */
function tanganiUnggahFoto(string $fieldName, string $folderTujuan): array
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['sukses' => true, 'namaFile' => null, 'pesan' => null]; // tidak wajib
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['sukses' => false, 'namaFile' => null, 'pesan' => 'Terjadi kesalahan saat mengunggah foto.'];
    }

    $ukuranMaksimal = 3 * 1024 * 1024; // 3 MB
    if ($file['size'] > $ukuranMaksimal) {
        return ['sukses' => false, 'namaFile' => null, 'pesan' => 'Ukuran foto maksimal 3 MB.'];
    }

    $infoGambar = @getimagesize($file['tmp_name']);
    if ($infoGambar === false) {
        return ['sukses' => false, 'namaFile' => null, 'pesan' => 'File yang diunggah bukan gambar yang valid.'];
    }

    $ekstensiDiizinkan = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    $mime = $infoGambar['mime'];
    if (!isset($ekstensiDiizinkan[$mime])) {
        return ['sukses' => false, 'namaFile' => null, 'pesan' => 'Format foto harus JPG, PNG, atau WEBP.'];
    }

    $ekstensi = $ekstensiDiizinkan[$mime];
    $namaFile = bin2hex(random_bytes(8)) . '.' . $ekstensi;
    $folderFisik = __DIR__ . '/../../uploads/' . $folderTujuan . '/';

    if (!is_dir($folderFisik)) {
        mkdir($folderFisik, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $folderFisik . $namaFile)) {
        return ['sukses' => false, 'namaFile' => null, 'pesan' => 'Gagal menyimpan foto ke server.'];
    }

    return ['sukses' => true, 'namaFile' => $namaFile, 'pesan' => null];
}

/**
 * Menghapus file foto lama dari folder uploads (dipakai saat update/hapus data).
 */
/**
 * Mengubah judul berita menjadi slug URL yang unik.
 */
function buatSlugUnik(PDO $koneksi, string $judul, ?int $idKecuali = null): string
{
    $dasar = strtolower(trim($judul));
    $dasar = preg_replace('/[^a-z0-9]+/', '-', $dasar);
    $dasar = trim($dasar, '-');
    if ($dasar === '') $dasar = 'berita';

    $slug = $dasar;
    $angka = 1;
    while (true) {
        $sql = "SELECT COUNT(*) FROM berita WHERE slug = ?" . ($idKecuali ? " AND id != ?" : "");
        $stmt = $koneksi->prepare($sql);
        $params = $idKecuali ? [$slug, $idKecuali] : [$slug];
        $stmt->execute($params);
        if ((int)$stmt->fetchColumn() === 0) {
            return $slug;
        }
        $angka++;
        $slug = $dasar . '-' . $angka;
    }
}

function hapusFotoLama(?string $namaFile, string $folderTujuan): void
{
    if (!$namaFile) return;
    $path = __DIR__ . '/../../uploads/' . $folderTujuan . '/' . $namaFile;
    if (is_file($path)) {
        @unlink($path);
    }
}
