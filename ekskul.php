<?php
require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Ekstrakurikuler';

// Nama sekolah untuk navbar
$namaSekolah = 'SMK Infokom Kota Bogor';

// Ambil data ekskul dari database
$stmt = $koneksi->query(
    'SELECT * FROM ekstrakurikuler ORDER BY id DESC'
);

$dataEkskul = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Panggil header dan navbar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main>

    <!-- BAGIAN ATAS EKSKUL -->
    <section class="header-ekskul">
        <div class="container">

            <span class="kop">KEGIATAN SISWA</span>

            <h1>Ekstrakurikuler</h1>

            <p>
                Kegiatan ekstrakurikuler menjadi wadah bagi siswa
                SMK Infokom Kota Bogor untuk mengembangkan bakat,
                minat, kreativitas, kedisiplinan, dan kerja sama.
            </p>

        </div>
    </section>

    <!-- DAFTAR EKSKUL -->
    <section class="bagian-ekskul">
        <div class="container">

            <div class="judul-ekskul">
                <h2>Jelajahi Kegiatan Siswa</h2>

                <p>
                    Temukan berbagai kegiatan yang dapat membantu
                    siswa mengembangkan potensi dan keterampilannya.
                </p>
            </div>

            <?php if (!empty($dataEkskul)): ?>

                <div class="grid-ekskul">

                    <?php foreach ($dataEkskul as $item): ?>

                        <article class="kartu-ekskul">

                            <?php if (!empty($item['foto'])): ?>

                                <img
                                    src="<?= BASE_URL ?>/unggah/ekskul/<?= rawurlencode($item['foto']) ?>"
                                    alt="<?= htmlspecialchars($item['nama']) ?>"
                                    class="foto-ekskul"
                                    loading="lazy"
                                >

                            <?php endif; ?>

                            <div class="isi-kartu">

                                <h3>
                                    <?= htmlspecialchars($item['nama']) ?>
                                </h3>

                                <p>
                                    <?= nl2br(htmlspecialchars(
                                        $item['deskripsi'] ?? ''
                                    )) ?>
                                </p>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="kosong">
                    <p>
                        Belum ada kegiatan ekstrakurikuler yang ditambahkan.
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>