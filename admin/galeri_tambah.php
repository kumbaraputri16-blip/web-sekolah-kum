<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$statusPesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal   = $_POST['tanggal'] ?? null;

    if ($judul === '') {
        $statusPesan = 'Judul foto wajib diisi.';
    } elseif (empty($_FILES['foto']['name'])) {
        $statusPesan = 'Silakan pilih foto yang akan diunggah.';
    } else {
        $hasilUnggah = tanganiUnggahFoto('foto', 'galeri');
        if (!$hasilUnggah['sukses'] || !$hasilUnggah['namaFile']) {
            $statusPesan = $hasilUnggah['pesan'] ?: 'Gagal mengunggah foto.';
        } else {
            $stmt = $koneksi->prepare(
                "INSERT INTO galeri (judul, deskripsi, foto, tanggal) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$judul, $deskripsi ?: null, $hasilUnggah['namaFile'], $tanggal ?: null]);
            header('Location: galeri.php?status=ditambahkan');
            exit;
        }
    }
}

$judul_halaman = 'Tambah Foto Galeri';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
  <div class="pesan-status pesan-gagal"><?= htmlspecialchars($statusPesan) ?></div>
<?php endif; ?>

<div class="kartu-panel">
  <form method="post" action="galeri_tambah.php" enctype="multipart/form-data">
    <div class="pratinjau-foto" id="pratinjau"></div>
    <div class="grup-form">
      <label for="foto">Foto</label>
      <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" required onchange="pratinjauGambar(this,'pratinjau')">
      <p class="bantuan-form">Format JPG/PNG/WEBP, maksimal 3MB.</p>
    </div>
    <div class="grup-form">
      <label for="judul">Judul</label>
      <input type="text" id="judul" name="judul" placeholder="Contoh: Lomba 17 Agustus" required>
    </div>
    <div class="grup-form">
      <label for="deskripsi">Deskripsi</label>
      <textarea id="deskripsi" name="deskripsi"></textarea>
    </div>
    <div class="grup-form">
      <label for="tanggal">Tanggal Kegiatan</label>
      <input type="date" id="tanggal" name="tanggal">
    </div>
    <button type="submit" class="tombol">Unggah Foto</button>
    <a href="galeri.php" class="tombol tombol-garis">Batal</a>
  </form>
</div>

<script>
function pratinjauGambar(input, targetId) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function (e) {
    document.getElementById(targetId).style.backgroundImage = 'url(' + e.target.result + ')';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
