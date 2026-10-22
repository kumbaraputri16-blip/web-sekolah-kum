<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

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
            $stmt = $koneksi->prepare(
                "INSERT INTO guru (nama, nip, jabatan, mata_pelajaran, foto) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nama, $nip ?: null, $jabatan, $mapel ?: null, $hasilUnggah['namaFile']]);
            header('Location: guru.php?status=ditambahkan');
            exit;
        }
    }
}

$judul_halaman = 'Tambah Guru';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
  <div class="pesan-status pesan-gagal"><?= htmlspecialchars($statusPesan) ?></div>
<?php endif; ?>

<div class="kartu-panel">
  <form method="post" action="guru_tambah.php" enctype="multipart/form-data">
    <div class="pratinjau-foto" id="pratinjau"></div>
    <div class="grup-form">
      <label for="foto">Foto</label>
      <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" data-preview="#pratinjau" onchange="pratinjauGambar(this,'pratinjau')">
      <p class="bantuan-form">Opsional. Format JPG/PNG/WEBP, maksimal 3MB.</p>
    </div>
    <div class="grup-form">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required>
    </div>
    <div class="grup-form">
      <label for="nip">NIP</label>
      <input type="text" id="nip" name="nip">
    </div>
    <div class="grup-form">
      <label for="jabatan">Jabatan</label>
      <input type="text" id="jabatan" name="jabatan" placeholder="Contoh: Guru, Kepala Sekolah, Staf TU" required>
    </div>
    <div class="grup-form">
      <label for="mata_pelajaran">Mata Pelajaran</label>
      <input type="text" id="mata_pelajaran" name="mata_pelajaran" placeholder="Kosongkan jika bukan guru mapel">
    </div>
    <button type="submit" class="tombol">Simpan</button>
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
