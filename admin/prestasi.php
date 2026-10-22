<?php
require_once __DIR__ . '/auth.php';

$daftarPrestasi = $koneksi->query("
    SELECT *
    FROM foto_prestasi
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$judul_halaman = 'Foto Prestasi & Kegiatan';
require_once __DIR__ . '/includes/layout_atas.php';
?>

<?php if (isset($_GET['status'])): ?>
    <div class="pesan-status pesan-berhasil">
        <?= htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="baris-header-panel">
    <h3 style="margin:0;">Foto Prestasi & Kegiatan Sekolah</h3>

    <a href="prestasi_tambah.php" class="tombol">
        + Tambah Foto
    </a>
</div>

<div class="kartu-panel">

    <?php if (!empty($daftarPrestasi)): ?>

        <table class="tabel-admin">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($daftarPrestasi as $p): ?>
                    <tr>
                        <td>
                            <img
                                class="mini-foto"
                                src="../uploads/prestasi/<?= rawurlencode(basename($p['foto'])) ?>"
                                alt="<?= htmlspecialchars($p['judul'], ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </td>

                        <td>
                            <?= htmlspecialchars($p['judul'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= !empty($p['tanggal'])
                                ? date('d M Y', strtotime($p['tanggal']))
                                : '-' ?>
                        </td>

                        <td>
                            <a
                                class="tombol tombol-kecil"
                                href="prestasi_edit.php?id=<?= (int) $p['id'] ?>"
                            >
                                Edit
                            </a>

                            <a
                                class="tombol tombol-bahaya tombol-kecil konfirmasi-hapus"
                                href="prestasi_hapus.php?id=<?= (int) $p['id'] ?>"
                            >
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p style="color:var(--teks-muda);">
            Belum ada foto prestasi atau kegiatan sekolah.
        </p>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>