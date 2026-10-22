<?php

$judul_halaman = 'Program';

require_once __DIR__ . '/config/koneksi.php';

/*
|--------------------------------------------------------------------------
| AMBIL DATA PROGRAM SEKOLAH
|--------------------------------------------------------------------------
*/

$dataProgram = [];

try {
    $stmtProgram = $koneksi->query("
        SELECT *
        FROM program_sekolah
        WHERE aktif = 1
        ORDER BY urutan ASC, id ASC
    ");

    $dataProgram = $stmtProgram->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $dataProgram = [];
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA ALUMNI JEPANG
|--------------------------------------------------------------------------
*/

$dataAlumni = [];

try {
    $stmtAlumni = $koneksi->query("
        SELECT *
        FROM alumni_jepang
    ");

    $dataAlumni = $stmtAlumni->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $dataAlumni = [];
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

?>

<style>
.program-page {
    padding: 55px 0 80px;
}

.program-container {
    width: min(1070px, calc(100% - 40px));
    margin: 0 auto;
}

.program-hero {
    background: var(--papan);
    color: var(--putih);
    border-radius: 24px;
    padding: 55px 40px;
    margin-bottom: 45px;
    position: relative;
    overflow: hidden;
}

.program-hero::after {
    content: '';
    position: absolute;
    width: 260px;
    height: 260px;
    border: 1px solid rgba(240, 200, 120, 0.25);
    border-radius: 50%;
    right: -80px;
    top: -80px;
    pointer-events: none;
}

.program-label {
    display: inline-block;
    color: var(--kapur-terang);
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.program-hero h1 {
    color: var(--putih);
    font-size: clamp(2rem, 4vw, 3.2rem);
    max-width: 760px;
    margin-bottom: 18px;
}

.program-hero p {
    max-width: 760px;
    color: #DCE3E9;
    font-size: 1rem;
    line-height: 1.8;
    margin: 0;
}

.program-section {
    margin-bottom: 50px;
}

.program-section-head {
    margin-bottom: 25px;
}

.program-section-head span {
    display: block;
    color: var(--kapur);
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 7px;
}

.program-section-head h2 {
    font-size: 2rem;
    margin-bottom: 8px;
}

.program-section-head p {
    color: var(--teks-muda);
    margin: 0;
    line-height: 1.7;
}

.program-card {
    background: var(--putih);
    border: 1px solid var(--garis);
    border-radius: 14px;
    padding: 28px;
    margin-bottom: 18px;
    display: grid;
    grid-template-columns: 70px minmax(0, 1fr);
    gap: 22px;
    box-shadow: var(--bayangan);
}

.program-icon {
    width: 62px;
    height: 62px;
    border-radius: 12px;
    background: var(--kertas-gelap);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    overflow: hidden;
}

.program-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.program-card h3 {
    font-size: 1.45rem;
    margin-bottom: 4px;
    overflow-wrap: anywhere;
}

.program-singkatan {
    display: inline-block;
    color: var(--kapur);
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.program-card p {
    color: var(--teks-muda);
    margin: 0;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.jepang-box {
    background: var(--papan);
    border-radius: 20px;
    padding: 38px;
    color: var(--putih);
    margin-top: 25px;
}

.jepang-box h2 {
    color: var(--putih);
    font-size: 2rem;
    margin-bottom: 12px;
}

.jepang-box > p {
    color: #DCE3E9;
    max-width: 800px;
    line-height: 1.8;
    margin-bottom: 28px;
}

.jepang-info {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
}

.jepang-info-item {
    background: var(--papan-terang);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 20px;
}

.jepang-info-item strong {
    display: block;
    color: var(--kapur-terang);
    margin-bottom: 5px;
}

.jepang-info-item span {
    color: #DCE3E9;
    font-size: 0.9rem;
    line-height: 1.6;
}

.alumni-section {
    margin-top: 40px;
}

.alumni-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
}

.alumni-card {
    background: var(--putih);
    border: 1px solid var(--garis);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--bayangan);
    min-width: 0;
}

.alumni-foto {
    width: 100%;
    height: 230px;
    background: var(--kertas-gelap);
    overflow: hidden;
}

.alumni-foto img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.alumni-tanpa-foto {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}

.alumni-info {
    padding: 20px;
    overflow-wrap: anywhere;
}

.alumni-info h3 {
    font-size: 1.25rem;
    margin-bottom: 5px;
}

.alumni-info p {
    color: var(--teks-muda);
    font-size: 0.88rem;
    margin: 5px 0;
}

.alumni-tag {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 10px;
    border-radius: 999px;
    background: var(--kertas-gelap);
    color: var(--papan);
    font-size: 0.75rem;
    font-weight: 700;
}

.data-kosong {
    background: var(--putih);
    border: 1px solid var(--garis);
    border-radius: 14px;
    padding: 35px;
    text-align: center;
}

.data-kosong-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.data-kosong h3 {
    font-size: 1.35rem;
    margin-bottom: 7px;
}

.data-kosong p {
    color: var(--teks-muda);
    margin: 0;
    line-height: 1.7;
}

@media (max-width: 800px) {
    .program-hero {
        padding: 38px 25px;
    }

    .program-card {
        grid-template-columns: 55px minmax(0, 1fr);
        padding: 22px;
    }

    .program-icon {
        width: 52px;
        height: 52px;
        font-size: 1.4rem;
    }

    .jepang-box {
        padding: 28px 22px;
    }

    .jepang-info {
        grid-template-columns: 1fr;
    }

    .alumni-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 560px) {
    .program-container {
        width: calc(100% - 25px);
    }

    .program-hero {
        border-radius: 16px;
        padding: 30px 20px;
    }

    .program-hero h1 {
        font-size: 2rem;
    }

    .program-card {
        grid-template-columns: 1fr;
    }

    .alumni-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="program-page">

    <div class="program-container">

        <!-- HERO -->

        <section class="program-hero">

            <span class="program-label">
                Program Sekolah
            </span>

            <h1>
                Program dan Peluang Karier
            </h1>

            <p>
                Berbagai program SMK Infokom Kota Bogor yang mendukung
                pengalaman belajar, kesiapan memasuki dunia kerja,
                dan pengembangan keterampilan siswa.
            </p>

        </section>

        <!-- DAFTAR PROGRAM DARI ADMIN -->

        <section class="program-section">

            <div class="program-section-head">

                <span>Program Sekolah</span>

                <h2>Program Unggulan</h2>

                <p>
                    Informasi PKL, bursa kerja, magang Jepang,
                    dan program lainnya yang dikelola oleh sekolah.
                </p>

            </div>

            <?php if (!empty($dataProgram)): ?>

                <?php foreach ($dataProgram as $item): ?>

                    <article class="program-card">

                        <div class="program-icon">

                            <?php if (!empty($item['foto'])): ?>

                                <img
                                    src="<?= BASE_URL ?>/uploads/program/<?= rawurlencode($item['foto']) ?>"
                                    alt="<?= htmlspecialchars($item['judul']) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <?= htmlspecialchars(
                                    !empty($item['ikon']) ? $item['ikon'] : '📌'
                                ) ?>

                            <?php endif; ?>

                        </div>

                        <div>

                            <h3>
                                <?= htmlspecialchars($item['judul']) ?>
                            </h3>

                            <?php if (!empty($item['singkatan'])): ?>

                                <span class="program-singkatan">
                                    <?= htmlspecialchars($item['singkatan']) ?>
                                </span>

                            <?php endif; ?>

                            <?php if (!empty($item['deskripsi'])): ?>

                                <p>
                                    <?= nl2br(htmlspecialchars($item['deskripsi'])) ?>
                                </p>

                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="data-kosong">

                    <div class="data-kosong-icon">📋</div>

                    <h3>Program belum tersedia</h3>

                    <p>
                        Belum ada program aktif. Tambahkan program
                        melalui halaman admin agar muncul di sini.
                    </p>

                </div>

            <?php endif; ?>

        </section>

        <!-- INFORMASI MAGANG JEPANG TETAP DIPERTAHANKAN -->

        <section class="program-section">

            <div class="jepang-box">

                <h2>Tentang Program Magang ke Jepang</h2>

                <p>
                    Program ini menjadi salah satu kesempatan bagi siswa
                    dan alumni untuk mendapatkan pengalaman internasional.
                    Informasi peserta yang telah mengikuti program akan
                    ditampilkan pada bagian alumni di bawah.
                </p>

                <div class="jepang-info">

                    <div class="jepang-info-item">

                        <strong>🌏 Pengalaman Internasional</strong>

                        <span>
                            Mengenal lingkungan kerja dan budaya Jepang.
                        </span>

                    </div>

                    <div class="jepang-info-item">

                        <strong>💼 Pengalaman Kerja</strong>

                        <span>
                            Mengembangkan keterampilan melalui pengalaman
                            langsung di dunia kerja.
                        </span>

                    </div>

                    <div class="jepang-info-item">

                        <strong>🎓 Pengembangan Diri</strong>

                        <span>
                            Meningkatkan kemampuan, kedisiplinan,
                            dan kemandirian peserta.
                        </span>

                    </div>

                </div>

            </div>

        </section>

        <!-- ALUMNI JEPANG -->

        <section class="alumni-section">

            <div class="program-section-head">

                <span>Peserta Program</span>

                <h2>Alumni yang Berangkat ke Jepang</h2>

                <p>
                    Dokumentasi dan informasi alumni yang telah mengikuti
                    program magang atau bekerja di Jepang.
                </p>

            </div>

            <?php if (!empty($dataAlumni)): ?>

                <div class="alumni-grid">

                    <?php foreach ($dataAlumni as $alumni): ?>

                        <?php
                        $nama = $alumni['nama']
                            ?? $alumni['nama_alumni']
                            ?? $alumni['nama_lengkap']
                            ?? 'Alumni SMK Infokom Bogor';

                        $foto = $alumni['foto'] ?? '';
                        $tahunBerangkat = $alumni['tahun_berangkat'] ?? '';
                        $tahunLulus = $alumni['tahun_lulus'] ?? '';
                        $jurusan = $alumni['jurusan'] ?? '';

                        $perusahaan = $alumni['perusahaan']
                            ?? $alumni['tempat_kerja']
                            ?? '';
                        ?>

                        <article class="alumni-card">

                            <div class="alumni-foto">

                                <?php if ($foto !== ''): ?>

                                    <img
                                        src="<?= BASE_URL ?>/uploads/alumni/<?= rawurlencode($foto) ?>"
                                        alt="<?= htmlspecialchars($nama) ?>"
                                        loading="lazy"
                                    >

                                <?php else: ?>

                                    <div class="alumni-tanpa-foto">
                                        🎓
                                    </div>

                                <?php endif; ?>

                            </div>

                            <div class="alumni-info">

                                <h3>
                                    <?= htmlspecialchars($nama) ?>
                                </h3>

                                <?php if ($jurusan !== ''): ?>

                                    <p>
                                        <strong>Jurusan:</strong>
                                        <?= htmlspecialchars($jurusan) ?>
                                    </p>

                                <?php endif; ?>

                                <?php if ($tahunLulus !== ''): ?>

                                    <p>
                                        <strong>Lulus:</strong>
                                        <?= htmlspecialchars($tahunLulus) ?>
                                    </p>

                                <?php endif; ?>

                                <span class="alumni-tag">

                                    🇯🇵

                                    <?php if ($tahunBerangkat !== ''): ?>

                                        Berangkat <?= htmlspecialchars($tahunBerangkat) ?>

                                    <?php else: ?>

                                        Program Jepang

                                    <?php endif; ?>

                                </span>

                                <?php if ($perusahaan !== ''): ?>

                                    <p style="margin-top:12px;">

                                        <strong>Tempat:</strong>

                                        <?= htmlspecialchars($perusahaan) ?>

                                    </p>

                                <?php endif; ?>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="data-kosong">

                    <div class="data-kosong-icon">🇯🇵</div>

                    <h3>Data alumni belum tersedia</h3>

                    <p>
                        Informasi alumni yang mengikuti program
                        magang ke Jepang akan ditampilkan di sini.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>