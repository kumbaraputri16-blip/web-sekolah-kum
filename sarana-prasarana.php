<?php
require_once __DIR__ . '/config/koneksi.php';

$judul_halaman = 'Sarana dan Prasarana';
$namaSekolah = 'SMK Infokom Kota Bogor';

$stmt = $koneksi->query(
    "SELECT * FROM fasilitas ORDER BY id DESC"
);
$daftarFasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main>

    <!-- BAGIAN ATAS -->
    <section class="header-halaman">
        <div class="container">

            <span class="kop">FASILITAS SEKOLAH</span>

            <h1>Sarana &amp; Prasarana</h1>

            <p>
                SMK Infokom Kota Bogor menyediakan berbagai sarana
                dan prasarana untuk mendukung kegiatan belajar mengajar,
                mengembangkan keterampilan, serta menciptakan lingkungan
                sekolah yang nyaman bagi seluruh warga sekolah.
            </p>

        </div>
    </section>

    <!-- BAGIAN INFORMASI -->
    <section class="bagian-halaman">
        <div class="container">

            <div class="judul-halaman">
                <h2>Fasilitas Sekolah</h2>

                <p>
                    Berbagai fasilitas yang mendukung proses pembelajaran
                    dan aktivitas siswa di lingkungan sekolah.
                </p>
            </div>

           <div class="grid-fasilitas">

    <?php if (empty($daftarFasilitas)): ?>

        <p class="fasilitas-kosong">
            Foto sarana dan prasarana belum tersedia.
        </p>

    <?php else: ?>

        <?php foreach ($daftarFasilitas as $fasilitas): ?>

            <article class="kartu-fasilitas">

                <?php if (!empty($fasilitas['foto'])): ?>

                    <img
                        src="uploads/fasilitas/<?= htmlspecialchars(
                            $fasilitas['foto'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        alt="<?= htmlspecialchars(
                            $fasilitas['nama'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="foto-fasilitas"
                        loading="lazy"
                    >

                <?php endif; ?>

                <div class="isi-fasilitas">

                    <h3>
                        <?= htmlspecialchars(
                            $fasilitas['nama'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>

                    <p>
                        <?= nl2br(htmlspecialchars(
                            $fasilitas['deskripsi'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        )) ?>
                    </p>

                </div>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>