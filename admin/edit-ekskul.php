<?php
require_once __DIR__ . '/auth.php';

$pesan = '';
$error = '';

$folderUpload = '../unggah/ekskul/';

if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0755, true);
}



// Tambah ekstrakurikuler
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['tambah'])) {

    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $foto = null;   

    if ($nama === '') {
        $error = 'Nama ekstrakurikuler wajib diisi.';
    } else {
        try {
            if (isset($_FILES['foto'])
                && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

                if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Foto gagal diunggah.');
                }

                if ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
                    throw new Exception('Ukuran foto maksimal 3 MB.');
                }

                $info = getimagesize($_FILES['foto']['tmp_name']);

                if ($info === false) {
                    throw new Exception('File harus berupa gambar.');
                }

                $mime = $info['mime'];
                $ekstensi = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($ekstensi[$mime])) {
                    throw new Exception('Gunakan JPG, PNG, atau WebP.');
                }

                $namaFile = bin2hex(random_bytes(16))
                    . '.' . $ekstensi[$mime];

                if (!move_uploaded_file(
                    $_FILES['foto']['tmp_name'],
                    $folderUpload . $namaFile
                )) {
                    throw new Exception('Foto gagal disimpan.');
                }

                $foto = $namaFile;
            }

            $stmt = $koneksi->prepare(
                'INSERT INTO ekstrakurikuler (nama, deskripsi, foto)
                 VALUES (?, ?, ?)'
            );

            $stmt->execute([$nama, $deskripsi, $foto]);

            header('Location: edit-ekskul.php?status=tambah');
            exit;

        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
    
}
// PROSES EDIT EKSKUL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_edit'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    try {
        if ($id <= 0 || $nama === '') {
            throw new Exception('ID dan nama ekskul wajib diisi.');
        }

        // Ambil data ekskul lama
        $stmt = $koneksi->prepare(
            "SELECT foto FROM ekstrakurikuler WHERE id = ?"
        );
        $stmt->execute([$id]);
        $ekskulLama = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ekskulLama) {
            throw new Exception('Data ekskul tidak ditemukan.');
        }

        // Pertahankan foto lama jika tidak ada foto baru
        $foto = $ekskulLama['foto'];

        // Jika admin mengunggah foto baru
        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Foto gagal diunggah.');
            }

            if ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
                throw new Exception('Ukuran foto maksimal 3 MB.');
            }

            $infoGambar = getimagesize($_FILES['foto']['tmp_name']);

            if ($infoGambar === false) {
                throw new Exception('File yang diunggah bukan gambar.');
            }

            $mime = $infoGambar['mime'];

            $ekstensiValid = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($ekstensiValid[$mime])) {
                throw new Exception(
                    'Format foto harus JPG, PNG, atau WEBP.'
                );
            }

            $folderUpload = __DIR__ . '/../unggah/ekskul/';

            if (!is_dir($folderUpload)) {
                mkdir($folderUpload, 0755, true);
            }

            $namaFotoBaru = bin2hex(random_bytes(16))
                . '.' . $ekstensiValid[$mime];

            if (!move_uploaded_file(
                $_FILES['foto']['tmp_name'],
                $folderUpload . $namaFotoBaru
            )) {
                throw new Exception('Foto baru gagal disimpan.');
            }

            $foto = $namaFotoBaru;
        }

        // Simpan perubahan ke database
        $stmt = $koneksi->prepare(
            "UPDATE ekstrakurikuler
             SET nama = ?, deskripsi = ?, foto = ?
             WHERE id = ?"
        );

        $stmt->execute([$nama, $deskripsi, $foto, $id]);

        // Hapus foto lama setelah foto baru berhasil disimpan
        if (
            $foto !== $ekskulLama['foto'] &&
            !empty($ekskulLama['foto'])
        ) {
            $fotoLama = __DIR__ . '/../unggah/ekskul/'
                . basename($ekskulLama['foto']);

            if (is_file($fotoLama)) {
                unlink($fotoLama);
            }
        }

        header('Location: edit-ekskul.php?status=edit');
        exit;

    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
// Hapus ekstrakurikuler
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['hapus'])) {

    $id = (int) $_POST['id'];

    try {
        $stmt = $koneksi->prepare(
            'SELECT foto FROM ekstrakurikuler WHERE id = ?'
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $stmt = $koneksi->prepare(
                'DELETE FROM ekstrakurikuler WHERE id = ?'
            );
            $stmt->execute([$id]);

            if (!empty($data['foto'])) {
                $path = $folderUpload . basename($data['foto']);

                if (is_file($path)) {
                    unlink($path);
                }
            }
        }

        header('Location: edit-ekskul.php?status=hapus');
        exit;

    } catch (Throwable $e) {
        $error = 'Data gagal dihapus: ' . $e->getMessage();
    }
}

/// Ambil data ekstrakurikuler
$stmt = $koneksi->query(
    'SELECT * FROM ekstrakurikuler ORDER BY id DESC'
);

$dataEkskul = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Ambil data ekskul yang akan diedit
$dataEdit = null;

if (isset($_GET['edit'])) {
    $idEdit = (int) $_GET['edit'];

    if ($idEdit > 0) {
        $stmtEdit = $koneksi->prepare(
            'SELECT * FROM ekstrakurikuler WHERE id = ?'
        );

        $stmtEdit->execute([$idEdit]);
        $dataEdit = $stmtEdit->fetch(PDO::FETCH_ASSOC);
    }
}

// Notifikasi
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'tambah') {
        $pesan = 'Ekstrakurikuler berhasil ditambahkan!';
    } elseif ($_GET['status'] === 'hapus') {
        $pesan = 'Ekstrakurikuler berhasil dihapus!';
    } elseif ($_GET['status'] === 'edit') {
        $pesan = 'Data ekstrakurikuler berhasil diperbarui!';
    }
}

?>

<?php require_once __DIR__ . '/includes/layout_atas.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ekstrakurikuler</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #1e293b;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            margin-bottom: 8px;
            color: #163b72;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 14px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,.04);
        }

        label {
            display: block;
            margin: 15px 0 8px;
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        button, .back {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }

        .submit {
            margin-top: 18px;
        }

        .delete {
            background: #dc2626;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            background: #dcfce7;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #e5eaf2;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f8fafc;
        }

        .foto {
            width: 85px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .card {
                padding: 16px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Kelola Ekstrakurikuler</h1>
        <p>Tambahkan dan kelola kegiatan ekstrakurikuler sekolah.</p>
        <a class="back" href="dashboard.php">← Kembali ke Dashboard</a>
    </div>

    <?php if ($pesan): ?>
        <div class="message">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Tambah Ekstrakurikuler</h2>

        <form method="POST" enctype="multipart/form-data">
            <label for="nama">Nama Ekstrakurikuler</label>
            <input
                type="text"
                id="nama"
                name="nama"
                maxlength="150"
                required
            >

            <label for="deskripsi">Deskripsi</label>
            <textarea
                id="deskripsi"
                name="deskripsi"
                placeholder="Jelaskan kegiatan ekstrakurikuler..."
            ></textarea>

            <label for="foto">Foto Kegiatan</label>
            <input
                type="file"
                id="foto"
                name="foto"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <button
                type="submit"
                name="tambah"
                class="submit"
            >
                + Tambah Ekstrakurikuler
            </button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Ekstrakurikuler</h2>

        <div class="table-wrap">
            <?php if (isset($_GET['edit']) && $dataEdit): ?>

    <div class="card" id="form-edit" style="margin-bottom:25px;">

        <h2>Edit Ekstrakurikuler</h2>

        <form method="POST" enctype="multipart/form-data">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $dataEdit['id'] ?>"
            >

            <label>Nama Ekstrakurikuler</label>

            <input
                type="text"
                name="nama"
                value="<?= htmlspecialchars($dataEdit['nama']) ?>"
                required
            >

            <label>Deskripsi</label>

            <textarea
                name="deskripsi"
                rows="5"
            ><?= htmlspecialchars($dataEdit['deskripsi'] ?? '') ?></textarea>

            <label>Foto Saat Ini</label>

            <?php if (!empty($dataEdit['foto'])): ?>
                <br>

                <img
                    src="<?= htmlspecialchars(
                        $folderUpload . rawurlencode($dataEdit['foto'])
                    ) ?>"
                    alt="Foto saat ini"
                    class="foto"
                    style="max-width:180px;"
                >

                <br>
            <?php else: ?>
                <p>Belum ada foto.</p>
            <?php endif; ?>

            <label>Ganti Foto (Opsional)</label>

            <input
                type="file"
                name="foto"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <br><br>

            <button type="submit" name="simpan_edit">
                Simpan Perubahan
            </button>

            <a
                href="edit-ekskul.php"
                style="margin-left:10px;"
            >
                Batal
            </a>

        </form>

    </div>

<?php elseif (isset($_GET['edit'])): ?>

    <p>Data ekstrakurikuler tidak ditemukan.</p>

<?php endif; ?>
            <table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!$dataEkskul): ?>
        <tr>
            <td colspan="5">
                Belum ada data ekstrakurikuler.
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($dataEkskul as $i => $item): ?>
            <tr>
                <td><?= $i + 1 ?></td>

                <td>
                    <?php if ($item['foto']): ?>
                        <img
                            class="foto"
                            src="<?= htmlspecialchars(
                                $folderUpload . rawurlencode($item['foto'])
                            ) ?>"
                            alt="Foto ekstrakurikuler"
                        >
                    <?php else: ?>
                        Belum ada foto
                    <?php endif; ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['nama']) ?>
                </td>

                <td>
                    <?= nl2br(htmlspecialchars(
                        $item['deskripsi'] ?? ''
                    )) ?>
                </td>

                <td>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">

                        <!-- Tombol Edit -->
                        <a
                            href="edit-ekskul.php?edit=<?= (int) $item['id'] ?>#form-edit"
                            class="edit"
                            style="padding:8px 12px; background:#2563eb; color:white; text-decoration:none; border-radius:5px;"
                        >
                            Edit
                        </a>

                        <!-- Tombol Hapus -->
                        <form
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                        >
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $item['id'] ?>"
                            >

                            <button
                                type="submit"
                                name="hapus"
                                class="delete"
                            >
                                Hapus
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
        </div>
    </div>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>