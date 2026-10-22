<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Pembayaran PPDB';
$pesan = '';
$jenisPesan = '';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Ambil data pendaftaran berdasarkan nomor pendaftaran
$nomor = trim($_GET['nomor'] ?? $_POST['nomor_pendaftaran'] ?? '');
$siswa = null;

if ($nomor !== '') {
    $stmt = $koneksi->prepare(
        "SELECT id, nomor_pendaftaran, nama_lengkap, jurusan
         FROM pendaftaran
         WHERE nomor_pendaftaran = :nomor
         LIMIT 1"
    );

    $stmt->execute(['nomor' => $nomor]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
}

// CSRF token
if (empty($_SESSION['csrf_pembayaran'])) {
    $_SESSION['csrf_pembayaran'] = bin2hex(random_bytes(32));
}

// Proses formulir pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_pembayaran'])) {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_pembayaran'], $token)
    ) {
        $pesan = 'Permintaan tidak valid. Silakan muat ulang halaman.';
        $jenisPesan = 'error';
    } elseif (!$siswa) {
        $pesan = 'Nomor pendaftaran tidak ditemukan.';
        $jenisPesan = 'error';
    } else {
        $metode = $_POST['metode_pembayaran'] ?? '';

        $metodeValid = [
            'Transfer Bank',
            'QRIS (Simulasi)',
            'Tunai (Simulasi)'
        ];

        if (!in_array($metode, $metodeValid, true)) {
            $pesan = 'Silakan pilih metode pembayaran yang tersedia.';
            $jenisPesan = 'error';
        } else {
            try {
                // Cegah pembuatan pembayaran berulang yang belum diverifikasi
                $cek = $koneksi->prepare(
                    "SELECT id
                     FROM pembayaran_ppdb
                     WHERE pendaftaran_id = :pendaftaran_id
                       AND status IN ('Belum Bayar', 'Menunggu Verifikasi')
                     LIMIT 1"
                );

                $cek->execute([
                    'pendaftaran_id' => $siswa['id']
                ]);

                if ($cek->fetch()) {
                    $pesan = 'Pembayaran untuk pendaftaran ini sudah tercatat dan sedang menunggu proses. Silakan hubungi admin jika ingin mengubahnya.';
                    $jenisPesan = 'error';
                } else {
                    // Nominal simulasi, bukan transaksi uang sungguhan
                    $nominal = 150000;

                    $insert = $koneksi->prepare(
                        "INSERT INTO pembayaran_ppdb
                        (
                            pendaftaran_id,
                            nomor_pendaftaran,
                            nama_siswa,
                            jenis_pembayaran,
                            nominal,
                            metode_pembayaran,
                            status,
                            tanggal_pembayaran
                        )
                        VALUES
                        (
                            :pendaftaran_id,
                            :nomor_pendaftaran,
                            :nama_siswa,
                            :jenis_pembayaran,
                            :nominal,
                            :metode_pembayaran,
                            'Menunggu Verifikasi',
                            NOW()
                        )"
                    );

                    $insert->execute([
                        'pendaftaran_id' => $siswa['id'],
                        'nomor_pendaftaran' => $siswa['nomor_pendaftaran'],
                        'nama_siswa' => $siswa['nama_lengkap'],
                        'jenis_pembayaran' => 'Biaya Pendaftaran PPDB (Simulasi)',
                        'nominal' => $nominal,
                        'metode_pembayaran' => $metode
                    ]);

                    $idPembayaran = $koneksi->lastInsertId();

                    // Simpan akses bukti di sesi browser ini
                    $_SESSION['bukti_pembayaran'][$idPembayaran] = bin2hex(random_bytes(32));

                    header(
                        'Location: ' .
                        (defined('BASE_URL') ? BASE_URL : '') .
                        '/status-pembayaran.php?id=' .
                        urlencode((string) $idPembayaran)
                    );
                    exit;
                }
            } catch (PDOException $ex) {
                $pesan = 'Pembayaran gagal disimpan. Periksa struktur tabel database atau hubungi admin.';
                $jenisPesan = 'error';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<style>
    .bayar-container {
        max-width: 850px;
        margin: 40px auto;
        padding: 20px;
    }

    .bayar-heading {
        text-align: center;
        margin-bottom: 25px;
    }

    .bayar-heading h1 {
        font-size: 30px;
        color: #166534;
        margin-bottom: 10px;
    }

    .bayar-heading p {
        color: #6b7280;
        line-height: 1.6;
    }

    .bayar-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
    }

    .bayar-card h2 {
        font-size: 19px;
        margin: 0 0 18px;
        color: #166534;
    }

    .data-siswa {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .data-item {
        padding: 13px;
        background: #f9fafb;
        border-radius: 8px;
        overflow-wrap: anywhere;
    }

    .data-item span {
        display: block;
        color: #6b7280;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .data-item strong {
        color: #111827;
        font-size: 14px;
    }

    .biaya-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 13px 0;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }

    .biaya-total {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding-top: 18px;
        font-size: 19px;
        font-weight: bold;
        color: #166534;
    }

    .label-simulasi {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 12px;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
    }

    .btn-bayar {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 8px;
        background: #166534;
        color: white;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-bayar:hover {
        background: #14532d;
    }

    .pesan {
        padding: 13px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 14px;
        line-height: 1.5;
    }

    .pesan.error {
        background: #fee2e2;
        color: #991b1b;
    }

    .pesan.sukses {
        background: #dcfce7;
        color: #166534;
    }

    .info-box {
        padding: 15px;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.7;
    }

    @media (max-width: 600px) {
        .bayar-container {
            margin: 20px auto;
            padding: 12px;
        }

        .bayar-heading h1 {
            font-size: 24px;
        }

        .bayar-card {
            padding: 18px;
        }

        .data-siswa {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="bayar-container">

    <div class="bayar-heading">
        <h1>Pembayaran PPDB</h1>
        <p>
            Periksa data pendaftaran dan informasi biaya sebelum melanjutkan.
        </p>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="pesan <?= e($jenisPesan) ?>">
            <?= e($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if (!$siswa): ?>

        <div class="bayar-card">
            <h2>Cari Data Pendaftaran</h2>

            <form method="GET" action="pembayaran-ppdb.php">
                <div class="form-group">
                    <label for="nomor">Nomor Pendaftaran</label>
                    <input
                        type="text"
                        id="nomor"
                        name="nomor"
                        placeholder="Contoh: PPDB-2026-0001"
                        value="<?= e($nomor) ?>"
                        required
                    >
                </div>

                <button class="btn-bayar" type="submit">
                    Cari Pendaftaran
                </button>
            </form>
        </div>

    <?php else: ?>

        <div class="bayar-card">
            <h2>Data Calon Siswa</h2>

            <div class="data-siswa">
                <div class="data-item">
                    <span>Nomor Pendaftaran</span>
                    <strong><?= e($siswa['nomor_pendaftaran']) ?></strong>
                </div>

                <div class="data-item">
                    <span>Nama Lengkap</span>
                    <strong><?= e($siswa['nama_lengkap']) ?></strong>
                </div>

                <div class="data-item">
                    <span>Jurusan Pilihan</span>
                    <strong><?= e($siswa['jurusan']) ?></strong>
                </div>
            </div>
        </div>

        <div class="bayar-card">
            <h2>Rincian Biaya</h2>

            <span class="label-simulasi">SIMULASI PROYEK SEKOLAH</span>

            <div class="biaya-row">
                <span>Biaya pendaftaran PPDB</span>
                <strong>Rp150.000</strong>
            </div>

            <div class="biaya-total">
                <span>Total</span>
                <span>Rp150.000</span>
            </div>

            <p class="info-box">
                Nominal di atas hanya contoh untuk simulasi proyek sekolah.
                Sesuaikan dengan biaya resmi yang ditetapkan sekolah.
                Halaman ini belum memproses transaksi uang sungguhan.
            </p>
        </div>

        <div class="bayar-card">
            <h2>Pilih Metode Pembayaran</h2>

            <form method="POST" action="pembayaran-ppdb.php">
                <input
                    type="hidden"
                    name="nomor_pendaftaran"
                    value="<?= e($siswa['nomor_pendaftaran']) ?>"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_pembayaran']) ?>"
                >

                <div class="form-group">
                    <label for="metode_pembayaran">
                        Metode Pembayaran
                    </label>

                    <select
                        name="metode_pembayaran"
                        id="metode_pembayaran"
                        required
                    >
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank">
                            Transfer Bank (Simulasi)
                        </option>
                        <option value="QRIS (Simulasi)">
                            QRIS (Simulasi)
                        </option>
                        <option value="Tunai (Simulasi)">
                            Tunai (Simulasi)
                        </option>
                    </select>
                </div>

                <button
                    type="submit"
                    name="kirim_pembayaran"
                    class="btn-bayar"
                >
                    Lanjutkan Pembayaran
                </button>
            </form>
        </div>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>