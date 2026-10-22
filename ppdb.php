    <?php

    require_once __DIR__ . '/config/koneksi.php';

    $judul_halaman = 'PPDB';

    $jurusan = [
        [
            'kode' => 'DKV',
            'nama' => 'Desain Komunikasi Visual',
            'deskripsi' => 'Desain grafis, ilustrasi, fotografi, videografi, dan komunikasi visual.'
        ],
        [
            'kode' => 'PSPT',
            'nama' => 'Produksi dan Siaran Program Tele',
            'deskripsi' => 'Produksi video, penyiaran, kamera, tata suara, dan editing.'
        ],
        [
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Pemrograman, website, aplikasi, database, dan pengembangan sistem.'
        ],
        [
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan',
            'deskripsi' => 'Komputer, jaringan, instalasi, konfigurasi, dan troubleshooting.'
        ]
    ];

    $alur = [
        [
            'nomor' => '01',
            'judul' => 'Cari Informasi',
            'deskripsi' => 'Pelajari informasi sekolah, pilihan jurusan, persyaratan, dan jadwal PPDB.'
        ],
        [
            'nomor' => '02',
            'judul' => 'Siapkan Persyaratan',
            'deskripsi' => 'Siapkan dokumen dan persyaratan yang dibutuhkan untuk proses pendaftaran.'
        ],
        [
            'nomor' => '03',
            'judul' => 'Melakukan Pendaftaran',
            'deskripsi' => 'Isi formulir pendaftaran dengan data diri yang benar dan lengkap.'
        ],
        [
            'nomor' => '04',
            'judul' => 'Verifikasi',
            'deskripsi' => 'Data dan dokumen pendaftaran akan diperiksa oleh pihak sekolah.'
        ],
        [
            'nomor' => '05',
            'judul' => 'Pengumuman',
            'deskripsi' => 'Pantau informasi hasil seleksi dan tahapan berikutnya dari sekolah.'
        ],
        [
            'nomor' => '06',
            'judul' => 'Daftar Ulang',
            'deskripsi' => 'Peserta yang diterima mengikuti proses daftar ulang sesuai jadwal.'
        ]
    ];

    $persyaratan = [
        'Fotokopi ijazah atau dokumen kelulusan',
        'Fotokopi kartu keluarga',
        'Fotokopi akta kelahiran',
        'Fotokopi KTP orang tua atau wali',
        'Pas foto terbaru',
        'Dokumen lain sesuai ketentuan sekolah'
    ];

    ?>

    <?php require_once __DIR__ . '/includes/header.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <!-- HEADER PPDB -->
    <section class="header-halaman halaman-ppdb">
        <div class="container">

            <span class="kop">
                PENERIMAAN PESERTA DIDIK BARU
            </span>

            <h1>
                Bergabung Bersama <?= htmlspecialchars($namaSekolah) ?>
            </h1>

            <p>
                Temukan informasi mengenai penerimaan peserta didik baru,
                pilihan program keahlian, persyaratan, dan tahapan pendaftaran.
            </p>

            <div class="hero-ppdb-aksi">

                <a href="#alur" class="tombol tombol-utama">
                    Lihat Alur Pendaftaran
                </a>

                <a
                    href="<?= BASE_URL ?>/formulir-ppdb.php"
                    class="tombol tombol-garis"
                >
                    Daftar Sekarang →
                </a>

            </div>
        </div>
    </section>

    <!-- PEMBUKA -->
    <section class="bagian ppdb-pembuka">
        <div class="container">

            <div class="ppdb-pembuka-grid">

                <div>
                    <span class="kop">INFORMASI PPDB</span>

                    <h2>
                        Awali langkahmu menuju masa depan
                    </h2>

                    <p>
                        Penerimaan peserta didik baru merupakan kesempatan
                        bagi calon siswa untuk menjadi bagian dari lingkungan
                        pendidikan <?= htmlspecialchars($namaSekolah) ?>.
                    </p>

                    <p>
                        Kenali program keahlian yang tersedia dan persiapkan
                        dokumen yang diperlukan sebelum melakukan pendaftaran.
                    </p>

                </div>

                <div class="ppdb-info-card">

                    <div class="info-icon">📝</div>

                    <h3>Pendaftaran Peserta Didik Baru</h3>

                    <p>
                        Isi formulir pendaftaran dengan data yang benar.
                        Pastikan seluruh informasi telah diperiksa sebelum
                        formulir dikirim.
                    </p>

                    <div class="info-card-line"></div>

                    <strong>
                        Pendaftaran dilakukan melalui formulir PPDB online.
                    </strong>

                </div>

            </div>
        </div>
    </section>

    <!-- JURUSAN -->
    <section class="bagian ppdb-jurusan">
        <div class="container">

            <div class="judul-bagian">
                <span class="kop">PILIHAN PROGRAM KEAHLIAN</span>

                <h2>Pilih jurusan sesuai minatmu</h2>

                <p>
                    Kenali pilihan program keahlian yang tersedia di
                    <?= htmlspecialchars($namaSekolah) ?>.
                </p>
            </div>

            <div class="ppdb-jurusan-grid">

                <?php foreach ($jurusan as $index => $j): ?>

                    <article class="ppdb-jurusan-card">

                        <div class="ppdb-jurusan-number">
                            <?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>
                        </div>

                        <span class="ppdb-kode">
                            <?= htmlspecialchars($j['kode']) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($j['nama']) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($j['deskripsi']) ?>
                        </p>

                    </article>

                <?php endforeach; ?>

            </div>

            <div class="lihat-jurusan">
                <a
                    href="<?= BASE_URL ?>/jurusan.php"
                    class="tombol tombol-garis-biru"
                >
                    Lihat Detail Semua Jurusan →
                </a>
            </div>

        </div>
    </section>

    <!-- ALUR PPDB -->
    <section class="bagian ppdb-alur" id="alur">
        <div class="container">

            <div class="judul-bagian">
                <span class="kop">TAHAPAN PENDAFTARAN</span>

                <h2>Alur PPDB</h2>

                <p>
                    Ikuti tahapan pendaftaran berikut agar proses berjalan
                    dengan tertib dan lancar.
                </p>
            </div>

            <div class="alur-grid">

                <?php foreach ($alur as $item): ?>

                    <article class="alur-card">

                        <div class="alur-number">
                            <?= htmlspecialchars($item['nomor']) ?>
                        </div>

                        <div>
                            <h3>
                                <?= htmlspecialchars($item['judul']) ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($item['deskripsi']) ?>
                            </p>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </div>
    </section>

    <!-- PERSYARATAN -->
    <section class="bagian ppdb-persyaratan">
        <div class="container">

            <div class="persyaratan-box">

                <div class="persyaratan-heading">
                    <span class="kop">PERSYARATAN</span>

                    <h2>Siapkan dokumen pendaftaran</h2>

                    <p>
                        Persiapkan dokumen berikut sebagai gambaran umum
                        kebutuhan administrasi pendaftaran.
                    </p>

                    <small>
                        * Persyaratan dapat menyesuaikan ketentuan PPDB
                        yang berlaku di sekolah.
                    </small>
                </div>

                <div class="persyaratan-list">

                    <?php foreach ($persyaratan as $index => $item): ?>

                        <div class="persyaratan-item">
                            <span><?= $index + 1 ?></span>

                            <p><?= htmlspecialchars($item) ?></p>
                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>
    </section>

    <!-- CATATAN PPDB -->
    <section class="bagian ppdb-catatan">
        <div class="container">

            <div class="catatan-grid">

                <div class="catatan-card">
                    <div class="catatan-icon">📅</div>

                    <h3>Jadwal Pendaftaran</h3>

                    <p>
                        Informasi jadwal dan tahapan PPDB dapat diperoleh
                        melalui pihak sekolah.
                    </p>
                </div>

                <div class="catatan-card">
                    <div class="catatan-icon">📍</div>

                    <h3>Informasi Sekolah</h3>

                    <p>
                        Kunjungi halaman kontak untuk mendapatkan informasi
                        alamat dan kontak <?= htmlspecialchars($namaSekolah) ?>.
                    </p>
                </div>

                <div class="catatan-card">
                    <div class="catatan-icon">💬</div>

                    <h3>Butuh Bantuan?</h3>

                    <p>
                        Hubungi pihak sekolah apabila membutuhkan
                        informasi lebih lanjut mengenai pendaftaran.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- CTA PENDAFTARAN -->
    <section class="bagian ppdb-cta">
        <div class="container">

            <div class="ppdb-cta-inner">

                <div>
                    <span class="kop">SIAP BERGABUNG?</span>

                    <h2>
                        Jadilah bagian dari <?= htmlspecialchars($namaSekolah) ?>
                    </h2>

                    <p>
                        Mulai proses pendaftaran dengan mengisi formulir
                        PPDB online dan lengkapi data yang dibutuhkan.
                    </p>
                </div>

                <div class="ppdb-cta-actions">

                    <a
                        href="<?= BASE_URL ?>/formulir-ppdb.php"
                        class="tombol tombol-utama"
                    >
                        Daftar Sekarang →
                    </a>

                    <a
                        href="<?= BASE_URL ?>/jurusan.php"
                        class="tombol tombol-garis"
                    >
                        Lihat Jurusan
                    </a>

                    <a
                        href="<?= BASE_URL ?>/kontak.php"
                        class="tombol tombol-garis"
                    >
                        Hubungi Sekolah
                    </a>

                </div>

            </div>

        </div>
    </section>

    <style>

    /* HEADER PPDB */
    .halaman-ppdb {
        padding: 90px 0;
        background: linear-gradient(
            135deg,
            rgba(28, 43, 57, 0.98),
            rgba(28, 43, 57, 0.92)
        );
        color: var(--kertas);
    }

    .halaman-ppdb .kop {
        color: var(--aksen);
    }

    .halaman-ppdb h1 {
        max-width: 900px;
        margin: 12px 0 18px;
        color: var(--kertas);
        font-size: 46px;
        line-height: 1.15;
    }

    .halaman-ppdb p {
        max-width: 780px;
        color: rgba(246, 242, 233, 0.78);
        font-size: 18px;
        line-height: 1.8;
    }

    .hero-ppdb-aksi {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    /* PEMBUKA */
    .ppdb-pembuka-grid {
        display: grid;
        grid-template-columns: 1.4fr 0.8fr;
        gap: 60px;
        align-items: center;
    }

    .ppdb-pembuka-grid h2 {
        margin: 10px 0 18px;
        color: var(--papan);
        font-family: var(--font-judul);
        font-size: 34px;
        line-height: 1.3;
    }

    .ppdb-pembuka-grid p {
        color: var(--teks-muda);
        line-height: 1.8;
    }

    .ppdb-info-card {
        padding: 35px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid rgba(28, 43, 57, 0.10);
        box-shadow: 0 15px 40px rgba(28, 43, 57, 0.08);
    }

    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 58px;
        margin-bottom: 20px;
        border-radius: 16px;
        background: var(--kertas-2);
        font-size: 28px;
    }

    .ppdb-info-card h3 {
        margin: 0 0 12px;
        color: var(--papan);
        font-family: var(--font-judul);
        font-size: 21px;
    }

    .ppdb-info-card p {
        color: var(--teks-muda);
        line-height: 1.7;
    }

    .info-card-line {
        height: 1px;
        margin: 22px 0;
        background: rgba(28, 43, 57, 0.10);
    }

    .ppdb-info-card strong {
        color: var(--papan);
        font-size: 14px;
        line-height: 1.6;
    }

    /* JURUSAN */
    .ppdb-jurusan {
        background: var(--kertas-2);
    }

    .ppdb-jurusan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .ppdb-jurusan-card {
        padding: 25px;
        background: #fff;
        border: 1px solid rgba(28, 43, 57, 0.10);
        border-radius: 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ppdb-jurusan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(28, 43, 57, 0.08);
    }

    .ppdb-jurusan-number {
        margin-bottom: 18px;
        color: var(--aksen);
        font-family: var(--font-judul);
        font-size: 23px;
        font-weight: 800;
    }

    .ppdb-kode {
        display: inline-block;
        padding: 6px 11px;
        margin-bottom: 13px;
        border-radius: 999px;
        background: rgba(227, 169, 59, 0.14);
        color: var(--papan);
        font-size: 12px;
        font-weight: 800;
    }

    .ppdb-jurusan-card h3 {
        margin: 0 0 12px;
        color: var(--papan);
        font-family: var(--font-judul);
        font-size: 18px;
        line-height: 1.4;
    }

    .ppdb-jurusan-card p {
        margin: 0;
        color: var(--teks-muda);
        font-size: 14px;
        line-height: 1.7;
    }

    .lihat-jurusan {
        margin-top: 30px;
        text-align: center;
    }

    /* ALUR */
    .ppdb-alur {
        background: var(--kertas);
    }

    .alur-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .alur-card {
        display: flex;
        gap: 18px;
        padding: 25px;
        background: #fff;
        border: 1px solid rgba(28, 43, 57, 0.10);
        border-radius: 18px;
    }

    .alur-number {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(227, 169, 59, 0.14);
        color: var(--papan);
        font-size: 15px;
        font-weight: 800;
    }

    .alur-card h3 {
        margin: 2px 0 8px;
        color: var(--papan);
        font-family: var(--font-judul);
        font-size: 18px;
    }

    .alur-card p {
        margin: 0;
        color: var(--teks-muda);
        font-size: 14px;
        line-height: 1.7;
    }

    /* PERSYARATAN */
    .ppdb-persyaratan {
        padding-top: 20px;
    }

    .persyaratan-box {
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        gap: 60px;
        padding: 45px;
        border-radius: 22px;
        background: var(--kertas-2);
        border: 1px solid rgba(28, 43, 57, 0.08);
    }

    .persyaratan-heading h2 {
        margin: 10px 0 15px;
        color: var(--papan);
        font-family: var(--font-judul);
        font-size: 30px;
        line-height: 1.3;
    }

    .persyaratan-heading p {
        color: var(--teks-muda);
        line-height: 1.8;
    }

    .persyaratan-heading small {
        display: block;
        margin-top: 18px;
        color: var(--teks-muda);
        line-height: 1.6;
    }

    .persyaratan-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .persyaratan-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 18px;
        border-radius: 12px;
        background: #fff;
    }

    .persyaratan-item span {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(227, 169, 59, 0.16);
        color: var(--papan);
        font-size: 13px;
        font-weight: 800;
    }

    .persyaratan-item p {
        margin: 0;
        color: var(--teks-muda);
        font-size: 14px;
        line-height: 1.5;
    }

    /* CATATAN */
    .catatan-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .catatan-card {
        padding: 28px;
        border: 1px solid rgba(28, 43, 57, 0.10);
        border-radius: 18px;
        background: #fff;
    }

    .catatan-icon {
        margin-bottom: 15px;
        font-size: 28px;
    }

    .catatan-card h3 {
        margin: 0 0 10px;
        color: var(--papan);
        font-family: var(--font-judul);
        font-size: 19px;
    }

    .catatan-card p {
        margin: 0;
        color: var(--teks-muda);
        font-size: 14px;
        line-height: 1.7;
    }

    /* CTA */
    .ppdb-cta {
        padding-top: 20px;
    }

    .ppdb-cta-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
        padding: 45px;
        border-radius: 22px;
        background: var(--papan);
        color: var(--kertas);
    }

    .ppdb-cta-inner .kop {
        color: var(--aksen);
    }

    .ppdb-cta-inner h2 {
        max-width: 700px;
        margin: 10px 0;
        color: var(--kertas);
        font-family: var(--font-judul);
        font-size: 30px;
        line-height: 1.3;
    }

    .ppdb-cta-inner p {
        margin: 0;
        color: rgba(246, 242, 233, 0.78);
        line-height: 1.8;
    }

    .ppdb-cta-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* TOMBOL OUTLINE */
    .tombol-garis-biru {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        border: 1px solid var(--papan);
        border-radius: 10px;
        color: var(--papan);
        text-decoration: none;
        font-weight: 700;
        transition: 0.2s ease;
    }

    .tombol-garis-biru:hover {
        background: var(--papan);
        color: var(--kertas);
    }

    /* RESPONSIVE */
    @media (max-width: 1000px) {
        .ppdb-jurusan-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .alur-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 800px) {
        .halaman-ppdb {
            padding: 65px 0;
        }

        .halaman-ppdb h1 {
            font-size: 35px;
        }

        .ppdb-pembuka-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .persyaratan-box {
            grid-template-columns: 1fr;
            gap: 30px;
            padding: 30px;
        }

        .catatan-grid {
            grid-template-columns: 1fr;
        }

        .ppdb-cta-inner {
            flex-direction: column;
            align-items: flex-start;
            padding: 30px;
        }
    }

    @media (max-width: 600px) {
        .ppdb-jurusan-grid,
        .alur-grid {
            grid-template-columns: 1fr;
        }

        .ppdb-cta-inner h2 {
            font-size: 26px;
        }
    }

    </style>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>