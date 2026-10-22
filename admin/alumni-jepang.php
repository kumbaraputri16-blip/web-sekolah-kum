<?php

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| KONFIGURASI
|--------------------------------------------------------------------------
*/

if (!defined('BASE_URL')) {
    define('BASE_URL', '/sekolah-website-kum');
}

$folderUpload = __DIR__ . '/../uploads/alumni/';

if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0777, true);
}

$pesan = '';
$error = '';

/*
|--------------------------------------------------------------------------
| BUAT TABEL JIKA BELUM ADA
|--------------------------------------------------------------------------
*/

$koneksi->exec("
    CREATE TABLE IF NOT EXISTS alumni_jepang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(150) NOT NULL,
        jurusan VARCHAR(100) DEFAULT NULL,
        tahun_lulus VARCHAR(10) DEFAULT NULL,
        tahun_berangkat VARCHAR(10) DEFAULT NULL,
        perusahaan VARCHAR(150) DEFAULT NULL,
        foto VARCHAR(255) DEFAULT NULL,
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");


/*
|--------------------------------------------------------------------------
| FUNGSI UPLOAD FOTO
|--------------------------------------------------------------------------
*/

function uploadFotoAlumni($file, $folderUpload)
{
    if (
        !isset($file) ||
        $file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    $ekstensi = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    $ekstensiDiizinkan = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    if (!in_array($ekstensi, $ekstensiDiizinkan, true)) {
        return false;
    }

    $namaBaru =
        'alumni_' .
        time() .
        '_' .
        bin2hex(random_bytes(5)) .
        '.' .
        $ekstensi;

    $tujuan = $folderUpload . $namaBaru;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        return false;
    }

    return $namaBaru;
}


/*
|--------------------------------------------------------------------------
| HAPUS DATA
|--------------------------------------------------------------------------
*/

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    $stmt = $koneksi->prepare("
        SELECT foto
        FROM alumni_jepang
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {

        if (!empty($data['foto'])) {

            $fileFoto = $folderUpload . $data['foto'];

            if (file_exists($fileFoto)) {
                unlink($fileFoto);
            }
        }

        $stmt = $koneksi->prepare("
            DELETE FROM alumni_jepang
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $pesan = 'Data alumni berhasil dihapus.';
    }
}


/*
|--------------------------------------------------------------------------
| TAMBAH DATA
|--------------------------------------------------------------------------
*/

if (isset($_POST['tambah_alumni'])) {

    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $tahunLulus = trim($_POST['tahun_lulus'] ?? '');
    $tahunBerangkat = trim($_POST['tahun_berangkat'] ?? '');
    $perusahaan = trim($_POST['perusahaan'] ?? '');

    if ($nama === '') {

        $error = 'Nama alumni wajib diisi.';

    } else {

        $foto = uploadFotoAlumni(
            $_FILES['foto'] ?? null,
            $folderUpload
        );

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE &&
            $foto === false
        ) {

            $error = 'Foto gagal diupload. Pastikan JPG, JPEG, PNG, atau WEBP dan maksimal 5 MB.';

        } else {

            $stmt = $koneksi->prepare("
                INSERT INTO alumni_jepang
                (
                    nama,
                    jurusan,
                    tahun_lulus,
                    tahun_berangkat,
                    perusahaan,
                    foto
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nama,
                $jurusan,
                $tahunLulus,
                $tahunBerangkat,
                $perusahaan,
                $foto
            ]);

            $pesan = 'Data alumni berhasil ditambahkan.';
        }
    }
}


/*
|--------------------------------------------------------------------------
| DATA EDIT
|--------------------------------------------------------------------------
*/

$dataEdit = null;

if (isset($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $stmt = $koneksi->prepare("
        SELECT *
        FROM alumni_jepang
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}


/*
|--------------------------------------------------------------------------
| UPDATE DATA
|--------------------------------------------------------------------------
*/

if (isset($_POST['update_alumni'])) {

    $id = (int) ($_POST['id'] ?? 0);

    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $tahunLulus = trim($_POST['tahun_lulus'] ?? '');
    $tahunBerangkat = trim($_POST['tahun_berangkat'] ?? '');
    $perusahaan = trim($_POST['perusahaan'] ?? '');

    if ($nama === '') {

        $error = 'Nama alumni wajib diisi.';

    } else {

        $stmt = $koneksi->prepare("
            SELECT foto
            FROM alumni_jepang
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $dataLama = $stmt->fetch(PDO::FETCH_ASSOC);

        $fotoBaru = uploadFotoAlumni(
            $_FILES['foto'] ?? null,
            $folderUpload
        );

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE &&
            $fotoBaru === false
        ) {

            $error = 'Foto gagal diupload. Pastikan JPG, JPEG, PNG, atau WEBP dan maksimal 5 MB.';

        } else {

            if ($fotoBaru !== null) {

                if (
                    $dataLama &&
                    !empty($dataLama['foto'])
                ) {

                    $fotoLama = $folderUpload . $dataLama['foto'];

                    if (file_exists($fotoLama)) {
                        unlink($fotoLama);
                    }
                }

                $foto = $fotoBaru;

            } else {

                $foto = $dataLama['foto'] ?? null;
            }

            $stmt = $koneksi->prepare("
                UPDATE alumni_jepang
                SET
                    nama = ?,
                    jurusan = ?,
                    tahun_lulus = ?,
                    tahun_berangkat = ?,
                    perusahaan = ?,
                    foto = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nama,
                $jurusan,
                $tahunLulus,
                $tahunBerangkat,
                $perusahaan,
                $foto,
                $id
            ]);

            $pesan = 'Data alumni berhasil diperbarui.';

            $dataEdit = null;
        }
    }
}


/*
|--------------------------------------------------------------------------
| AMBIL SEMUA ALUMNI
|--------------------------------------------------------------------------
*/

$stmt = $koneksi->query("
    SELECT *
    FROM alumni_jepang
    ORDER BY tahun_berangkat DESC, id DESC
");

$alumni = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| LAYOUT ADMIN
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/layout_atas.php';

?>

<div class="area-konten">

    <?php if ($pesan !== ''): ?>

        <div class="pesan-status pesan-berhasil">
            <?= htmlspecialchars($pesan) ?>
        </div>

    <?php endif; ?>


    <?php if ($error !== ''): ?>

        <div class="pesan-status pesan-gagal">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         FORM ALUMNI
         ===================================================== -->

    <div class="kartu-panel">

        <div class="baris-header-panel">

            <div>

                <h2>
                    <?= $dataEdit ? 'Edit Alumni Jepang' : 'Tambah Alumni Jepang' ?>
                </h2>

                <p style="margin:0;color:#5C574C;">
                    Tambahkan alumni yang mengikuti program ke Jepang.
                </p>

            </div>

        </div>


        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <?php if ($dataEdit): ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $dataEdit['id'] ?>"
                >

            <?php endif; ?>


            <div class="grup-form">

                <label for="nama">
                    Nama Alumni *
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    required
                    placeholder="Contoh: Ahmad Rizky"
                    value="<?= htmlspecialchars($dataEdit['nama'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="jurusan">
                    Jurusan
                </label>

                <input
                    type="text"
                    id="jurusan"
                    name="jurusan"
                    placeholder="Contoh: DKV"
                    value="<?= htmlspecialchars($dataEdit['jurusan'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="tahun_lulus">
                    Tahun Lulus
                </label>

                <input
                    type="text"
                    id="tahun_lulus"
                    name="tahun_lulus"
                    placeholder="Contoh: 2025"
                    value="<?= htmlspecialchars($dataEdit['tahun_lulus'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="tahun_berangkat">
                    Tahun Berangkat ke Jepang
                </label>

                <input
                    type="text"
                    id="tahun_berangkat"
                    name="tahun_berangkat"
                    placeholder="Contoh: 2026"
                    value="<?= htmlspecialchars($dataEdit['tahun_berangkat'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="perusahaan">
                    Perusahaan / Tempat Kerja
                </label>

                <input
                    type="text"
                    id="perusahaan"
                    name="perusahaan"
                    placeholder="Contoh: Perusahaan Jepang"
                    value="<?= htmlspecialchars($dataEdit['perusahaan'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="foto">
                    Foto Alumni
                </label>


                <?php if (
                    $dataEdit &&
                    !empty($dataEdit['foto'])
                ): ?>

                    <div style="margin-bottom:10px;">

                        <img
                            src="<?= BASE_URL ?>/uploads/alumni/<?= htmlspecialchars($dataEdit['foto']) ?>"
                            alt="Foto alumni"
                            style="
                                width:150px;
                                height:150px;
                                object-fit:cover;
                                border-radius:8px;
                                border:1px solid #DCD5C4;
                            "
                        >

                    </div>

                <?php endif; ?>


                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="bantuan-form">
                    JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.
                </div>

            </div>


            <div>

                <?php if ($dataEdit): ?>

                    <button
                        type="submit"
                        name="update_alumni"
                        class="tombol"
                    >
                        Simpan Perubahan
                    </button>

                    <a
                        href="alumni-jepang.php"
                        class="tombol tombol-garis"
                    >
                        Batal
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="tambah_alumni"
                        class="tombol"
                    >
                        + Tambah Alumni
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =====================================================
         DAFTAR ALUMNI
         ===================================================== -->

    <div class="kartu-panel">

        <div class="baris-header-panel">

            <div>

                <h2>
                    Daftar Alumni Jepang
                </h2>

                <p style="margin:0;color:#5C574C;">
                    <?= count($alumni) ?> alumni tersimpan.
                </p>

            </div>

        </div>


        <?php if (!empty($alumni)): ?>

            <div style="overflow-x:auto;">

                <table class="tabel-admin">

                    <thead>

                        <tr>

                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Lulus</th>
                            <th>Berangkat</th>
                            <th>Tempat</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($alumni as $a): ?>

                            <tr>

                                <td>

                                    <?php if (!empty($a['foto'])): ?>

                                        <img
                                            src="<?= BASE_URL ?>/uploads/alumni/<?= htmlspecialchars($a['foto']) ?>"
                                            alt="<?= htmlspecialchars($a['nama']) ?>"
                                            class="mini-foto"
                                        >

                                    <?php else: ?>

                                        <span style="font-size:25px;">
                                            🎓
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars($a['nama']) ?>
                                    </strong>

                                </td>


                                <td>
                                    <?= htmlspecialchars($a['jurusan'] ?: '-') ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($a['tahun_lulus'] ?: '-') ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($a['tahun_berangkat'] ?: '-') ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($a['perusahaan'] ?: '-') ?>
                                </td>


                                <td>

                                    <div
                                        style="
                                            display:flex;
                                            gap:6px;
                                            flex-wrap:wrap;
                                        "
                                    >

                                        <a
                                            href="alumni-jepang.php?edit=<?= (int) $a['id'] ?>"
                                            class="tombol tombol-kecil tombol-garis"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="alumni-jepang.php?hapus=<?= (int) $a['id'] ?>"
                                            class="tombol tombol-kecil tombol-bahaya"
                                            onclick="return confirm('Yakin ingin menghapus alumni ini?')"
                                        >
                                            Hapus
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div
                style="
                    padding:30px;
                    text-align:center;
                    color:#5C574C;
                    background:#F6F2E9;
                    border-radius:6px;
                "
            >

                Belum ada data alumni Jepang.

            </div>

        <?php endif; ?>

    </div>

</div>


<?php

require_once __DIR__ . '/includes/layout_bawah.php';

?>