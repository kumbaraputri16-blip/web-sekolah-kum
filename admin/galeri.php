<?php
require_once __DIR__ . '/auth.php';

$daftarGaleri = $koneksi->query("SELECT * FROM galeri ORDER BY id DESC")->fetchAll();

$judul_halaman = 'Galeri Foto';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if (!empty($_GET['status'])): ?>
  <div class="pesan-status pesan-berhasil">
    <?= $_GET['status'] === 'ditambahkan' ? 'Foto berhasil ditambahkan ke galeri.' : 'Foto berhasil dihapus.' ?>
  </div>
<?php endif; ?>

<div class="baris-header-panel">
  <h3 style="margin:0;">Galeri Foto Kegiatan</h3>
  <a href="galeri_tambah.php" class="tombol">+ Tambah Foto</a>
</div>

<div class="kartu-panel">
  <?php if ($daftarGaleri): ?>
  <table class="tabel-admin">
    <thead><tr><th>Foto</th><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($daftarGaleri as $g): ?>
      <tr>
        <td><img class="mini-foto" src="../uploads/galeri/<?= htmlspecialchars($g['foto']) ?>" alt=""></td>
        <td><?= htmlspecialchars($g['judul']) ?></td>
        <td><?= $g['tanggal'] ? date('d M Y', strtotime($g['tanggal'])) : '-' ?></td>
        <td>
          <a class="tombol tombol-bahaya tombol-kecil konfirmasi-hapus" href="galeri_hapus.php?id=<?= $g['id'] ?>">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p style="color:var(--teks-muda);">Belum ada foto pada galeri.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
