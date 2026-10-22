<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/koneksi.php';

// Pastikan ada ID dan token bukti pendaftaran
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$token = $_GET['token'] ?? '';

if (!$id || $id < 1 || !is_string($token) || $token === '') {
    http_response_code(403);
    exit('Akses bukti pendaftaran tidak valid.');
}

// Pastikan token cocok dengan token yang dibuat saat mendaftar
$tokenTersimpan = $_SESSION['token_ppdb_' . $id] ?? '';

if (
    !is_string($tokenTersimpan) ||
    $tokenTersimpan === '' ||
    !hash_equals($tokenTersimpan, $token)
) {
    http_response_code(403);
    exit('Bukti pendaftaran tidak dapat diakses. Silakan gunakan tautan setelah mengirim formulir.');
}

// Ambil data pendaftaran dari database
$stmt = $koneksi->prepare(
    "SELECT
        nomor_pendaftaran,
        nama_lengkap,
        nik,
        tempat_lahir,
        tanggal_lahir,
        jenis_kelamin,
        agama,
        alamat,
        asal_sekolah,
        tahun_lulus,
        jurusan,
        nama_orang_tua,
        no_whatsapp,
        tanggal_daftar,
        status
    FROM pendaftaran
    WHERE id = :id
    LIMIT 1"
);

$stmt->execute(['id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    http_response_code(404);
    exit('Data pendaftaran tidak ditemukan.');
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$tanggalLahir = date(
    'd-m-Y',
    strtotime($data['tanggal_lahir'])
);

$tanggalDaftar = date(
    'd-m-Y H:i',
    strtotime($data['tanggal_daftar'])
);

$judul_halaman = 'Bukti Pendaftaran PPDB';
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<style>
    * {
        box-sizing: border-box;
    }

    .bukti-wrapper {
        max-width: 750px;
        margin: 25px auto;
        padding: 12px;
    }

    .bukti-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
    }

    .bukti-header {
        background: #166534;
        color: white;
        padding: 15px;
        text-align: center;
    }

    .bukti-header .icon {
        font-size: 25px;
        margin-bottom: 3px;
    }

    .bukti-header h1 {
        font-size: 18px;
        margin: 0 0 5px;
    }

    .bukti-header p {
        font-size: 11px;
        margin: 3px 0;
    }

    .bukti-body {
        padding: 18px;
    }

    .status-box {
        padding: 8px 12px;
        background: #fef3c7;
        color: #92400e;
        border-radius: 6px;
        text-align: center;
        font-size: 11px;
        margin-bottom: 12px;
    }

    .status-box strong {
        display: block;
        margin-bottom: 3px;
    }

    .nomor-pendaftaran {
        background: #f0fdf4;
        border: 1px dashed #16a34a;
        border-radius: 7px;
        padding: 9px;
        text-align: center;
        margin-bottom: 12px;
    }

    .nomor-pendaftaran span {
        display: block;
        color: #166534;
        font-size: 10px;
        margin-bottom: 3px;
    }

    .nomor-pendaftaran strong {
        font-size: 16px;
        color: #14532d;
        overflow-wrap: anywhere;
    }

    .bukti-body h2 {
        font-size: 13px;
        margin: 14px 0 7px;
        color: #1f2937;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .data-table td {
        padding: 5px 7px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
        overflow-wrap: anywhere;
        font-size: 10px;
        line-height: 1.35;
    }

    .data-table td:first-child {
        width: 35%;
        color: #6b7280;
    }

    .data-table td:last-child {
        color: #111827;
        font-weight: 500;
    }

    .catatan {
        margin-top: 12px;
        padding: 9px 11px;
        background: #eff6ff;
        border-left: 3px solid #2563eb;
        border-radius: 5px;
        color: #1e40af;
        font-size: 9px;
        line-height: 1.45;
    }

    .tombol-wrapper {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .btn {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
    }

    .btn-cetak {
        background: #166534;
        color: white;
    }

    .btn-kembali {
        background: #f3f4f6;
        color: #374151;
    }

    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    @media print {
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }

        body {
            font-size: 10px;
        }

        .bukti-wrapper {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .bukti-card {
            border: 1px solid #ccc;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
        }

        .bukti-header {
            padding: 10px;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .bukti-header .icon {
            display: none;
        }

        .bukti-header h1 {
            font-size: 16px;
        }

        .bukti-header p {
            font-size: 10px;
        }

        .bukti-body {
            padding: 12px;
        }

        .status-box {
            padding: 6px;
            margin-bottom: 8px;
        }

        .nomor-pendaftaran {
            padding: 6px;
            margin-bottom: 8px;
        }

        .nomor-pendaftaran strong {
            font-size: 14px;
        }

        .bukti-body h2 {
            margin: 9px 0 5px;
            font-size: 12px;
        }

        .data-table td {
            padding: 4px 6px;
            font-size: 9px;
            line-height: 1.2;
        }

        .catatan {
            margin-top: 8px;
            padding: 7px;
            font-size: 8px;
            line-height: 1.3;
        }

        .tombol-wrapper,
        nav,
        header,
        footer {
            display: none !important;
        }

        .status-box,
        .nomor-pendaftaran,
        .catatan {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        tr,
        .status-box,
        .nomor-pendaftaran,
        .catatan {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>

<main class="bukti-wrapper">
    <div class="bukti-card">

        <div class="bukti-header">
            <div class="icon">✓</div>
            <h1>Pendaftaran Berhasil!</h1>
            <p>Bukti Pendaftaran Peserta Didik Baru</p>
            <p><?= e($namaSekolah ?? 'Sekolah') ?></p>
        </div>

        <div class="bukti-body">

            <div class="status-box">
                <strong>Status: <?= e($data['status']) ?></strong>
                Data pendaftaran kamu berhasil disimpan.
            </div>

            <div class="nomor-pendaftaran">
                <span>Nomor Pendaftaran</span>
                <strong><?= e($data['nomor_pendaftaran']) ?></strong>
            </div>

            <h2>Data Calon Siswa</h2>

            <table class="data-table">
                <tr>
                    <td>Nama Lengkap</td>
                    <td><?= e($data['nama_lengkap']) ?></td>
                </tr>
                <tr>
                    <td>NIK</td>
                    <td><?= e($data['nik']) ?></td>
                </tr>
                <tr>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>
                        <?= e($data['tempat_lahir']) ?>,
                        <?= e($tanggalLahir) ?>
                    </td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td><?= e($data['jenis_kelamin']) ?></td>
                </tr>
                <tr>
                    <td>Agama</td>
                    <td><?= e($data['agama']) ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td><?= nl2br(e($data['alamat'])) ?></td>
                </tr>
                <tr>
                    <td>Asal Sekolah</td>
                    <td><?= e($data['asal_sekolah']) ?></td>
                </tr>
                <tr>
                    <td>Tahun Lulus</td>
                    <td><?= e($data['tahun_lulus']) ?></td>
                </tr>
                <tr>
                    <td>Jurusan Pilihan</td>
                    <td><?= e($data['jurusan']) ?></td>
                </tr>
                <tr>
                    <td>Nama Orang Tua</td>
                    <td><?= e($data['nama_orang_tua']) ?></td>
                </tr>
                <tr>
                    <td>Nomor WhatsApp</td>
                    <td><?= e($data['no_whatsapp']) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Pendaftaran</td>
                    <td><?= e($tanggalDaftar) ?></td>
                </tr>
            </table>

            <div class="catatan">
                <strong>Informasi penting:</strong><br>
                1. Simpan atau cetak bukti pendaftaran ini.<br>
                2. Catat nomor pendaftaran kamu.<br>
                3. Status pendaftaran saat ini adalah
                <strong><?= e($data['status']) ?></strong>.<br>
                4. Tunggu informasi selanjutnya dari pihak sekolah.
            </div>

            <div class="tombol-wrapper">
                <button
                    type="button"
                    class="btn btn-cetak"
                    onclick="window.print()">
                    Cetak / Simpan PDF
                </button>

                <a
                    href="<?= defined('BASE_URL') ? BASE_URL . '/index.php' : 'index.php' ?>"
                    class="btn btn-kembali">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>