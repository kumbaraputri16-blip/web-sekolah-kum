<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/includes/unggah_foto.php';

// Variabel pesan
$statusPesan = '';
$statusTipe = '';

// Ambil status dari URL
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'tambah') {
        $statusPesan = 'Fasilitas berhasil ditambahkan.';
        $statusTipe = 'berhasil';
    } elseif ($_GET['status'] === 'hapus') {
        $statusPesan = 'Fasilitas berhasil dihapus.';
        $statusTipe = 'berhasil';
    }
}

// Proses formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    // TAMBAH FASILITAS
    if ($aksi === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($nama === '') {
            $statusPesan = 'Nama fasilitas wajib diisi.';
            $statusTipe = 'gagal';
        } elseif (
            !isset($_FILES['foto']) ||
            $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE
        ) {
            $statusPesan = 'Silakan pilih foto fasilitas.';
            $statusTipe = 'gagal';
        } else {
            $hasilUnggah = tanganiUnggahFoto('foto', 'fasilitas');

            if (empty($hasilUnggah['sukses'])) {
                $statusPesan = $hasilUnggah['pesan'] ?? 'Foto gagal diunggah.';
                $statusTipe = 'gagal';
            } elseif (empty($hasilUnggah['namaFile'])) {
                $statusPesan = 'Nama file foto tidak ditemukan setelah diunggah.';
                $statusTipe = 'gagal';
            } else {
                try {
                    $foto = $hasilUnggah['namaFile'];

                    $stmt = $koneksi->prepare(
                        'INSERT INTO fasilitas (nama, deskripsi, foto)
                         VALUES (:nama, :deskripsi, :foto)'
                    );

                    $stmt->execute([
                        ':nama' => $nama,
                        ':deskripsi' => $deskripsi,
                        ':foto' => $foto
                    ]);

                    header('Location: fasilitas.php?status=tambah');
                    exit;
                } catch (PDOException $e) {
                    if (!empty($foto)) {
                        hapusFotoLama($foto, 'fasilitas');
                    }

                    $statusPesan = 'Gagal menyimpan fasilitas ke database. Periksa struktur tabel fasilitas.';
                    $statusTipe = 'gagal';

                    error_log('Gagal menyimpan fasilitas: ' . $e->getMessage());
                }
            }
        }
    }

    // HAPUS FASILITAS
    if ($aksi === 'hapus') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            try {
                $stmt = $koneksi->prepare(
                    'SELECT foto FROM fasilitas WHERE id = ? LIMIT 1'
                );
                $stmt->execute([$id]);

                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($data) {
                    $stmt = $koneksi->prepare(
                        'DELETE FROM fasilitas WHERE id = ?'
                    );
                    $stmt->execute([$id]);

                    hapusFotoLama($data['foto'] ?? null, 'fasilitas');
                }

                header('Location: fasilitas.php?status=hapus');
                exit;
            } catch (PDOException $e) {
                $statusPesan = 'Gagal menghapus fasilitas.';
                $statusTipe = 'gagal';

                error_log('Gagal menghapus fasilitas: ' . $e->getMessage());
            }
        }
    }
}

// Ambil daftar fasilitas
try {
    $stmt = $koneksi->query(
        'SELECT * FROM fasilitas ORDER BY id DESC'
    );

    $daftarFasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $daftarFasilitas = [];
    $statusPesan = 'Data fasilitas gagal dibaca dari database. Periksa tabel fasilitas.';
    $statusTipe = 'gagal';

    error_log('Gagal membaca fasilitas: ' . $e->getMessage());
}

$judul_halaman = 'Sarana dan Prasarana';

require_once __DIR__ . '/includes/layout_atas.php';
?>

<div class="konten-admin">

    <div class="judul-konten">
        <h1>Sarana dan Prasarana</h1>
        <p>Kelola data dan foto fasilitas sekolah.</p>
    </div>

    <?php if ($statusPesan !== ''): ?>
        <div class="alert <?= $statusTipe === 'berhasil' ? 'alert-sukses' : 'alert-gagal' ?>">
            <?= htmlspecialchars($statusPesan, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section class="kartu-admin">
        <h2>Tambah Fasilitas Sekolah</h2>
        <p>Isi informasi fasilitas dan pilih foto yang ingin ditampilkan di website.</p>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="aksi" value="tambah">

            <div class="form-group">
                <label for="nama">Nama fasilitas</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    maxlength="150"
                    required
                >
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi fasilitas</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="4"
                ></textarea>
            </div>

            <div class="form-group">
                <label for="foto">Foto fasilitas</label>
                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    required
                >
                <small>Gunakan foto JPG, PNG, atau WEBP.</small>
            </div>

            <button type="submit" class="tombol-admin">
                + Tambahkan Fasilitas
            </button>

        </form>
    </section>

    <section class="kartu-admin">
        <h2>Daftar Fasilitas</h2>

        <?php if (empty($daftarFasilitas)): ?>

            <p>Belum ada fasilitas yang ditambahkan.</p>

        <?php else: ?>

            <div class="daftar-fasilitas-admin">

                <?php foreach ($daftarFasilitas as $fasilitas): ?>

                    <article class="item-fasilitas-admin">

                        <?php if (!empty($fasilitas['foto'])): ?>
                            <img
                                src="<?= htmlspecialchars(
                                    BASE_URL . '/uploads/fasilitas/' . rawurlencode(basename($fasilitas['foto'])),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars($fasilitas['nama'], ENT_QUOTES, 'UTF-8') ?>"
                                class="foto-fasilitas-admin"
                            >
                        <?php endif; ?>

                        <div class="detail-fasilitas-admin">
                            <h3>
                                <?= htmlspecialchars($fasilitas['nama'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p>
                                <?= nl2br(htmlspecialchars($fasilitas['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                            </p>

                            <form
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')"
                            >
                                <input type="hidden" name="aksi" value="hapus">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $fasilitas['id'] ?>"
                                >

                                <button type="submit" class="tombol-hapus">
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</div>

<style>
.konten-admin {
    padding: 24px;
}

.judul-konten {
    margin-bottom: 24px;
}

.kartu-admin {
    background: #fff;
    padding: 24px;
    margin-bottom: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input[type="text"],
.form-group textarea {
    box-sizing: border-box;
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 7px;
}

.form-group input[type="file"] {
    display: block;
    max-width: 100%;
}

.form-group small {
    display: block;
    margin-top: 7px;
    color: #666;
}

.tombol-admin,
.tombol-hapus {
    padding: 10px 16px;
    border: none;
    border-radius: 7px;
    cursor: pointer;
}

.tombol-admin {
    background: #176b45;
    color: #fff;
}

.tombol-hapus {
    background: #b42318;
    color: #fff;
}

.alert {
    padding: 12px 16px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.alert-sukses {
    background: #e1f5e8;
    color: #176b45;
}

.alert-gagal {
    background: #fde8e7;
    color: #a61b1b;
}

.daftar-fasilitas-admin {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.item-fasilitas-admin {
    overflow: hidden;
    border: 1px solid #ddd;
    border-radius: 10px;
}

.foto-fasilitas-admin {
    display: block;
    width: 100%;
    height: 190px;
    object-fit: cover;
}

.detail-fasilitas-admin {
    padding: 16px;
}

.detail-fasilitas-admin h3 {
    margin-top: 0;
}

@media (max-width: 600px) {
    .konten-admin {
        padding: 12px;
    }

    .kartu-admin {
        padding: 16px;
    }
}
</style>

<?php require_once __DIR__ . '/includes/layout_bawah.php'; ?>