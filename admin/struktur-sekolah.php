<?php

require_once __DIR__ . '/auth.php';

$folderFoto = __DIR__ . '/../uploads/struktur/';

if (!is_dir($folderFoto)) {
    mkdir($folderFoto, 0755, true);
}

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nama = trim($_POST['nama'] ?? '');

    $stmt = $koneksi->prepare(
        "SELECT * FROM struktur_sekolah WHERE id = ?"
    );
    $stmt->execute([$id]);
    $dataLama = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataLama) {
        $error = 'Data jabatan tidak ditemukan.';
    } elseif ($nama === '') {
        $error = 'Nama tidak boleh kosong.';
    } elseif (mb_strlen($nama) > 150) {
        $error = 'Nama maksimal 150 karakter.';
    } else {

        $fotoBaru = $dataLama['foto'];

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['foto'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Foto gagal diunggah.';
            } elseif ($file['size'] > 3 * 1024 * 1024) {
                $error = 'Ukuran foto maksimal 3 MB.';
            } else {
                $info = new finfo(FILEINFO_MIME_TYPE);
                $mime = $info->file($file['tmp_name']);

                $ekstensiValid = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($ekstensiValid[$mime])) {
                    $error = 'Foto harus berformat JPG, PNG, atau WEBP.';
                } elseif (
                    @getimagesize($file['tmp_name']) === false
                ) {
                    $error = 'File yang diunggah bukan gambar yang valid.';
                } else {
                    $namaFile = bin2hex(random_bytes(16))
                        . '.'
                        . $ekstensiValid[$mime];

                    if (
                        move_uploaded_file(
                            $file['tmp_name'],
                            $folderFoto . $namaFile
                        )
                    ) {
                        $fotoBaru = $namaFile;
                    } else {
                        $error = 'Foto gagal disimpan.';
                    }
                }
            }
        }

        if ($error === '') {
            $update = $koneksi->prepare(
                "UPDATE struktur_sekolah
                 SET nama = ?, foto = ?
                 WHERE id = ?"
            );

            $update->execute([
                $nama,
                $fotoBaru,
                $id
            ]);

            if (
                $fotoBaru !== $dataLama['foto'] &&
                !empty($dataLama['foto'])
            ) {
                $fotoLama = basename($dataLama['foto']);
                $pathLama = $folderFoto . $fotoLama;

                if (is_file($pathLama)) {
                    unlink($pathLama);
                }
            }

            header('Location: struktur-sekolah.php?status=berhasil');
            exit;
        }
    }
}

$daftar = $koneksi->query(
    "SELECT * FROM struktur_sekolah ORDER BY urutan ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$judul_halaman = 'Struktur Organisasi Sekolah';

require_once __DIR__ . '/includes/layout_atas.php';
?>
<!-- NAVIGASI PENGELOLAAN -->
<div class="kartu-panel" style="margin-bottom:20px;">

    <h3 style="margin-top:0;">Pengelolaan Sekolah</h3>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">

        <a
            href="<?= BASE_URL ?>/admin/guru.php"
            class="tombol tombol-garis"
        >
            👨‍🏫 Kembali ke Data Guru &amp; Staf
        </a>

        <a
            href="<?= BASE_URL ?>/admin/struktur-sekolah.php"
            class="tombol"
        >
            🏫 Struktur Sekolah
        </a>

    </div>

</div>

<style>
.panel-struktur {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.kartu-struktur {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
}

.kartu-struktur h3 {
    font-size: 15px;
    margin: 0 0 15px;
    line-height: 1.5;
}

.foto-struktur {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    display: block;
    margin-bottom: 15px;
    background: #f1f5f9;
}

.input-struktur {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    margin: 7px 0 14px;
}

.label-struktur {
    display: block;
    font-size: 13px;
    font-weight: 600;
}

.tombol-simpan-struktur {
    background: #176b50;
    color: white;
    border: 0;
    border-radius: 8px;
    padding: 11px 16px;
    cursor: pointer;
}

.info-struktur {
    background: #ecfdf5;
    color: #166534;
    padding: 12px;
    border-radius: 8px;
    margin: 15px 0;
}

.error-struktur {
    background: #fef2f2;
    color: #991b1b;
    padding: 12px;
    border-radius: 8px;
    margin: 15px 0;
}
</style>

<div class="baris-header-panel">
    <h3 style="margin:0;">Pengaturan Struktur Organisasi</h3>
</div>

<p>
    Ubah nama dan foto sesuai orang yang menjabat.
    Susunan jabatan sudah ditetapkan dan tidak dapat diubah dari halaman ini.
</p>

<?php if (isset($_GET['status']) && $_GET['status'] === 'berhasil'): ?>
    <div class="info-struktur">
        Data struktur organisasi berhasil diperbarui!
    </div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="error-struktur">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="panel-struktur">

    <?php foreach ($daftar as $item): ?>

        <div class="kartu-struktur">

            <h3>
                <?= htmlspecialchars($item['jabatan']) ?>
            </h3>

            <?php if (!empty($item['foto'])): ?>

                <img
                    class="foto-struktur"
                    src="<?= BASE_URL ?>/uploads/struktur/<?= rawurlencode(basename($item['foto'])) ?>"
                    alt="Foto <?= htmlspecialchars($item['jabatan']) ?>"
                >

            <?php else: ?>

                <div class="foto-struktur"
                     style="display:flex;align-items:center;justify-content:center;">
                    Belum ada foto
                </div>

            <?php endif; ?>

            <form method="POST"
                  enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $item['id'] ?>"
                >

                <label class="label-struktur">
                    Nama lengkap
                </label>

                <input
                    class="input-struktur"
                    type="text"
                    name="nama"
                    maxlength="150"
                    value="<?= htmlspecialchars($item['nama']) ?>"
                    required
                >

                <label class="label-struktur">
                    Foto (JPG, PNG, WEBP; maksimal 3 MB)
                </label>

                <input
                    class="input-struktur"
                    type="file"
                    name="foto"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <button
                    class="tombol-simpan-struktur"
                    type="submit"
                >
                    Simpan Perubahan
                </button>

            </form>

        </div>

    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>