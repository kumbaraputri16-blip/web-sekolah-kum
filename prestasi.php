<?php
require_once __DIR__ . '/config/koneksi.php';

$daftarPrestasi = $koneksi->query("
    SELECT *
    FROM foto_prestasi
    ORDER BY tanggal DESC, id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$profil = $koneksi->query("
    SELECT * FROM profil_sekolah LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$namaSekolah = $profil['nama_sekolah'] ?? 'SMK Infokom Kota Bogor';
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<style>
/* HALAMAN PRESTASI DAN KEGIATAN */

.halaman-prestasi {
    min-height: 70vh;
    padding: 65px 0 85px;
    background: #F6F2E9;
}

.prestasi-heading {
    text-align: center;
    max-width: 750px;
    margin: 0 auto 40px;
}

.prestasi-heading .label-kecil {
    display: inline-block;
    color: #94651C;
    font-weight: 700;
    letter-spacing: 2px;
    font-size: 12px;
    margin-bottom: 12px;
}

.prestasi-heading h1 {
    color: #1C2B39;
    font-size: clamp(28px, 4vw, 42px);
    margin-bottom: 14px;
}

.prestasi-heading p {
    color: #66717B;
    line-height: 1.8;
}

.prestasi-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}

.prestasi-kartu {
    overflow: hidden;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(28, 43, 57, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.prestasi-kartu:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(28, 43, 57, 0.14);
}

.prestasi-kartu img {
    display: block;
    width: 100%;
    height: 235px;
    object-fit: cover;
    cursor: pointer;
}

.prestasi-kartu-isi {
    padding: 21px;
}

.prestasi-kartu-isi h2 {
    font-size: 19px;
    line-height: 1.5;
    color: #1C2B39;
    margin: 0 0 10px;
}

.prestasi-tanggal {
    display: block;
    font-size: 12px;
    color: #94651C;
    margin-bottom: 12px;
}

.prestasi-kartu-isi p {
    font-size: 14px;
    color: #66717B;
    line-height: 1.8;
    margin: 0;
    white-space: pre-line;
}

.prestasi-kosong {
    text-align: center;
    padding: 50px 20px;
    background: #fff;
    border-radius: 16px;
    color: #66717B;
    line-height: 1.8;
}

.prestasi-kosong span {
    display: block;
    font-size: 48px;
    margin-bottom: 12px;
}

@media (max-width: 900px) {
    .prestasi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .halaman-prestasi {
        padding: 45px 0 60px;
    }

    .prestasi-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .prestasi-kartu img {
        height: 230px;
    }
}
</style>

<main class="halaman-prestasi">
    <div class="container">

        <header class="prestasi-heading">
            <span class="label-kecil">SMK INFOKOM KOTA BOGOR</span>

            <h1>Prestasi & Kegiatan Sekolah</h1>

            <p>
                <?= htmlspecialchars($namaSekolah, ENT_QUOTES, 'UTF-8') ?>
                terus mendorong siswa untuk berprestasi,
                berkarya, dan aktif mengikuti berbagai kegiatan.
                Berikut dokumentasi prestasi dan kegiatan sekolah.
            </p>
        </header>

        <?php if (!empty($daftarPrestasi)): ?>

            <div class="prestasi-grid">

                <?php foreach ($daftarPrestasi as $foto): ?>

                    <article class="prestasi-kartu">

                        <img
                            src="<?= BASE_URL ?>/uploads/prestasi/<?= rawurlencode(basename($foto['foto'])) ?>"
                            alt="<?= htmlspecialchars($foto['judul'], ENT_QUOTES, 'UTF-8') ?>"
                            loading="lazy"
                        >

                        <div class="prestasi-kartu-isi">

                            <h2>
                                <?= htmlspecialchars($foto['judul'], ENT_QUOTES, 'UTF-8') ?>
                            </h2>

                            <?php if (!empty($foto['tanggal'])): ?>
                                <time
                                    class="prestasi-tanggal"
                                    datetime="<?= htmlspecialchars($foto['tanggal'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <?= date('d F Y', strtotime($foto['tanggal'])) ?>
                                </time>
                            <?php endif; ?>

                            <?php if (!empty($foto['deskripsi'])): ?>
                                <p><?= htmlspecialchars($foto['deskripsi'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="prestasi-kosong">
                <span>🏆</span>

                <h2>Dokumentasi Segera Hadir</h2>

                <p>
                    Foto prestasi dan kegiatan sekolah belum tersedia.
                </p>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>