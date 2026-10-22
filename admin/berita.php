<?php
require_once __DIR__ . '/auth.php';

$daftarBerita = $koneksi->query("SELECT * FROM berita ORDER BY tanggal DESC")->fetchAll();

$judul_halaman = 'Berita';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if (!empty($_GET['status'])): ?>
  <div class="pesan-status pesan-berhasil">
    <?= $_GET['status'] === 'ditambahkan' ? 'Berita berhasil dipublikasikan.' :
        ($_GET['status'] === 'diubah' ? 'Berita berhasil diperbarui.' : 'Berita berhasil dihapus.') ?>
  </div>
<?php endif; ?>

<div class="baris-header-panel">
  <h3 style="margin:0;">Daftar Berita</h3>
  <a href="berita_tambah.php" class="tombol">+ Tulis Berita</a>
</div>

<div class="kartu-panel">
  <?php if ($daftarBerita): ?>
  <table class="tabel-admin">
    <thead><tr><th>Foto</th><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($daftarBerita as $b): ?>
      <tr>
        <td><img class="mini-foto" src="<?= $b['foto'] ? '../uploads/berita/'.htmlspecialchars($b['foto']) : 'https://placehold.co/48x48/EDE6D6/1C2B39?text=B' ?>" alt=""></td>
        <td><?= htmlspecialchars($b['judul']) ?></td>
        <td><?= date('d M Y', strtotime($b['tanggal'])) ?></td>
        <td>
          <a class="tombol tombol-garis tombol-kecil" href="berita_edit.php?id=<?= $b['id'] ?>">Edit</a>
          <a class="tombol tombol-bahaya tombol-kecil konfirmasi-hapus" href="berita_hapus.php?id=<?= $b['id'] ?>">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p style="color:var(--teks-muda);">Belum ada berita. Klik "Tulis Berita" untuk membuat yang pertama.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
