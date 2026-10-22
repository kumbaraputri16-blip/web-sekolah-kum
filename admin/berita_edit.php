<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $koneksi->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->execute([$id]);
$berita = $stmt->fetch();

if (!$berita) {
    header('Location: berita.php');
    exit;
}

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
            $foto = $berita['foto'];
            if ($hasilUnggah['namaFile']) {
                hapusFotoLama($berita['foto'], 'berita');
                $foto = $hasilUnggah['namaFile'];
            }
            $slug = $judul !== $berita['judul'] ? buatSlugUnik($koneksi, $judul, $id) : $berita['slug'];

            $stmt = $koneksi->prepare(
                "UPDATE berita SET judul=?, slug=?, ringkasan=?, isi=?, foto=?, penulis=? WHERE id=?"
            );
            $stmt->execute([$judul, $slug, $ringkasan ?: null, $isi, $foto, $penulis, $id]);
            header('Location: berita.php?status=diubah');
            exit;
        }
    }
}

$judul_halaman = 'Edit Berita';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
  <div class="pesan-status pesan-gagal"><?= htmlspecialchars($statusPesan) ?></div>
<?php endif; ?>

<div class="kartu-panel">
  <form method="post" action="berita_edit.php?id=<?= $berita['id'] ?>" enctype="multipart/form-data">
    <div class="pratinjau-foto" id="pratinjau" style="<?= $berita['foto'] ? "background-image:url('../uploads/berita/".htmlspecialchars($berita['foto'])."')" : '' ?>"></div>
    <div class="grup-form">
      <label for="foto">Ganti Foto Sampul</label>
      <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" onchange="pratinjauGambar(this,'pratinjau')">
      <p class="bantuan-form">Kosongkan jika tidak ingin mengubah foto.</p>
    </div>
    <div class="grup-form">
      <label for="judul">Judul Berita</label>
      <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($berita['judul']) ?>" required>
    </div>
    <div class="grup-form">
      <label for="ringkasan">Ringkasan Singkat</label>
      <input type="text" id="ringkasan" name="ringkasan" value="<?= htmlspecialchars($berita['ringkasan'] ?? '') ?>">
    </div>
    <div class="grup-form">
      <label for="penulis">Penulis</label>
      <input type="text" id="penulis" name="penulis" value="<?= htmlspecialchars($berita['penulis']) ?>">
    </div>
    <div class="grup-form">
      <label for="isi">Isi Berita</label>
      <textarea id="isi" name="isi" style="min-height:220px;" required><?= htmlspecialchars($berita['isi']) ?></textarea>
    </div>
    <button type="submit" class="tombol">Simpan Perubahan</button>
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
