<?php
require_once __DIR__ . '/auth.php';

$daftarGuru = $koneksi->query("SELECT * FROM guru ORDER BY id DESC")->fetchAll();

$judul_halaman = 'Guru & Staf';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if (!empty($_GET['status'])): ?>
  <div class="pesan-status pesan-berhasil">
    <?= $_GET['status'] === 'ditambahkan' ? 'Data guru berhasil ditambahkan.' :
        ($_GET['status'] === 'diubah' ? 'Data guru berhasil diperbarui.' : 'Data guru berhasil dihapus.') ?>
  </div>
<?php endif; ?>

<!-- NAVIGASI PENGELOLAAN -->
<div class="kartu-panel" style="margin-bottom:20px;">

    <h3 style="margin-top:0;">Pengelolaan Sekolah</h3>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">

        <a
            href="<?= BASE_URL ?>/admin/guru.php"
            class="tombol"
        >
            👨‍🏫 Data Guru &amp; Staf
        </a>

        <a
            href="<?= BASE_URL ?>/admin/struktur-sekolah.php"
            class="tombol tombol-garis"
        >
            🏫 Struktur Sekolah
        </a>

    </div>

</div>

<!-- HEADER DAFTAR GURU -->
<div class="baris-header-panel">

    <h3 style="margin:0;">Daftar Guru &amp; Staf</h3>

    <a href="<?= BASE_URL ?>/admin/guru_tambah.php" class="tombol">
        + Tambah Guru
    </a>

</div>

<div class="kartu-panel">
  <?php if ($daftarGuru): ?>
  <table class="tabel-admin">
    <thead><tr><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Mapel</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($daftarGuru as $g): ?>
      <tr>
        <td><img class="mini-foto" src="<?= $g['foto'] ? '../uploads/guru/'.htmlspecialchars($g['foto']) : 'https://placehold.co/48x48/EDE6D6/1C2B39?text='.urlencode(mb_substr($g['nama'],0,1)) ?>" alt=""></td>
        <td><?= htmlspecialchars($g['nama']) ?></td>
        <td><?= htmlspecialchars($g['jabatan']) ?></td>
        <td><?= htmlspecialchars($g['mata_pelajaran'] ?: '-') ?></td>
        <td>
          <a class="tombol tombol-garis tombol-kecil" href="guru_edit.php?id=<?= $g['id'] ?>">Edit</a>
          <a class="tombol tombol-bahaya tombol-kecil konfirmasi-hapus" href="guru_hapus.php?id=<?= $g['id'] ?>">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p style="color:var(--teks-muda);">Belum ada data guru. Klik "Tambah Guru" untuk menambahkan.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
