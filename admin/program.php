<?php

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| KONFIGURASI
|--------------------------------------------------------------------------
*/

$folderUpload = __DIR__ . '/../uploads/program/';

if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0777, true);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/sekolah-website-kum');
}

$pesan = '';
$error = '';

/*
|--------------------------------------------------------------------------
| BUAT TABEL PROGRAM JIKA BELUM ADA
|--------------------------------------------------------------------------
*/

$koneksi->exec("
    CREATE TABLE IF NOT EXISTS program_sekolah (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nomor VARCHAR(10) DEFAULT NULL,
        ikon VARCHAR(50) DEFAULT NULL,
        judul VARCHAR(150) NOT NULL,
        singkatan VARCHAR(50) DEFAULT NULL,
        deskripsi TEXT DEFAULT NULL,
        foto VARCHAR(255) DEFAULT NULL,
        urutan INT DEFAULT 0,
        aktif TINYINT(1) DEFAULT 1,
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

/*
|--------------------------------------------------------------------------
| FUNGSI UPLOAD FOTO
|--------------------------------------------------------------------------
*/

function uploadFotoProgram($file, $folderUpload)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    $namaAsli = $file['name'];
    $tmpName = $file['tmp_name'];

    $ekstensi = strtolower(pathinfo($namaAsli, PATHINFO_EXTENSION));

    $ekstensiDiizinkan = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    if (!in_array($ekstensi, $ekstensiDiizinkan, true)) {
        return false;
    }

    $namaBaru = 'program_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ekstensi;

    $tujuan = $folderUpload . $namaBaru;

    if (!move_uploaded_file($tmpName, $tujuan)) {
        return false;
    }

    return $namaBaru;
}

/*
|--------------------------------------------------------------------------
| HAPUS PROGRAM
|--------------------------------------------------------------------------
*/

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    $stmt = $koneksi->prepare(
        "SELECT foto FROM program_sekolah WHERE id = ?"
    );

    $stmt->execute([$id]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {

        if (!empty($data['foto'])) {

            $fileFoto = $folderUpload . $data['foto'];

            if (file_exists($fileFoto)) {
                unlink($fileFoto);
            }
        }

        $stmt = $koneksi->prepare(
            "DELETE FROM program_sekolah WHERE id = ?"
        );

        $stmt->execute([$id]);

        $pesan = 'Program berhasil dihapus.';
    }
}

/*
|--------------------------------------------------------------------------
| TAMBAH PROGRAM
|--------------------------------------------------------------------------
*/

if (isset($_POST['tambah_program'])) {

    $nomor = trim($_POST['nomor'] ?? '');
    $ikon = trim($_POST['ikon'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $singkatan = trim($_POST['singkatan'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $urutan = (int) ($_POST['urutan'] ?? 0);
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    if ($judul === '') {

        $error = 'Judul program wajib diisi.';

    } else {

        $foto = uploadFotoProgram(
            $_FILES['foto'] ?? null,
            $folderUpload
        );

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE &&
            $foto === false
        ) {

            $error = 'Foto gagal diupload. Pastikan JPG, JPEG, PNG, atau WEBP dan ukuran maksimal 5 MB.';

        } else {

            $stmt = $koneksi->prepare("
                INSERT INTO program_sekolah
                (
                    nomor,
                    ikon,
                    judul,
                    singkatan,
                    deskripsi,
                    foto,
                    urutan,
                    aktif
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nomor,
                $ikon,
                $judul,
                $singkatan,
                $deskripsi,
                $foto,
                $urutan,
                $aktif
            ]);

            $pesan = 'Program berhasil ditambahkan.';
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

    $idEdit = (int) $_GET['edit'];

    $stmt = $koneksi->prepare(
        "SELECT * FROM program_sekolah WHERE id = ?"
    );

    $stmt->execute([$idEdit]);

    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| UPDATE PROGRAM
|--------------------------------------------------------------------------
*/

if (isset($_POST['update_program'])) {

    $id = (int) ($_POST['id'] ?? 0);

    $nomor = trim($_POST['nomor'] ?? '');
    $ikon = trim($_POST['ikon'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $singkatan = trim($_POST['singkatan'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $urutan = (int) ($_POST['urutan'] ?? 0);
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    if ($judul === '') {

        $error = 'Judul program wajib diisi.';

    } else {

        $stmt = $koneksi->prepare(
            "SELECT foto FROM program_sekolah WHERE id = ?"
        );

        $stmt->execute([$id]);

        $programLama = $stmt->fetch(PDO::FETCH_ASSOC);

        $fotoBaru = uploadFotoProgram(
            $_FILES['foto'] ?? null,
            $folderUpload
        );

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE &&
            $fotoBaru === false
        ) {

            $error = 'Foto gagal diupload. Pastikan JPG, JPEG, PNG, atau WEBP dan ukuran maksimal 5 MB.';

        } else {

            if ($fotoBaru !== null) {

                if (
                    $programLama &&
                    !empty($programLama['foto'])
                ) {

                    $fotoLama = $folderUpload . $programLama['foto'];

                    if (file_exists($fotoLama)) {
                        unlink($fotoLama);
                    }
                }

                $foto = $fotoBaru;

            } else {

                $foto = $programLama['foto'] ?? null;
            }

            $stmt = $koneksi->prepare("
                UPDATE program_sekolah
                SET
                    nomor = ?,
                    ikon = ?,
                    judul = ?,
                    singkatan = ?,
                    deskripsi = ?,
                    foto = ?,
                    urutan = ?,
                    aktif = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nomor,
                $ikon,
                $judul,
                $singkatan,
                $deskripsi,
                $foto,
                $urutan,
                $aktif,
                $id
            ]);

            $pesan = 'Program berhasil diperbarui.';

            $dataEdit = null;
        }
    }
}

/*
|--------------------------------------------------------------------------
| AMBIL SEMUA PROGRAM
|--------------------------------------------------------------------------
*/

$stmt = $koneksi->query("
    SELECT *
    FROM program_sekolah
    ORDER BY urutan ASC, id ASC
");

$program = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HEADER ADMIN
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


    <!-- =========================================================
         FORM TAMBAH / EDIT PROGRAM
         ========================================================= -->

    <div class="kartu-panel">

        <div class="baris-header-panel">

            <div>
                <h2>
                    <?= $dataEdit ? 'Edit Program' : 'Tambah Program' ?>
                </h2>

                <p style="margin:0;color:#5C574C;">
                    Kelola program yang tampil di website sekolah.
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

                <label for="nomor">
                    Nomor
                </label>

                <input
                    type="text"
                    id="nomor"
                    name="nomor"
                    placeholder="01"
                    value="<?= htmlspecialchars($dataEdit['nomor'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="ikon">
                    Ikon
                </label>

                <input
                    type="text"
                    id="ikon"
                    name="ikon"
                    placeholder="🎓"
                    value="<?= htmlspecialchars($dataEdit['ikon'] ?? '') ?>"
                >

                <div class="bantuan-form">
                    Bisa menggunakan emoji, misalnya 🎓, 💼, 🇯🇵, 💻.
                </div>

            </div>


            <div class="grup-form">

                <label for="judul">
                    Nama Program *
                </label>

                <input
                    type="text"
                    id="judul"
                    name="judul"
                    required
                    placeholder="Contoh: Praktik Kerja Lapangan"
                    value="<?= htmlspecialchars($dataEdit['judul'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="singkatan">
                    Singkatan
                </label>

                <input
                    type="text"
                    id="singkatan"
                    name="singkatan"
                    placeholder="PKL"
                    value="<?= htmlspecialchars($dataEdit['singkatan'] ?? '') ?>"
                >

            </div>


            <div class="grup-form">

                <label for="deskripsi">
                    Deskripsi
                </label>

                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    placeholder="Tuliskan penjelasan tentang program..."
                ><?= htmlspecialchars($dataEdit['deskripsi'] ?? '') ?></textarea>

            </div>


            <div class="grup-form">

                <label for="foto">
                    Foto Program
                </label>

                <?php if (
                    $dataEdit &&
                    !empty($dataEdit['foto'])
                ): ?>

                    <img
                        src="<?= BASE_URL ?>/uploads/program/<?= htmlspecialchars($dataEdit['foto']) ?>"
                        alt="Foto program"
                        class="mini-foto"
                        style="width:150px;height:100px;object-fit:cover;margin-bottom:10px;"
                    >

                    <br>

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


            <div class="grup-form">

                <label for="urutan">
                    Urutan
                </label>

                <input
                    type="number"
                    id="urutan"
                    name="urutan"
                    min="0"
                    value="<?= htmlspecialchars($dataEdit['urutan'] ?? '0') ?>"
                >

                <div class="bantuan-form">
                    Semakin kecil angkanya, semakin awal tampil.
                </div>

            </div>


            <div class="grup-form">

                <label>

                    <input
                        type="checkbox"
                        name="aktif"
                        value="1"
                        <?= (
                            !isset($dataEdit['aktif']) ||
                            (int) $dataEdit['aktif'] === 1
                        ) ? 'checked' : '' ?>
                    >

                    Tampilkan di website

                </label>

            </div>


            <div>

                <?php if ($dataEdit): ?>

                    <button
                        type="submit"
                        name="update_program"
                        class="tombol"
                    >
                        Simpan Perubahan
                    </button>

                    <a
                        href="program.php"
                        class="tombol tombol-garis"
                    >
                        Batal
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="tambah_program"
                        class="tombol"
                    >
                        + Tambah Program
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =========================================================
         DAFTAR PROGRAM
         ========================================================= -->

    <div class="kartu-panel">

        <div class="baris-header-panel">

            <div>

                <h2>
                    Daftar Program
                </h2>

                <p style="margin:0;color:#5C574C;">
                    <?= count($program) ?> program tersimpan.
                </p>

            </div>

        </div>


        <?php if (count($program) > 0): ?>

            <div style="overflow-x:auto;">

                <table class="tabel-admin">

                    <thead>

                        <tr>

                            <th>Foto</th>

                            <th>Program</th>

                            <th>Singkatan</th>

                            <th>Urutan</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($program as $p): ?>

                            <tr>

                                <td>

                                    <?php if (!empty($p['foto'])): ?>

                                        <img
                                            src="<?= BASE_URL ?>/uploads/program/<?= htmlspecialchars($p['foto']) ?>"
                                            alt="<?= htmlspecialchars($p['judul']) ?>"
                                            class="mini-foto"
                                        >

                                    <?php else: ?>

                                        <span
                                            style="
                                                display:inline-flex;
                                                width:48px;
                                                height:48px;
                                                align-items:center;
                                                justify-content:center;
                                                background:#F6F2E9;
                                                border-radius:4px;
                                                font-size:22px;
                                            "
                                        >
                                            <?= htmlspecialchars($p['ikon'] ?: '📌') ?>
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars($p['judul']) ?>
                                    </strong>

                                    <?php if (!empty($p['deskripsi'])): ?>

                                        <div
                                            style="
                                                font-size:0.8rem;
                                                color:#5C574C;
                                                max-width:350px;
                                            "
                                        >
                                            <?= htmlspecialchars($p['deskripsi']) ?>
                                        </div>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars($p['singkatan'] ?: '-') ?>

                                </td>


                                <td>

                                    <?= (int) $p['urutan'] ?>

                                </td>


                                <td>

                                    <?php if ((int) $p['aktif'] === 1): ?>

                                        <span class="lencana">
                                            Aktif
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="lencana"
                                            style="
                                                background:#FBE6E4;
                                                color:#8A2C22;
                                            "
                                        >
                                            Nonaktif
                                        </span>

                                    <?php endif; ?>

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
                                            href="program.php?edit=<?= (int) $p['id'] ?>"
                                            class="tombol tombol-kecil tombol-garis"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="program.php?hapus=<?= (int) $p['id'] ?>"
                                            class="tombol tombol-kecil tombol-bahaya"
                                            onclick="return confirm('Yakin ingin menghapus program ini?')"
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

                Belum ada program.

            </div>

        <?php endif; ?>

    </div>

</div>


<?php

require_once __DIR__ . '/includes/layout_bawah.php';

?>