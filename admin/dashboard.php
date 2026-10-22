<?php
require_once __DIR__ . '/auth.php';

// =============================================
// STATISTIK WEBSITE
// =============================================
$jumlahGuru = (int) $koneksi
    ->query("SELECT COUNT(*) FROM guru")
    ->fetchColumn();

$jumlahGaleri = (int) $koneksi
    ->query("SELECT COUNT(*) FROM galeri")
    ->fetchColumn();

$jumlahBerita = (int) $koneksi
    ->query("SELECT COUNT(*) FROM berita")
    ->fetchColumn();

$jumlahPesan = (int) $koneksi
    ->query("SELECT COUNT(*) FROM pesan WHERE sudah_dibaca = 0")
    ->fetchColumn();

// =============================================
// STATISTIK PPDB
// =============================================
$jumlahPendaftar = (int) $koneksi
    ->query("SELECT COUNT(*) FROM pendaftaran")
    ->fetchColumn();

// Ambil jumlah pendaftar berdasarkan jurusan
$stmtJurusan = $koneksi->query("
    SELECT jurusan, COUNT(*) AS jumlah
    FROM pendaftaran
    GROUP BY jurusan
");

$dataJurusan = [];

foreach ($stmtJurusan->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $dataJurusan[$row['jurusan']] = (int) $row['jumlah'];
}

// Urutan jurusan pada dashboard
$daftarJurusan = [
    'DKV'  => 'Desain Komunikasi Visual',
    'PSPT' => 'Produksi dan Siaran Program Televisi',
    'RPL'  => 'Rekayasa Perangkat Lunak',
    'TKJ'  => 'Teknik Komputer dan Jaringan'
];

// Warna masing-masing jurusan
$warnaJurusan = [
    'DKV'  => '#7c3aed',
    'PSPT' => '#ea580c',
    'RPL'  => '#2563eb',
    'TKJ'  => '#059669'
];

// Hitung persentase setiap jurusan
$totalJurusan = 0;

foreach ($daftarJurusan as $kode => $nama) {
    $totalJurusan += $dataJurusan[$kode] ?? 0;
}

// =============================================
// PESAN TERBARU
// =============================================
$pesanTerbaru = $koneksi->query("
    SELECT *
    FROM pesan
    ORDER BY dikirim_pada DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$judul_halaman = 'Dasbor';

require_once __DIR__ . '/includes/layout_atas.php';
?>

<style>
/* Statistik PPDB */
.ppdb-dashboard {
    margin-top: 28px;
}

.ppdb-dashboard h2 {
    margin: 0 0 6px;
    font-size: 21px;
}

.ppdb-dashboard .deskripsi {
    color: var(--teks-muda);
    margin: 0 0 20px;
    line-height: 1.6;
}

.ppdb-total {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: white;
    padding: 24px;
    border-radius: 14px;
    margin-bottom: 20px;
    box-shadow: 0 8px 22px rgba(37, 99, 235, .18);
}

.ppdb-total .label {
    display: block;
    font-size: 14px;
    opacity: .9;
}

.ppdb-total .angka {
    display: block;
    font-size: 38px;
    font-weight: 800;
    margin-top: 8px;
}

.ppdb-total .keterangan {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    opacity: .9;
}

.ppdb-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.ppdb-kartu {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    min-width: 0;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    transition: transform .2s ease, box-shadow .2s ease;
}

.ppdb-kartu:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 22px rgba(15, 23, 42, .09);
}

.ppdb-kartu-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 18px;
}

.ppdb-kode {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 7px;
    color: white;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .5px;
}

.ppdb-persen {
    font-size: 18px;
    font-weight: 800;
    color: #1f2937;
}

.ppdb-nama {
    color: #4b5563;
    font-size: 13px;
    line-height: 1.5;
    min-height: 40px;
    margin-bottom: 10px;
}

.ppdb-jumlah {
    font-size: 30px;
    font-weight: 800;
    color: #111827;
}

.ppdb-jumlah small {
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
}

.ppdb-progress {
    height: 9px;
    background: #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
    margin-top: 15px;
}

.ppdb-progress-bar {
    height: 100%;
    width: 0;
    border-radius: 20px;
    animation: isiProgress 1s ease forwards;
}

@keyframes isiProgress {
    from {
        width: 0;
    }
}

.ppdb-catatan {
    font-size: 12px;
    color: var(--teks-muda);
    margin-top: 18px;
    line-height: 1.6;
}

@media (max-width: 700px) {
    .ppdb-grid {
        grid-template-columns: 1fr;
    }

    .ppdb-total .angka {
        font-size: 32px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .ppdb-kartu,
    .ppdb-progress-bar {
        animation: none;
        transition: none;
    }

    .ppdb-kartu:hover {
        transform: none;
    }
}
</style>

<p style="color:var(--teks-muda);margin-top:0;">
    Selamat datang kembali,
    <strong>
        <?= htmlspecialchars(
            $_SESSION['admin_nama'] ?? 'Admin',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </strong>.
</p>

<!-- STATISTIK WEBSITE -->
<div class="grid-statistik">

    <div class="kotak-statistik">
        <span class="angka"><?= $jumlahGuru ?></span>
        <span class="label">Guru &amp; Staf</span>
    </div>

    <div class="kotak-statistik">
        <span class="angka"><?= $jumlahGaleri ?></span>
        <span class="label">Foto Galeri</span>
    </div>

    <div class="kotak-statistik">
        <span class="angka"><?= $jumlahBerita ?></span>
        <span class="label">Berita Terbit</span>
    </div>

    <div class="kotak-statistik">
        <span class="angka"><?= $jumlahPesan ?></span>
        <span class="label">Pesan Belum Dibaca</span>
    </div>

</div>

<!-- STATISTIK PPDB -->
<section class="ppdb-dashboard">

    <h2>Statistik PPDB</h2>

    <p class="deskripsi">
        Ringkasan jumlah pendaftar berdasarkan program keahlian.
    </p>

    <div class="ppdb-total">
        <span class="label">Total Seluruh Pendaftar</span>

        <span class="angka">
            <?= number_format($jumlahPendaftar, 0, ',', '.') ?>
        </span>

        <span class="keterangan">
            Semua data yang tersimpan dalam database pendaftaran.
        </span>
    </div>

    <div class="ppdb-grid">

        <?php foreach ($daftarJurusan as $kode => $nama): ?>

            <?php
            $jumlah = $dataJurusan[$kode] ?? 0;

            $persentase = $jumlahPendaftar > 0
                ? ($jumlah / $jumlahPendaftar) * 100
                : 0;

            $warna = $warnaJurusan[$kode];
            ?>

            <article class="ppdb-kartu">

                <div class="ppdb-kartu-header">

                    <span
                        class="ppdb-kode"
                        style="background:<?= $warna ?>;"
                    >
                        <?= htmlspecialchars($kode) ?>
                    </span>

                    <span class="ppdb-persen">
                        <?= number_format($persentase, 1, ',', '.') ?>%
                    </span>

                </div>

                <div class="ppdb-nama">
                    <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="ppdb-jumlah">
                    <?= number_format($jumlah, 0, ',', '.') ?>
                    <small>pendaftar</small>
                </div>

                <div
                    class="ppdb-progress"
                    role="progressbar"
                    aria-label="Persentase pendaftar <?= htmlspecialchars($kode) ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="<?= round($persentase, 1) ?>"
                >
                    <div
                        class="ppdb-progress-bar"
                        style="
                            width:<?= min(100, $persentase) ?>%;
                            background:<?= $warna ?>;
                        "
                    ></div>
                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <p class="ppdb-catatan">
        Persentase dihitung dari jumlah pendaftar setiap jurusan
        dibandingkan dengan total seluruh pendaftar. Data diperbarui
        secara otomatis ketika halaman dashboard dibuka.
    </p>

</section>

<!-- PESAN TERBARU -->
<div class="kartu-panel" style="margin-top:28px;">

    <div class="baris-header-panel">

        <h3 style="margin:0;">Pesan Masuk Terbaru</h3>

        <a
            href="pesan.php"
            class="tombol tombol-garis tombol-kecil"
        >
            Lihat Semua
        </a>

    </div>

    <?php if ($pesanTerbaru): ?>

        <table class="tabel-admin">

            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Subjek</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($pesanTerbaru as $p): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                $p['nama'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $p['subjek'] ?: '(tanpa subjek)',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= date(
                                'd M Y H:i',
                                strtotime($p['dikirim_pada'])
                            ) ?>
                        </td>

                        <td>
                            <?php if ($p['sudah_dibaca']): ?>

                                <span class="lencana">Dibaca</span>

                            <?php else: ?>

                                <span
                                    class="lencana"
                                    style="background:#FBEED9;color:#8A5A16;"
                                >
                                    Baru
                                </span>

                            <?php endif; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>

        <p style="color:var(--teks-muda);">
            Belum ada pesan masuk.
        </p>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
```
