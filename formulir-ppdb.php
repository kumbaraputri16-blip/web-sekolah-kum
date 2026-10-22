<?php
session_start();

require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Formulir Pendaftaran PPDB';
$error = '';
$gelombang = [];
$biaya = [];

function e($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

try {
    // Ambil gelombang PPDB yang sedang aktif.
    $stmtGelombang = $koneksi->query("
        SELECT id, nama_gelombang, tanggal_mulai, tanggal_selesai
        FROM gelombang_ppdb
        WHERE status = 'Dibuka'
          AND CURDATE() BETWEEN tanggal_mulai AND tanggal_selesai
        ORDER BY tanggal_mulai ASC
    ");
    $gelombang = $stmtGelombang->fetchAll(PDO::FETCH_ASSOC);

    // Ambil daftar biaya per gelombang dan jurusan.
    $stmtBiaya = $koneksi->query("
        SELECT gelombang_id, jurusan, nama_biaya, nominal
        FROM biaya_ppdb
        ORDER BY nama_biaya ASC
    ");
    $semuaBiaya = $stmtBiaya->fetchAll(PDO::FETCH_ASSOC);

    foreach ($semuaBiaya as $item) {
        $biaya[$item['gelombang_id']][$item['jurusan']][] = $item;
    }
} catch (PDOException $ex) {
    $error = 'Data PPDB gagal dimuat. Pastikan tabel gelombang_ppdb dan biaya_ppdb sudah dibuat. Detail: ' . $ex->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === '') {
    try {
        $namaSiswa = trim($_POST['nama_lengkap'] ?? '');
        $nik = trim($_POST['nik'] ?? '');
        $tempatLahir = trim($_POST['tempat_lahir'] ?? '');
        $tanggalLahir = trim($_POST['tanggal_lahir'] ?? '');
        $jenisKelamin = trim($_POST['jenis_kelamin'] ?? '');
        $agama = trim($_POST['agama'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $asalSekolah = trim($_POST['asal_sekolah'] ?? '');
        $tahunLulus = (int) ($_POST['tahun_lulus'] ?? 0);
        $jurusan = trim($_POST['jurusan'] ?? '');
        $namaOrangTua = trim($_POST['nama_orang_tua'] ?? '');
        $noWhatsapp = trim($_POST['no_whatsapp'] ?? '');
        $gelombangId = (int) ($_POST['gelombang_id'] ?? 0);

        if (
            $namaSiswa === '' || $nik === '' || $tempatLahir === '' ||
            $tanggalLahir === '' || $jenisKelamin === '' || $agama === '' ||
            $alamat === '' || $asalSekolah === '' || $namaOrangTua === '' ||
            $noWhatsapp === '' || $jurusan === '' || $gelombangId < 1
        ) {
            throw new Exception('Semua kolom wajib diisi.');
        }

        if (!preg_match('/^[0-9]{16}$/', $nik)) {
            throw new Exception('NIK harus terdiri dari tepat 16 angka.');
        }

        if (!in_array($jenisKelamin, ['Laki-laki', 'Perempuan'], true)) {
            throw new Exception('Pilihan jenis kelamin tidak valid.');
        }

        if (!in_array($jurusan, ['DKV', 'PSPT', 'RPL', 'TKJ'], true)) {
            throw new Exception('Pilihan jurusan tidak valid.');
        }

        if ($tahunLulus < 2000 || $tahunLulus > 2100) {
            throw new Exception('Tahun lulus tidak valid.');
        }

        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggalLahir);
        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggalLahir) {
            throw new Exception('Tanggal lahir tidak valid.');
        }

        // Pastikan gelombang benar-benar masih dibuka.
        $cekGelombang = $koneksi->prepare("
            SELECT id
            FROM gelombang_ppdb
            WHERE id = ?
              AND status = 'Dibuka'
              AND CURDATE() BETWEEN tanggal_mulai AND tanggal_selesai
            LIMIT 1
        ");
        $cekGelombang->execute([$gelombangId]);

        if (!$cekGelombang->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('Gelombang PPDB sudah ditutup atau tidak tersedia.');
        }

        // Pastikan jurusan memiliki rincian biaya.
        $cekBiaya = $koneksi->prepare("
            SELECT COUNT(*)
            FROM biaya_ppdb
            WHERE gelombang_id = ? AND jurusan = ?
        ");
        $cekBiaya->execute([$gelombangId, $jurusan]);

        if ((int) $cekBiaya->fetchColumn() === 0) {
            throw new Exception('Biaya untuk jurusan yang dipilih belum tersedia.');
        }

        $koneksi->beginTransaction();

        // Simpan 13 data formulir. Nomor pendaftaran dibuat setelah ID tersedia.
        $simpan = $koneksi->prepare("
            INSERT INTO pendaftaran (
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
                gelombang_id,
                status
            ) VALUES (
                NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                'Menunggu Verifikasi'
            )
        ");

        $simpan->execute([
            $namaSiswa,
            $nik,
            $tempatLahir,
            $tanggalLahir,
            $jenisKelamin,
            $agama,
            $alamat,
            $asalSekolah,
            $tahunLulus,
            $jurusan,
            $namaOrangTua,
            $noWhatsapp,
            $gelombangId
        ]);

        $idPendaftaran = (int) $koneksi->lastInsertId();
        $nomorPendaftaran = 'PPDB-' . date('Y') . '-' . str_pad((string) $idPendaftaran, 5, '0', STR_PAD_LEFT);

        $updateNomor = $koneksi->prepare("
            UPDATE pendaftaran
            SET nomor_pendaftaran = ?
            WHERE id = ?
        ");
        $updateNomor->execute([$nomorPendaftaran, $idPendaftaran]);

        $koneksi->commit();

        $token = bin2hex(random_bytes(24));
        $_SESSION['token_ppdb_' . $idPendaftaran] = $token;

        header('Location: cetak-ppdb.php?id=' . $idPendaftaran . '&token=' . urlencode($token));

exit;
    } catch (Throwable $ex) {
        if ($koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        // Tampilkan pesan asli supaya penyebab database bisa diketahui jika masih gagal.
        $error = 'Pendaftaran gagal: ' . $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul_halaman) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #263445;
        }
        .form-container {
            max-width: 950px;
            margin: auto;
            padding: 35px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,.07);
        }
        .judul { margin-bottom: 28px; text-align: center; }
        .judul h1 { margin-bottom: 10px; font-size: 30px; }
        .judul p { color: #697586; line-height: 1.6; }
        .bagian { margin-top: 30px; }
        .bagian h2 {
            padding-bottom: 12px;
            border-bottom: 1px solid #e4e8ef;
            font-size: 20px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: 14px; font-weight: bold; }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d5dbe5;
            border-radius: 8px;
            font: inherit;
            background: #fff;
        }
        textarea { min-height: 100px; resize: vertical; }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #c9dcff;
            border-color: #4779db;
        }
        .catatan {
            padding: 14px;
            border-radius: 8px;
            background: #eef5ff;
            color: #264b82;
            font-size: 14px;
            line-height: 1.6;
        }
        .biaya-box {
            margin-top: 20px;
            padding: 20px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e3e8ef;
        }
        .biaya-box h3 { margin-top: 0; }
        .baris-biaya, .total-biaya {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e9f0;
        }
        .total-biaya {
            padding-top: 18px;
            border-bottom: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .pesan-error {
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #ffe9e9;
            color: #a32323;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }
        .tombol {
            display: inline-block;
            width: 100%;
            margin-top: 25px;
            padding: 15px;
            border: none;
            border-radius: 9px;
            background: #263b55;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .tombol:hover { background: #385779; }
        .kembali {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #385779;
            text-decoration: none;
        }
        @media (max-width: 650px) {
            .form-container { padding: 22px 16px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: auto; }
            .judul h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
<div class="form-container">
    <div class="judul">
        <h1>Formulir Pendaftaran PPDB</h1>
        <p>Isi data calon siswa dengan benar. Periksa rincian biaya sesuai gelombang dan jurusan pilihanmu.</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="pesan-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (empty($gelombang)): ?>
        <div class="catatan">
            Saat ini belum ada gelombang PPDB yang aktif. Periksa jadwal dan status gelombang di database.
        </div>
    <?php else: ?>
        <form method="POST" action="">
            <section class="bagian">
                <h2>1. Pilihan Pendaftaran</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="gelombang_id">Gelombang PPDB *</label>
                        <select name="gelombang_id" id="gelombang_id" required>
                            <option value="">-- Pilih Gelombang --</option>
                            <?php foreach ($gelombang as $g): ?>
                                <option value="<?= (int) $g['id'] ?>"
                                    <?= (string) ($_POST['gelombang_id'] ?? '') === (string) $g['id'] ? 'selected' : '' ?>>
                                    <?= e($g['nama_gelombang']) ?> (<?= e($g['tanggal_mulai']) ?> s.d. <?= e($g['tanggal_selesai']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jurusan">Jurusan Pilihan *</label>
                        <select name="jurusan" id="jurusan" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <option value="DKV" <?= ($_POST['jurusan'] ?? '') === 'DKV' ? 'selected' : '' ?>>DKV - Desain Komunikasi Visual</option>
                            <option value="PSPT" <?= ($_POST['jurusan'] ?? '') === 'PSPT' ? 'selected' : '' ?>>PSPT - Produksi dan Siaran Program Televisi</option>
                            <option value="RPL" <?= ($_POST['jurusan'] ?? '') === 'RPL' ? 'selected' : '' ?>>RPL - Rekayasa Perangkat Lunak</option>
                            <option value="TKJ" <?= ($_POST['jurusan'] ?? '') === 'TKJ' ? 'selected' : '' ?>>TKJ - Teknik Komputer dan Jaringan</option>
                        </select>
                    </div>
                </div>

                <div class="biaya-box">
                    <h3>Rincian Biaya Pendaftaran</h3>
                    <div id="daftar-biaya">Pilih gelombang dan jurusan untuk melihat biaya.</div>
                    <div class="total-biaya">
                        <span>Total Biaya Masuk</span>
                        <span id="total-biaya">Rp 0</span>
                    </div>
                    <p style="font-size:13px;color:#697586;line-height:1.6">
                        Nominal mengikuti data biaya yang tersimpan di database sekolah.
                    </p>
                </div>
            </section>

            <section class="bagian">
                <h2>2. Data Calon Siswa</h2>
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="nama_lengkap">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" maxlength="100" value="<?= e($_POST['nama_lengkap'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nik">NIK *</label>
                        <input type="text" name="nik" id="nik" maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}" value="<?= e($_POST['nik'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" <?= ($_POST['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= ($_POST['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir *</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" maxlength="100" value="<?= e($_POST['tempat_lahir'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="<?= e($_POST['tanggal_lahir'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="agama">Agama *</label>
                        <select name="agama" id="agama" required>
                            <option value="">-- Pilih Agama --</option>
                            <?php foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $a): ?>
                                <option value="<?= e($a) ?>" <?= ($_POST['agama'] ?? '') === $a ? 'selected' : '' ?>><?= e($a) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="asal_sekolah">Asal Sekolah *</label>
                        <input type="text" name="asal_sekolah" id="asal_sekolah" maxlength="150" value="<?= e($_POST['asal_sekolah'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tahun_lulus">Tahun Lulus *</label>
                        <input type="number" name="tahun_lulus" id="tahun_lulus" min="2000" max="2100" value="<?= e($_POST['tahun_lulus'] ?? date('Y')) ?>" required>
                    </div>
                    <div class="form-group full">
                        <label for="alamat">Alamat Lengkap *</label>
                        <textarea name="alamat" id="alamat" required><?= e($_POST['alamat'] ?? '') ?></textarea>
                    </div>
                </div>
            </section>

            <section class="bagian">
                <h2>3. Data Orang Tua atau Wali</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama_orang_tua">Nama Orang Tua/Wali *</label>
                        <input type="text" name="nama_orang_tua" id="nama_orang_tua" maxlength="100" value="<?= e($_POST['nama_orang_tua'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="no_whatsapp">Nomor WhatsApp *</label>
                        <input type="tel" name="no_whatsapp" id="no_whatsapp" maxlength="20" value="<?= e($_POST['no_whatsapp'] ?? '') ?>" required>
                    </div>
                </div>
            </section>

            <div class="catatan">
                Pastikan semua data sudah benar sebelum menekan tombol <strong>Kirim Pendaftaran</strong>.
            </div>
            <button type="submit" class="tombol">Kirim Pendaftaran</button>
        </form>
    <?php endif; ?>

    <a href="ppdb.php" class="kembali">← Kembali ke halaman PPDB</a>
</div>

<script>
const dataBiaya = <?= json_encode($biaya, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const pilihanGelombang = document.getElementById('gelombang_id');
const pilihanJurusan = document.getElementById('jurusan');
const daftarBiaya = document.getElementById('daftar-biaya');
const totalBiaya = document.getElementById('total-biaya');

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function tampilkanBiaya() {
    const gelombangId = pilihanGelombang.value;
    const jurusan = pilihanJurusan.value;
    daftarBiaya.innerHTML = '';
    totalBiaya.textContent = formatRupiah(0);

    if (!gelombangId || !jurusan || !dataBiaya[gelombangId] || !dataBiaya[gelombangId][jurusan]) {
        daftarBiaya.textContent = 'Belum ada rincian biaya untuk pilihan ini.';
        return;
    }

    const daftar = dataBiaya[gelombangId][jurusan];
    let total = 0;

    daftar.forEach(function(item) {
        const nominal = Number(item.nominal);
        total += nominal;

        const baris = document.createElement('div');
        baris.className = 'baris-biaya';

        const nama = document.createElement('span');
        nama.textContent = item.nama_biaya;

        const nilai = document.createElement('strong');
        nilai.textContent = formatRupiah(nominal);

        baris.appendChild(nama);
        baris.appendChild(nilai);
        daftarBiaya.appendChild(baris);
    });

    totalBiaya.textContent = formatRupiah(total);
}

if (pilihanGelombang && pilihanJurusan) {
    pilihanGelombang.addEventListener('change', tampilkanBiaya);
    pilihanJurusan.addEventListener('change', tampilkanBiaya);
    tampilkanBiaya();
}
</script>
</body>
</html>
