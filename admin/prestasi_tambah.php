<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$statusPesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? null;

    if ($judul === '') {
        $statusPesan = 'Judul foto wajib diisi.';
    } elseif (empty($_FILES['foto']['name'])) {
        $statusPesan = 'Silakan pilih foto yang akan diunggah.';
    } else {
        // Upload ke folder khusus prestasi
        $hasilUnggah = tanganiUnggahFoto('foto', 'prestasi');

        if (!$hasilUnggah['sukses'] || !$hasilUnggah['namaFile']) {
            $statusPesan = $hasilUnggah['pesan'] ?: 'Gagal mengunggah foto.';
        } else {
            try {
                $stmt = $koneksi->prepare("
                    INSERT INTO foto_prestasi
                    (judul, deskripsi, foto, tanggal)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $judul,
                    $deskripsi ?: null,
                    $hasilUnggah['namaFile'],
                    $tanggal ?: null
                ]);

                header('Location: prestasi.php?status=Foto%20berhasil%20ditambahkan');
                exit;
            } catch (PDOException $e) {
                $statusPesan = 'Data gagal disimpan ke database.';
            }
        }
    }
}

$judul_halaman = 'Tambah Foto Prestasi';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
    <div class="pesan-status pesan-gagal">
        <?= htmlspecialchars($statusPesan, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="kartu-panel">

    <h3>Tambah Foto Prestasi & Kegiatan</h3>

    <p style="color:var(--teks-muda);">
        Foto yang diunggah akan digunakan untuk dokumentasi
        prestasi dan kegiatan sekolah.
    </p>

    <form
        method="post"
        action="prestasi_tambah.php"
        enctype="multipart/form-data"
    >

        <div
            class="pratinjau-foto"
            id="pratinjau"
        ></div>

        <div class="grup-form">
            <label for="foto">Pilih Foto</label>

            <input
                type="file"
                id="foto"
                name="foto"
                accept="image/jpeg,image/png,image/webp"
                required
                onchange="pratinjauGambar(this,'pratinjau')"
            >

            <p class="bantuan-form">
                Format JPG, PNG, atau WEBP. Maksimal 3 MB,
                mengikuti ketentuan upload website.
            </p>
        </div>

        <div class="grup-form">
            <label for="judul">Judul Prestasi atau Kegiatan</label>

            <input
                type="text"
                id="judul"
                name="judul"
                maxlength="150"
                placeholder="Contoh: Juara Lomba Kompetensi Siswa"
                required
            >
        </div>

        <div class="grup-form">
            <label for="deskripsi">Keterangan Foto</label>

            <textarea
                id="deskripsi"
                name="deskripsi"
                placeholder="Tuliskan keterangan prestasi atau kegiatan..."
            ></textarea>
        </div>

        <div class="grup-form">
            <label for="tanggal">Tanggal Kegiatan</label>

            <input
                type="date"
                id="tanggal"
                name="tanggal"
            >
        </div>

        <button type="submit" class="tombol">
            Unggah Foto
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
        target.style.backgroundImage = '';
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