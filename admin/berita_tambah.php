<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$statusPesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $isi       = trim($_POST['isi'] ?? '');
    $penulis   = trim($_POST['penulis'] ?? '') ?: 'Admin';

    if ($judul === '' || $isi === '') {
        $statusPesan = 'Judul dan isi berita wajib diisi.';
    } else {
        $hasilUnggah = tanganiUnggahFoto('foto', 'berita');
        if (!$hasilUnggah['sukses']) {
            $statusPesan = $hasilUnggah['pesan'];
        } else {
            $slug = buatSlugUnik($koneksi, $judul);
            $stmt = $koneksi->prepare(
                "INSERT INTO berita (judul, slug, ringkasan, isi, foto, penulis) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$judul, $slug, $ringkasan ?: null, $isi, $hasilUnggah['namaFile'], $penulis]);
            header('Location: berita.php?status=ditambahkan');
            exit;
        }
    }
}

$judul_halaman = 'Tulis Berita';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
  <div class="pesan-status pesan-gagal"><?= htmlspecialchars($statusPesan) ?></div>
<?php endif; ?>

<div class="kartu-panel">
  <form method="post" action="berita_tambah.php" enctype="multipart/form-data">
    <div class="pratinjau-foto" id="pratinjau"></div>
    <div class="grup-form">
      <label for="foto">Foto Sampul</label>
      <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" onchange="pratinjauGambar(this,'pratinjau')">
      <p class="bantuan-form">Opsional. Format JPG/PNG/WEBP, maksimal 3MB.</p>
    </div>
    <div class="grup-form">
      <label for="judul">Judul Berita</label>
      <input type="text" id="judul" name="judul" required>
    </div>
    <div class="grup-form">
      <label for="ringkasan">Ringkasan Singkat</label>
      <input type="text" id="ringkasan" name="ringkasan" placeholder="Tampil di daftar berita, maks 1-2 kalimat">
    </div>
    <div class="grup-form">
      <label for="penulis">Penulis</label>
      <input type="text" id="penulis" name="penulis" value="Admin">
    </div>
    <div class="grup-form">
      <label for="isi">Isi Berita</label>
      <textarea id="isi" name="isi" style="min-height:220px;" required></textarea>
    </div>
    <button type="submit" class="tombol">Publikasikan</button>
    <a href="berita.php" class="tombol tombol-garis">Batal</a>
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
