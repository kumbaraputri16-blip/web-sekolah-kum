<?php
require_once __DIR__ . '/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID pendaftaran tidak valid.');
}

$stmt = $koneksi->prepare("
    SELECT
        id,
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
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    http_response_code(404);
    exit('Data pendaftaran tidak ditemukan.');
}

function e($nilai)
{
    return htmlspecialchars(
        (string) ($nilai ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}

function tanggalIndonesia($tanggal)
{
    if (!$tanggal) {
        return '-';
    }

    $timestamp = strtotime($tanggal);

    if (!$timestamp) {
        return '-';
    }

    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    return date('d', $timestamp) . ' ' .
        $bulan[(int) date('n', $timestamp)] . ' ' .
        date('Y', $timestamp);
}

$logo = '../assets/images/logo-sekolah.png';

$tanggalDaftar = $siswa['tanggal_daftar'] ?? null;

$tahunAjaran = date('Y') . '/' . (date('Y') + 1);

$nomorPendaftaran = $siswa['nomor_pendaftaran']
    ?: 'Belum tersedia';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Dokumen PPDB - <?= e($siswa['nama_lengkap']) ?>
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #e9edf3;
            color: #222;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            padding: 18px;
        }

        
.toolbar button,
.toolbar a {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    padding: 11px 18px;
    border: none;
    border-radius: 5px;
    background: #174ea6;
    color: white;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.5;
    text-decoration: none;
    cursor: pointer;
    box-sizing: border-box;
}

.toolbar a {
    background: #596579;
}


        .halaman {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 20px;
            padding: 15mm 16mm;
            background: white;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
        }

        .kop {
            display: grid;
            grid-template-columns: 78px 1fr;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 3px double #183b68;
        }

        .logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-teks {
            text-align: center;
        }

        .kop-teks h1 {
            margin: 0;
            color: #183b68;
            font-size: 19px;
            letter-spacing: 0.5px;
        }

        .kop-teks h2 {
            margin: 4px 0;
            font-size: 13px;
            font-weight: bold;
        }

        .kop-teks p {
            margin: 2px 0;
            font-size: 10px;
        }

        .judul {
            margin: 23px 0 18px;
            text-align: center;
        }

        .judul h2 {
            margin: 0 0 5px;
            font-size: 16px;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .judul p {
            margin: 5px 0;
            font-size: 11px;
        }

        .nomor-dokumen {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #aab7c8;
            text-align: center;
        }

        .nomor-dokumen strong {
            display: block;
            margin-top: 3px;
            color: #183b68;
            font-size: 14px;
        }

        .bagian {
            margin-top: 18px;
        }

        .bagian h3 {
            margin: 0 0 8px;
            padding: 7px 9px;
            background: #eaf0f8;
            border-left: 4px solid #183b68;
            color: #183b68;
            font-size: 11px;
            letter-spacing: 0.2px;
        }

        .tabel-data {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-data td {
            padding: 5px 6px;
            border: 1px solid #d6dce5;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .tabel-data td:first-child {
            width: 34%;
        }

        .tabel-data td:nth-child(2) {
            width: 18px;
            text-align: center;
        }

        .kotak-jurusan {
            margin-top: 12px;
            padding: 12px;
            border: 1px solid #183b68;
        }

        .kotak-jurusan strong {
            display: block;
            margin-bottom: 5px;
            color: #183b68;
        }

        .catatan {
            margin-top: 18px;
            padding: 10px;
            border: 1px solid #d6dce5;
            font-size: 10px;
            line-height: 1.7;
        }

        .area-ttd {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            margin-top: 28px;
            text-align: center;
        }

        .ttd p {
            margin: 0;
        }

        .ruang-ttd {
            height: 65px;
        }

        .garis-nama {
            display: inline-block;
            min-width: 150px;
            padding-top: 4px;
            border-top: 1px solid #333;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            border: 1px solid #aab7c8;
            font-weight: bold;
        }

        .footer-dokumen {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            text-align: center;
            font-size: 9px;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body {
                background: white;
                font-size: 11px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .toolbar {
                display: none !important;
            }

            .halaman {
                width: 210mm;
                height: 297mm;
                min-height: 297mm;
                margin: 0;
                padding: 15mm 16mm;
                box-shadow: none;
                overflow: hidden;
            }

            .page-break {
                page-break-before: always;
                break-before: page;
            }
        }

        @media screen and (max-width: 800px) {
            .halaman {
                width: 100%;
                min-height: auto;
                padding: 20px;
            }

            .kop {
                grid-template-columns: 55px 1fr;
                gap: 8px;
            }

            .logo {
                width: 52px;
                height: 52px;
            }

            .kop-teks h1 {
                font-size: 15px;
            }

            .area-ttd {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<div class="toolbar">
    <button onclick="window.print()">
        Cetak / Simpan PDF
    </button>

    <a href="<?= e(BASE_URL) ?>/admin/pendaftaran.php">
        Kembali
    </a>
</div>


<!-- =====================================================
     HALAMAN 1: FORMULIR PENDAFTARAN
     ===================================================== -->

<section class="halaman">

    <header class="kop">

        <img
            src="<?= e($logo) ?>"
            alt="Logo SMK Infokom Bogor"
            class="logo"
            onerror="this.style.display='none'"
        >

        <div class="kop-teks">
            <h1>SMK INFOKOM BOGOR</h1>

            <h2>
                FORMULIR PENDAFTARAN PESERTA DIDIK BARU
            </h2>

            <p>
                TAHUN AJARAN <?= e($tahunAjaran) ?>
            </p>

            <p>
                Dokumen Administrasi Penerimaan Peserta Didik Baru
            </p>
        </div>

    </header>


    <div class="judul">
        <h2>FORMULIR PENDAFTARAN PPDB</h2>

        <p>
            Diisi berdasarkan data pendaftaran calon peserta didik
            yang tersimpan dalam sistem.
        </p>
    </div>


    <div class="nomor-dokumen">
        NOMOR PENDAFTARAN

        <strong>
            <?= e($nomorPendaftaran) ?>
        </strong>
    </div>


    <div class="bagian">

        <h3>A. IDENTITAS CALON PESERTA DIDIK</h3>

        <table class="tabel-data">

            <tr>
                <td>Nama lengkap</td>
                <td>:</td>
                <td><?= e($siswa['nama_lengkap']) ?></td>
            </tr>

            <tr>
                <td>Nomor Induk Kependudukan (NIK)</td>
                <td>:</td>
                <td><?= e($siswa['nik']) ?></td>
            </tr>

            <tr>
                <td>Tempat lahir</td>
                <td>:</td>
                <td><?= e($siswa['tempat_lahir']) ?></td>
            </tr>

            <tr>
                <td>Tanggal lahir</td>
                <td>:</td>
                <td>
                    <?= e(tanggalIndonesia($siswa['tanggal_lahir'])) ?>
                </td>
            </tr>

            <tr>
                <td>Jenis kelamin</td>
                <td>:</td>
                <td><?= e($siswa['jenis_kelamin']) ?></td>
            </tr>

            <tr>
                <td>Agama</td>
                <td>:</td>
                <td><?= e($siswa['agama']) ?></td>
            </tr>

            <tr>
                <td>Alamat lengkap</td>
                <td>:</td>
                <td><?= nl2br(e($siswa['alamat'])) ?></td>
            </tr>

        </table>

    </div>


    <div class="bagian">

        <h3>B. IDENTITAS ASAL SEKOLAH</h3>

        <table class="tabel-data">

            <tr>
                <td>Asal sekolah</td>
                <td>:</td>
                <td><?= e($siswa['asal_sekolah']) ?></td>
            </tr>

            <tr>
                <td>Tahun lulus</td>
                <td>:</td>
                <td><?= e($siswa['tahun_lulus']) ?></td>
            </tr>

        </table>

    </div>


    <div class="bagian">

        <h3>C. PILIHAN PROGRAM KEAHLIAN</h3>

        <div class="kotak-jurusan">
            <strong>Program keahlian yang dipilih:</strong>
            <?= e($siswa['jurusan']) ?>
        </div>

    </div>


    <div class="bagian">

        <h3>D. DATA ORANG TUA / WALI</h3>

        <table class="tabel-data">

            <tr>
                <td>Nama orang tua/wali</td>
                <td>:</td>
                <td><?= e($siswa['nama_orang_tua']) ?></td>
            </tr>

            <tr>
                <td>Nomor WhatsApp</td>
                <td>:</td>
                <td><?= e($siswa['no_whatsapp']) ?></td>
            </tr>

        </table>

    </div>


    <div class="catatan">
        <strong>Pernyataan:</strong>

        Dokumen ini memuat data yang telah dimasukkan ke dalam sistem
        pendaftaran PPDB. Calon peserta didik dan orang tua/wali
        bertanggung jawab atas kebenaran data yang diberikan.
        Pencetakan dokumen ini tidak dengan sendirinya menyatakan
        bahwa calon peserta didik telah diterima sebagai siswa.
    </div>


    <div class="area-ttd">

        <div class="ttd">
            <p>Calon peserta didik,</p>

            <div class="ruang-ttd"></div>

            <span class="garis-nama">
                <?= e($siswa['nama_lengkap']) ?>
            </span>
        </div>

        <div class="ttd">
            <p>Orang tua/wali,</p>

            <div class="ruang-ttd"></div>

            <span class="garis-nama">
                <?= e($siswa['nama_orang_tua']) ?>
            </span>
        </div>

    </div>


    <div class="footer-dokumen">
        FORMULIR PPDB • SMK INFOKOM BOGOR • TAHUN AJARAN
        <?= e($tahunAjaran) ?>
    </div>

</section>



<!-- =====================================================
     HALAMAN 2: BUKTI PENDAFTARAN
     ===================================================== -->

<section class="halaman page-break">

    <header class="kop">

        <img
            src="<?= e($logo) ?>"
            alt="Logo SMK Infokom Bogor"
            class="logo"
            onerror="this.style.display='none'"
        >

        <div class="kop-teks">
            <h1>SMK INFOKOM BOGOR</h1>

            <h2>
                BUKTI PENDAFTARAN PESERTA DIDIK BARU
            </h2>

            <p>
                TAHUN AJARAN <?= e($tahunAjaran) ?>
            </p>

            <p>
                Salinan administrasi pendaftaran
            </p>
        </div>

    </header>


    <div class="judul">
        <h2>BUKTI PENDAFTARAN PPDB</h2>

        <p>
            Dokumen konfirmasi pencatatan data calon peserta didik.
        </p>
    </div>


    <div class="nomor-dokumen">
        NOMOR PENDAFTARAN

        <strong>
            <?= e($nomorPendaftaran) ?>
        </strong>
    </div>


    <div class="bagian">

        <h3>A. RINGKASAN PENDAFTARAN</h3>

        <table class="tabel-data">

            <tr>
                <td>Tanggal pendaftaran</td>
                <td>:</td>
                <td>
                    <?= e(tanggalIndonesia($tanggalDaftar)) ?>

                    <?php if ($tanggalDaftar): ?>
                        <?= e(date('H:i', strtotime($tanggalDaftar))) ?>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <td>Nama calon peserta didik</td>
                <td>:</td>
                <td><?= e($siswa['nama_lengkap']) ?></td>
            </tr>

            <tr>
                <td>NIK</td>
                <td>:</td>
                <td><?= e($siswa['nik']) ?></td>
            </tr>

            <tr>
                <td>Asal sekolah</td>
                <td>:</td>
                <td><?= e($siswa['asal_sekolah']) ?></td>
            </tr>

            <tr>
                <td>Program keahlian</td>
                <td>:</td>
                <td><?= e($siswa['jurusan']) ?></td>
            </tr>

            <tr>
                <td>Status pendaftaran</td>
                <td>:</td>
                <td>
                    <span class="status">
                        <?= e($siswa['status']) ?>
                    </span>
                </td>
            </tr>

        </table>

    </div>


    <div class="bagian">

        <h3>B. KETERANGAN PENDAFTARAN</h3>

        <p>
            Berdasarkan data yang tersimpan pada sistem PPDB,
            calon peserta didik dengan nomor pendaftaran
            <strong><?= e($nomorPendaftaran) ?></strong>
            telah tercatat melakukan pendaftaran pada tanggal
            <?= e(tanggalIndonesia($tanggalDaftar)) ?>.
        </p>

        <p>
            Status yang tercantum pada dokumen ini merupakan status
            pendaftaran yang tersimpan di sistem pada saat dokumen
            dicetak. Calon peserta didik tetap wajib mengikuti
            proses verifikasi dan ketentuan penerimaan peserta didik
            yang ditetapkan oleh sekolah.
        </p>

    </div>


    <div class="catatan">
        <strong>Petunjuk bagi calon peserta didik:</strong>

        <ol>
            <li>Simpan dokumen ini sebagai bukti pencatatan pendaftaran.</li>
            <li>Periksa kembali nomor pendaftaran dan data diri.</li>
            <li>Hubungi pihak sekolah apabila ditemukan kesalahan data.</li>
            <li>Ikuti informasi dan tahapan PPDB yang diumumkan sekolah.</li>
        </ol>
    </div>


    <div class="area-ttd">

        <div class="ttd">
            <p>Calon peserta didik,</p>

            <div class="ruang-ttd"></div>

            <span class="garis-nama">
                <?= e($siswa['nama_lengkap']) ?>
            </span>
        </div>

        <div class="ttd">
            <p>
                Bogor,
                <?= e(tanggalIndonesia(date('Y-m-d'))) ?>
            </p>

            <p>Panitia PPDB,</p>

            <div class="ruang-ttd"></div>

            <span class="garis-nama">
                Panitia PPDB
            </span>
        </div>

    </div>


    <div class="footer-dokumen">
        DOKUMEN PPDB • NOMOR <?= e($nomorPendaftaran) ?>
        • SMK INFOKOM BOGOR
    </div>

</section>


</body>
</html>