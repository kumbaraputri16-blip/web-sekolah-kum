<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

function e($nilai) {
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $koneksi->prepare("SELECT * FROM foto_prestasi WHERE id = ?");
$stmt->execute([$id]);
$prestasi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestasi) {
    header('Location: prestasi.php?status=Data%20foto%20tidak%20ditemukan');
    exit;
}

$statusPesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';

    if ($judul === '') {
        $statusPesan = 'Judul foto wajib diisi.';
    } else {
        $namaFoto = $prestasi['foto'];

        // Foto lama tetap digunakan jika tidak memilih foto baru.
        if (!empty($_FILES['foto']['name'])) {
            $hasilUnggah = tanganiUnggahFoto('foto', 'prestasi');

            if (
                empty($hasilUnggah['sukses']) ||
                empty($hasilUnggah['namaFile'])
            ) {
                $statusPesan = $hasilUnggah['pesan'] ?: 'Gagal mengunggah foto.';
            } else {
                $namaFoto = $hasilUnggah['namaFile'];
            }
        }

        if ($statusPesan === '') {
            try {
                $update = $koneksi->prepare("
                    UPDATE foto_prestasi
                    SET judul = ?, deskripsi = ?, foto = ?, tanggal = ?
                    WHERE id = ?
                ");

                $update->execute([
                    $judul,
                    $deskripsi !== '' ? $deskripsi : null,
                    $namaFoto,
                    $tanggal !== '' ? $tanggal : null,
                    $id
                ]);

                header('Location: prestasi.php?status=Foto%20berhasil%20diperbarui');
                exit;
            } catch (PDOException $e) {
                $statusPesan = 'Data gagal diperbarui. Periksa kembali database.';
            }
        }
    }

    // Pertahankan isian ketika terjadi kesalahan.
    $prestasi['judul'] = $judul;
    $prestasi['deskripsi'] = $deskripsi;
    $prestasi['tanggal'] = $tanggal;
    $prestasi['foto'] = $namaFoto ?? $prestasi['foto'];
}

$judul_halaman = 'Edit Foto Prestasi';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan !== ''): ?>
    <div class="pesan-status pesan-gagal">
        <?= e($statusPesan) ?>
    </div>
<?php endif; ?>

<div class="kartu-panel">
    <h3>Edit Foto Prestasi & Kegiatan</h3>

    <form
        method="post"
        action="prestasi_edit.php?id=<?= (int) $id ?>"
        enctype="multipart/form-data"
    >
        <input type="hidden" name="id" value="<?= (int) $id ?>">

        <div
            class="pratinjau-foto"
            id="pratinjau"
            style="background-image:url('../uploads/prestasi/<?= e(rawurlencode(basename($prestasi['foto']))) ?>');"
        ></div>

        <div class="grup-form">
            <label for="foto">Ganti Foto (Opsional)</label>

            <input
                type="file"
                id="foto"
                name="foto"
                accept="image/jpeg,image/png,image/webp"
                onchange="pratinjauGambar(this, 'pratinjau')"
            >

            <p class="bantuan-form">
                Kosongkan jika tidak ingin mengganti foto.
                Format JPG, PNG, atau WEBP.
            </p>
        </div>

        <div class="grup-form">
            <label for="judul">Judul Prestasi atau Kegiatan</label>

            <input
                type="text"
                id="judul"
                name="judul"
                maxlength="150"
                value="<?= e($prestasi['judul']) ?>"
                required
            >
        </div>

        <div class="grup-form">
            <label for="deskripsi">Keterangan Foto (Opsional)</label>

            <textarea
                id="deskripsi"
                name="deskripsi"
                placeholder="Tuliskan keterangan jika diperlukan..."
            ><?= e($prestasi['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="grup-form">
            <label for="tanggal">Tanggal Kegiatan</label>

            <input
                type="date"
                id="tanggal"
                name="tanggal"
                value="<?= e($prestasi['tanggal'] ?? '') ?>"
            >
        </div>

        <button type="submit" class="tombol">
            Simpan Perubahan
        </button>

        <a href="prestasi.php" class="tombol tombol-garis">
            Batal
        </a>
    </form>
</div>

<script>
function pratinjauGambar(input, targetId) {
    const target = document.getElementById(targetId);

    if (!input.files || !input.files[0]) {
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        target.style.backgroundImage = 'url("' + e.target.result + '")';
    };

    reader.readAsDataURL(input.files[0]);
}
</script>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>