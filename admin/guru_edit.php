<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $koneksi->prepare("SELECT * FROM guru WHERE id = ?");
$stmt->execute([$id]);
$guru = $stmt->fetch();

if (!$guru) {
    header('Location: guru.php');
    exit;
}

$statusPesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $nip     = trim($_POST['nip'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $mapel   = trim($_POST['mata_pelajaran'] ?? '');

    if ($nama === '' || $jabatan === '') {
        $statusPesan = 'Nama dan jabatan wajib diisi.';
    } else {
        $hasilUnggah = tanganiUnggahFoto('foto', 'guru');
        if (!$hasilUnggah['sukses']) {
            $statusPesan = $hasilUnggah['pesan'];
        } else {
            $foto = $guru['foto'];
            if ($hasilUnggah['namaFile']) {
                hapusFotoLama($guru['foto'], 'guru');
                $foto = $hasilUnggah['namaFile'];
            }
            $stmt = $koneksi->prepare(
                "UPDATE guru SET nama=?, nip=?, jabatan=?, mata_pelajaran=?, foto=? WHERE id=?"
            );
            $stmt->execute([$nama, $nip ?: null, $jabatan, $mapel ?: null, $foto, $id]);
            header('Location: guru.php?status=diubah');
            exit;
        }
    }
}

$judul_halaman = 'Edit Guru';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
  <div class="pesan-status pesan-gagal"><?= htmlspecialchars($statusPesan) ?></div>
<?php endif; ?>

<div class="kartu-panel">
  <form method="post" action="guru_edit.php?id=<?= $guru['id'] ?>" enctype="multipart/form-data">
    <div class="pratinjau-foto" id="pratinjau" style="<?= $guru['foto'] ? "background-image:url('../uploads/guru/".htmlspecialchars($guru['foto'])."')" : '' ?>"></div>
    <div class="grup-form">
      <label for="foto">Ganti Foto</label>
      <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" onchange="pratinjauGambar(this,'pratinjau')">
      <p class="bantuan-form">Kosongkan jika tidak ingin mengubah foto.</p>
    </div>
    <div class="grup-form">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($guru['nama']) ?>" required>
    </div>
    <div class="grup-form">
      <label for="nip">NIP</label>
      <input type="text" id="nip" name="nip" value="<?= htmlspecialchars($guru['nip'] ?? '') ?>">
    </div>
    <div class="grup-form">
      <label for="jabatan">Jabatan</label>
      <input type="text" id="jabatan" name="jabatan" value="<?= htmlspecialchars($guru['jabatan']) ?>" required>
    </div>
    <div class="grup-form">
      <label for="mata_pelajaran">Mata Pelajaran</label>
      <input type="text" id="mata_pelajaran" name="mata_pelajaran" value="<?= htmlspecialchars($guru['mata_pelajaran'] ?? '') ?>">
    </div>
    <button type="submit" class="tombol">Simpan Perubahan</button>
    <a href="guru.php" class="tombol tombol-garis">Batal</a>
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
