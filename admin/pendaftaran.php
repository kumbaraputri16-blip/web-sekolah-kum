<?php
require_once __DIR__ . '/auth.php';

$judul_halaman = 'Data Pendaftaran PPDB';

// Ambil semua data pendaftar
$stmt = $koneksi->query("
    SELECT
        id,
        nomor_pendaftaran,
        nama_lengkap,
        nik,
        no_whatsapp,
        jurusan,
        tanggal_daftar,
        status
    FROM pendaftaran
    ORDER BY tanggal_daftar DESC
");

$pendaftar = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/layout_atas.php';

function h($nilai)
{
    return htmlspecialchars(
        (string) ($nilai ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<style>
.ppdb-admin {
    margin-top: 20px;
}

.ppdb-admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.ppdb-admin-header h2 {
    margin: 0 0 6px;
}

.ppdb-admin-header p {
    margin: 0;
    color: var(--teks-muda);
    font-size: 13px;
}

.ppdb-admin-info {
    background: #eff6ff;
    color: #1d4ed8;
    padding: 14px 16px;
    border-radius: 9px;
    font-size: 13px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.ppdb-tabel-wrapper {
    overflow-x: auto;
}

.ppdb-tabel {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.ppdb-tabel th,
.ppdb-tabel td {
    padding: 12px 10px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    font-size: 12px;
    vertical-align: middle;
}

.ppdb-tabel th {
    background: #f8fafc;
    color: #374151;
    font-weight: 700;
}

.ppdb-tabel tr:hover td {
    background: #f9fafb;
}

.ppdb-status {
    display: inline-block;
    padding: 5px 9px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 20px;
    font-size: 11px;
    white-space: nowrap;
}

.ppdb-status.status-diterima {
    background: #dcfce7;
    color: #166534;
}

.ppdb-status.status-ditolak {
    background: #fee2e2;
    color: #991b1b;
}

.ppdb-aksi {
    display: inline-block;
    padding: 8px 11px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    background: #2563eb;
    color: white;
    white-space: nowrap;
}

.ppdb-aksi:hover {
    background: #1d4ed8;
    color: white;
}

.ppdb-kosong {
    text-align: center;
    padding: 40px 15px;
    color: var(--teks-muda);
}

.ppdb-total {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 7px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 12px;
    font-weight: 700;
}
</style>

<div class="ppdb-admin">

    <div class="ppdb-admin-header">

        <div>
            <h2>Data Pendaftaran PPDB</h2>

            <p>
                Lihat data calon siswa dan cetak bukti pendaftaran.
            </p>
        </div>

        <span class="ppdb-total">
            Total: <?= count($pendaftar) ?> pendaftar
        </span>

    </div>

    <div class="ppdb-admin-info">
        <strong>Informasi:</strong>
        Klik tombol <strong>Cetak PDF</strong> untuk membuka
        bukti pendaftaran siswa. Kamu dapat mencetak atau menyimpannya
        sebagai PDF melalui browser.
    </div>

    <div class="kartu-panel">

        <?php if (!empty($pendaftar)): ?>

            <div class="ppdb-tabel-wrapper">

                <table class="ppdb-tabel">

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama Siswa</th>
                            <th>Jurusan</th>
                            <th>WhatsApp</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($pendaftar as $i => $p): ?>

                            <?php
                            $status = strtolower(
                                trim($p['status'] ?? '')
                            );

                            $kelasStatus = '';

                            if ($status === 'diterima') {
                                $kelasStatus = 'status-diterima';
                            } elseif ($status === 'ditolak') {
                                $kelasStatus = 'status-ditolak';
                            }
                            ?>

                            <tr>

                                <td><?= $i + 1 ?></td>

                                <td>
                                    <strong>
                                        <?= h($p['nomor_pendaftaran']) ?>
                                    </strong>
                                </td>

                                <td><?= h($p['nama_lengkap']) ?></td>

                                <td><?= h($p['jurusan']) ?></td>

                                <td><?= h($p['no_whatsapp']) ?></td>

                                <td>
                                    <?php
                                    echo !empty($p['tanggal_daftar'])
                                        ? date(
                                            'd-m-Y H:i',
                                            strtotime($p['tanggal_daftar'])
                                        )
                                        : '-';
                                    ?>
                                </td>

                                <td>
                                    <span class="ppdb-status <?= h($kelasStatus) ?>">
                                        <?= h($p['status']) ?>
                                    </span>
                                </td>

                                <td>
                                   
<a
    class="ppdb-aksi"
    href="<?= BASE_URL ?>/admin/cetak-pendaftaran.php?id=<?= (int) $p['id'] ?>"
    target="_blank"
    rel="noopener"
    onclick="return konfirmasiCetak(this.href)"
>
    Cetak PDF
</a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="ppdb-kosong">

                <h3>Belum Ada Pendaftar</h3>

                <p>
                    Data calon siswa akan muncul di halaman ini
                    setelah formulir PPDB dikirim.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

<script>
function konfirmasiCetak(url) {
    alert(
        'Bukti pendaftaran akan dibuka di tab baru. ' +
        'Untuk menyimpan PDF, klik Cetak / Simpan PDF.'
    );

    return true;
}
</script>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>
