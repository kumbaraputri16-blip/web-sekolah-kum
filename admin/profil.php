<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/unggah_foto.php';

$stmt = $koneksi->query("SELECT * FROM profil_sekolah LIMIT 1");
$profil = $stmt->fetch();

$statusPesan = '';
$statusTipe  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_sekolah    = trim($_POST['nama_sekolah'] ?? '');
    $npsn            = trim($_POST['npsn'] ?? '');
    $alamat          = trim($_POST['alamat'] ?? '');
    $telepon         = trim($_POST['telepon'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $jam_operasional = trim($_POST['jam_operasional'] ?? '');
    $sejarah         = trim($_POST['sejarah'] ?? '');
    $visi            = trim($_POST['visi'] ?? '');
    $misi            = trim($_POST['misi'] ?? '');

    $hasilUnggah = tanganiUnggahFoto('logo', 'profil');
    // Simpan foto profil di folder uploads/profil (dibuat otomatis oleh helper)

    if (!$hasilUnggah['sukses']) {
        $statusPesan = $hasilUnggah['pesan'];
        $statusTipe  = 'gagal';
    } else {
        $logo = $profil['logo'] ?? null;
        if ($hasilUnggah['namaFile']) {
            hapusFotoLama($profil['logo'] ?? null, 'profil');
            $logo = $hasilUnggah['namaFile'];
        }

        $stmt = $koneksi->prepare(
            "UPDATE profil_sekolah SET nama_sekolah=?, npsn=?, alamat=?, telepon=?, email=?,
             jam_operasional=?, sejarah=?, visi=?, misi=?, logo=? WHERE id=?"
        );
        $stmt->execute([$nama_sekolah, $npsn, $alamat, $telepon, $email, $jam_operasional,
                         $sejarah, $visi, $misi, $logo, $profil['id']]);

        $statusPesan = 'Profil sekolah berhasil diperbarui.';
        $statusTipe  = 'berhasil';

        $stmt = $koneksi->query("SELECT * FROM profil_sekolah LIMIT 1");
        $profil = $stmt->fetch();
    }
}

$judul_halaman = 'Profil Sekolah';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($statusPesan): ?>
  <div class="pesan-status pesan-<?= $statusTipe ?>"><?= htmlspecialchars($statusPesan) ?></div>
<?php endif; ?>

<div class="kartu-panel">
  <form method="post" action="profil.php" enctype="multipart/form-data">

    <?php if (!empty($profil['logo'])): ?>
    <div class="pratinjau-foto" id="pratinjau-logo" style="background-image:url('../uploads/profil/<?= htmlspecialchars($profil['logo']) ?>')"></div>
    <?php else: ?>
    <div class="pratinjau-foto" id="pratinjau-logo"></div>
    <?php endif; ?>

    <div class="grup-form">
      <label for="logo">Logo Sekolah</label>
      <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp"
             onchange="pratinjauGambar(this,'pratinjau-logo')">
      <p class="bantuan-form">Format JPG/PNG/WEBP, maksimal 3MB. Kosongkan jika tidak ingin mengubah logo.</p>
    </div>

    <div class="grup-form">
      <label for="nama_sekolah">Nama Sekolah</label>
      <input type="text" id="nama_sekolah" name="nama_sekolah" value="<?= htmlspecialchars($profil['nama_sekolah'] ?? '') ?>" required>
    </div>

    <div class="grup-form">
      <label for="npsn">NPSN</label>
      <input type="text" id="npsn" name="npsn" value="<?= htmlspecialchars($profil['npsn'] ?? '') ?>">
    </div>

    <div class="grup-form">
      <label for="alamat">Alamat</label>
      <textarea id="alamat" name="alamat"><?= htmlspecialchars($profil['alamat'] ?? '') ?></textarea>
    </div>

    <div class="grup-form">
      <label for="telepon">Telepon</label>
      <input type="text" id="telepon" name="telepon" value="<?= htmlspecialchars($profil['telepon'] ?? '') ?>">
    </div>

    <div class="grup-form">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
    </div>

    <div class="grup-form">
      <label for="jam_operasional">Jam Operasional</label>
      <input type="text" id="jam_operasional" name="jam_operasional" value="<?= htmlspecialchars($profil['jam_operasional'] ?? '') ?>">
    </div>

    <div class="grup-form">
      <label for="sejarah">Sejarah Singkat</label>
      <textarea id="sejarah" name="sejarah" style="min-height:150px;"><?= htmlspecialchars($profil['sejarah'] ?? '') ?></textarea>
    </div>

    <div class="grup-form">
      <label for="visi">Visi</label>
      <textarea id="visi" name="visi"><?= htmlspecialchars($profil['visi'] ?? '') ?></textarea>
    </div>

    <div class="grup-form">
      <label for="misi">Misi</label>
      <textarea id="misi" name="misi" style="min-height:150px;"><?= htmlspecialchars($profil['misi'] ?? '') ?></textarea>
      <p class="bantuan-form">Tulis satu poin misi per baris, contoh: "1. Menyelenggarakan..."</p>
    </div>

    <button type="submit" class="tombol">Simpan Perubahan</button>
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
