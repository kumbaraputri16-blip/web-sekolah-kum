<?php
require_once __DIR__ . '/auth.php';

$pesan = '';
$error = '';

$folderUpload = __DIR__ . '/../unggah/mitra/';

if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0755, true);
}

// Fungsi upload logo perusahaan
function uploadLogoMitra($file, $folderUpload)
{
    if (
        !isset($file) ||
        !isset($file['error']) ||
        $file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload logo gagal.');
    }

    if ($file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Ukuran logo maksimal 3 MB.');
    }

    $info = @getimagesize($file['tmp_name']);

    if ($info === false) {
        throw new RuntimeException('File harus berupa gambar.');
    }

    $mime = $info['mime'];

    $ekstensiDiizinkan = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($ekstensiDiizinkan[$mime])) {
        throw new RuntimeException(
            'Format logo harus JPG, PNG, atau WEBP.'
        );
    }

    $namaFile = bin2hex(random_bytes(16))
        . '.'
        . $ekstensiDiizinkan[$mime];

    if (!move_uploaded_file(
        $file['tmp_name'],
        $folderUpload . $namaFile
    )) {
        throw new RuntimeException('Logo gagal disimpan.');
    }

    return $namaFile;
}

// Tambah perusahaan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    try {
        $nama = trim($_POST['nama_perusahaan'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($nama === '') {
            throw new RuntimeException(
                'Nama perusahaan wajib diisi.'
            );
        }

        $logo = uploadLogoMitra(
            $_FILES['logo'] ?? null,
            $folderUpload
        );

        $stmt = $koneksi->prepare(
            'INSERT INTO mitra_industri
            (nama_perusahaan, deskripsi, logo)
            VALUES (?, ?, ?)'
        );

        $stmt->execute([$nama, $deskripsi, $logo]);

        header('Location: edit-mitra.php?status=tambah');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

// Edit perusahaan
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['simpan_edit'])
) {
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama_perusahaan'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($id <= 0 || $nama === '') {
            throw new RuntimeException(
                'Data perusahaan tidak valid.'
            );
        }

        $stmt = $koneksi->prepare(
            'SELECT logo FROM mitra_industri WHERE id = ?'
        );
        $stmt->execute([$id]);
        $dataLama = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dataLama) {
            throw new RuntimeException(
                'Data perusahaan tidak ditemukan.'
            );
        }

        $logoBaru = uploadLogoMitra(
            $_FILES['logo'] ?? null,
            $folderUpload
        );

        $logo = $logoBaru ?? $dataLama['logo'];

        $stmt = $koneksi->prepare(
            'UPDATE mitra_industri
             SET nama_perusahaan = ?, deskripsi = ?, logo = ?
             WHERE id = ?'
        );

        $stmt->execute([
            $nama,
            $deskripsi,
            $logo,
            $id
        ]);

        if (
            $logoBaru !== null &&
            !empty($dataLama['logo'])
        ) {
            $fileLama = $folderUpload . basename($dataLama['logo']);

            if (is_file($fileLama)) {
                unlink($fileLama);
            }
        }

        header('Location: edit-mitra.php?status=edit');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

// Hapus perusahaan
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['hapus'])
) {
    try {
        $id = (int) ($_POST['id'] ?? 0);

        $stmt = $koneksi->prepare(
            'SELECT logo FROM mitra_industri WHERE id = ?'
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new RuntimeException(
                'Data perusahaan tidak ditemukan.'
            );
        }

        $stmt = $koneksi->prepare(
            'DELETE FROM mitra_industri WHERE id = ?'
        );
        $stmt->execute([$id]);

        if (!empty($data['logo'])) {
            $fileLogo = $folderUpload . basename($data['logo']);

            if (is_file($fileLogo)) {
                unlink($fileLogo);
            }
        }

        header('Location: edit-mitra.php?status=hapus');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

// Ambil data untuk diedit
$dataEdit = null;

if (isset($_GET['edit'])) {
    $idEdit = (int) $_GET['edit'];

    $stmt = $koneksi->prepare(
        'SELECT * FROM mitra_industri WHERE id = ?'
    );
    $stmt->execute([$idEdit]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil semua data perusahaan
$stmt = $koneksi->query(
    'SELECT * FROM mitra_industri ORDER BY id DESC'
);
$dataMitra = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pesan berhasil
$status = $_GET['status'] ?? '';

if ($status === 'tambah') {
    $pesan = 'Mitra industri berhasil ditambahkan.';
} elseif ($status === 'edit') {
    $pesan = 'Data mitra industri berhasil diperbarui.';
} elseif ($status === 'hapus') {
    $pesan = 'Mitra industri berhasil dihapus.';
}
?>

<?php require_once __DIR__ . '/includes/layout_atas.php'; ?>

<style>
    .mitra-admin {
        max-width: 1100px;
        margin: 30px auto;
        padding: 0 20px;
    }

    .mitra-admin h1 {
        margin-bottom: 8px;
    }

    .mitra-admin .deskripsi-halaman {
        color: #666;
        margin-bottom: 25px;
    }

    .mitra-box {
        background: #fff;
        padding: 24px;
        margin-bottom: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0,0,0,.07);
    }

    .mitra-box h2 {
        margin-top: 0;
        margin-bottom: 20px;
    }

    .mitra-form label {
        display: block;
        font-weight: 600;
        margin: 15px 0 7px;
    }

    .mitra-form input[type="text"],
    .mitra-form textarea,
    .mitra-form input[type="file"] {
        width: 100%;
        padding: 11px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    .mitra-form textarea {
        min-height: 120px;
        resize: vertical;
    }

    .mitra-btn {
        display: inline-block;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        margin-top: 15px;
    }

    .mitra-btn-simpan {
        background: #1c2b39;
        color: white;
    }

    .mitra-btn-edit {
        background: #e3a93b;
        color: #221f1a;
        margin: 0;
    }

    .mitra-btn-hapus {
        background: #c0392b;
        color: white;
        margin: 0;
    }

    .mitra-btn-kembali {
        background: #eee;
        color: #222;
        margin: 0 0 20px;
    }

    .mitra-pesan {
        padding: 13px 16px;
        background: #e3f6e8;
        color: #206b35;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .mitra-error {
        padding: 13px 16px;
        background: #fde8e7;
        color: #a12622;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .mitra-tabel {
        width: 100%;
        border-collapse: collapse;
    }

    .mitra-tabel th,
    .mitra-tabel td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        text-align: left;
        vertical-align: middle;
    }

    .mitra-tabel th {
        background: #f5f5f5;
    }

    .mitra-logo-preview {
        width: 100px;
        height: 75px;
        object-fit: contain;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 5px;
    }

    .mitra-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .mitra-tabel form {
        margin: 0;
    }

    .mitra-catatan {
        color: #777;
        font-size: 13px;
        margin-top: 6px;
    }

    @media (max-width: 700px) {
        .mitra-tabel {
            font-size: 13px;
        }

        .mitra-tabel th,
        .mitra-tabel td {
            padding: 7px;
        }

        .mitra-logo-preview {
            width: 65px;
            height: 55px;
        }
    }
</style>

<div class="mitra-admin">

    <a href="dashboard.php" class="mitra-btn mitra-btn-kembali">
        ← Kembali ke Dashboard
    </a>

    <h1>Kelola Mitra Industri</h1>

    <p class="deskripsi-halaman">
        Tambahkan perusahaan mitra sekolah beserta logo dan deskripsinya.
    </p>

    <?php if ($pesan !== ''): ?>
        <div class="mitra-pesan">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="mitra-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <section class="mitra-box">
        <h2>
            <?= $dataEdit ? 'Edit Mitra Industri' : 'Tambah Mitra Industri' ?>
        </h2>

        <form
            class="mitra-form"
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

            <label for="nama_perusahaan">Nama Perusahaan / PT *</label>

            <input
                type="text"
                id="nama_perusahaan"
                name="nama_perusahaan"
                maxlength="200"
                required
                value="<?= htmlspecialchars(
                    $dataEdit['nama_perusahaan'] ?? ''
                ) ?>"
                placeholder="Contoh: PT Nama Perusahaan"
            >

            <label for="deskripsi">Deskripsi Perusahaan</label>

            <textarea
                id="deskripsi"
                name="deskripsi"
                placeholder="Jelaskan kerja sama perusahaan dengan sekolah..."
            ><?= htmlspecialchars($dataEdit['deskripsi'] ?? '') ?></textarea>

            <label for="logo">Logo Perusahaan</label>

            <input
                type="file"
                id="logo"
                name="logo"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            >

            <p class="mitra-catatan">
                Format JPG, PNG, atau WEBP. Maksimal 3 MB.
                <?php if ($dataEdit): ?>
                    Kosongkan jika tidak ingin mengganti logo.
                <?php endif; ?>
            </p>

            <?php if ($dataEdit && !empty($dataEdit['logo'])): ?>
                <p>Logo saat ini:</p>

                <img
                    class="mitra-logo-preview"
                    src="<?= BASE_URL ?>/unggah/mitra/<?= rawurlencode(basename($dataEdit['logo'])) ?>"
                    alt="Logo perusahaan"
                >
            <?php endif; ?>

            <button
                type="submit"
                name="<?= $dataEdit ? 'simpan_edit' : 'tambah' ?>"
                class="mitra-btn mitra-btn-simpan"
            >
                <?= $dataEdit ? 'Simpan Perubahan' : 'Tambah Perusahaan' ?>
            </button>

            <?php if ($dataEdit): ?>
                <a href="edit-mitra.php" class="mitra-btn mitra-btn-kembali">
                    Batal Edit
                </a>
            <?php endif; ?>

        </form>
    </section>

    <section class="mitra-box">
        <h2>Daftar Mitra Industri</h2>

        <?php if (empty($dataMitra)): ?>

            <p>Belum ada perusahaan yang ditambahkan.</p>

        <?php else: ?>

            <div style="overflow-x:auto;">
                <table class="mitra-tabel">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Logo</th>
                            <th>Nama Perusahaan</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($dataMitra as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td>
                                    <?php if (!empty($item['logo'])): ?>
                                        <img
                                            class="mitra-logo-preview"
                                            src="<?= BASE_URL ?>/unggah/mitra/<?= rawurlencode(basename($item['logo'])) ?>"
                                            alt="Logo <?= htmlspecialchars($item['nama_perusahaan']) ?>"
                                        >
                                    <?php else: ?>
                                        Belum ada logo
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($item['nama_perusahaan']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= nl2br(htmlspecialchars(
                                        $item['deskripsi'] ?? ''
                                    )) ?>
                                </td>

                                <td>
                                    <div class="mitra-aksi">
                                        <a
                                            class="mitra-btn mitra-btn-edit"
                                            href="edit-mitra.php?edit=<?= (int) $item['id'] ?>"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?')"
                                        >
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $item['id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="hapus"
                                                class="mitra-btn mitra-btn-hapus"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>

</div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>