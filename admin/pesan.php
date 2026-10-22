<?php
require_once __DIR__ . '/auth.php';

// Tandai sudah dibaca ketika admin membuka detail pesan
if (!empty($_GET['baca'])) {
    $stmt = $koneksi->prepare("UPDATE pesan SET sudah_dibaca = 1 WHERE id = ?");
    $stmt->execute([(int)$_GET['baca']]);
    header('Location: pesan.php?dibuka=' . (int)$_GET['baca']);
    exit;
}

if (!empty($_GET['hapus'])) {
    $stmt = $koneksi->prepare("DELETE FROM pesan WHERE id = ?");
    $stmt->execute([(int)$_GET['hapus']]);
    header('Location: pesan.php');
    exit;
}

$daftarPesan = $koneksi->query("SELECT * FROM pesan ORDER BY dikirim_pada DESC")->fetchAll();

$pesanDibuka = null;
if (!empty($_GET['dibuka'])) {
    foreach ($daftarPesan as $p) {
        if ($p['id'] == $_GET['dibuka']) { $pesanDibuka = $p; break; }
    }
}

$judul_halaman = 'Pesan Masuk';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if ($pesanDibuka): ?>
<div class="kartu-panel">
  <div class="baris-header-panel">
    <h3 style="margin:0;"><?= htmlspecialchars($pesanDibuka['subjek'] ?: '(Tanpa subjek)') ?></h3>
    <a href="pesan.php?hapus=<?= $pesanDibuka['id'] ?>" class="tombol tombol-bahaya tombol-kecil konfirmasi-hapus">Hapus Pesan</a>
  </div>
  <p style="color:var(--teks-muda);margin-bottom:4px;">
    Dari <strong><?= htmlspecialchars($pesanDibuka['nama']) ?></strong> (<?= htmlspecialchars($pesanDibuka['email']) ?>)
  </p>
  <p style="color:var(--teks-muda);font-size:0.85rem;">
    Dikirim <?= date('d F Y H:i', strtotime($pesanDibuka['dikirim_pada'])) ?>
  </p>
  <hr style="border:none;border-top:1px solid var(--garis);margin:18px 0;">
  <p><?= nl2br(htmlspecialchars($pesanDibuka['isi_pesan'])) ?></p>
  <a href="mailto:<?= htmlspecialchars($pesanDibuka['email']) ?>" class="tombol tombol-kecil">Balas via Email</a>
</div>
<?php endif; ?>

<div class="kartu-panel">
  <h3 style="margin-top:0;">Semua Pesan</h3>
  <?php if ($daftarPesan): ?>
  <table class="tabel-admin">
    <thead><tr><th>Status</th><th>Nama</th><th>Subjek</th><th>Tanggal</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($daftarPesan as $p): ?>
      <tr>
        <td><?= $p['sudah_dibaca'] ? '<span class="lencana">Dibaca</span>' : '<span class="lencana" style="background:#FBEED9;color:#8A5A16;">Baru</span>' ?></td>
        <td><?= htmlspecialchars($p['nama']) ?></td>
        <td><?= htmlspecialchars($p['subjek'] ?: '(tanpa subjek)') ?></td>
        <td><?= date('d M Y H:i', strtotime($p['dikirim_pada'])) ?></td>
        <td>
          <a class="tombol tombol-garis tombol-kecil" href="pesan.php?baca=<?= $p['id'] ?>">Buka</a>
          <a class="tombol tombol-bahaya tombol-kecil konfirmasi-hapus" href="pesan.php?hapus=<?= $p['id'] ?>">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p style="color:var(--teks-muda);">Belum ada pesan masuk dari pengunjung.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
