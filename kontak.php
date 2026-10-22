<?php

require_once __DIR__ . '/config/koneksi.php';

$statusPesan = '';
$statusTipe  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $isi    = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $isi === '') {

        $statusPesan = 'Mohon lengkapi nama, email, dan isi pesan.';
        $statusTipe  = 'gagal';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $statusPesan = 'Format email tidak valid.';
        $statusTipe  = 'gagal';

    } else {

        $stmt = $koneksi->prepare("
            INSERT INTO pesan
                (nama, email, subjek, isi_pesan)
            VALUES
                (?, ?, ?, ?)
        ");

        $stmt->execute([
            $nama,
            $email,
            $subjek,
            $isi
        ]);

        $statusPesan = 'Terima kasih, pesan Anda sudah kami terima dan akan segera ditindaklanjuti.';
        $statusTipe  = 'berhasil';

        $nama = '';
        $email = '';
        $subjek = '';
        $isi = '';
    }
}

$judul_halaman = 'Kontak';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

?>

<section class="header-halaman">
    <div class="container">

        <span class="kop">HUBUNGI SEKOLAH</span>

        <h1>Hubungi Kami</h1>

        <p>
            Ada pertanyaan mengenai pendaftaran, kegiatan,
            atau informasi sekolah? Silakan kirim pesan kepada kami.
        </p>

    </div>
</section>

<section class="bagian">
    <div class="container">

        <div class="judul-bagian">
            <span class="kop">KOMUNIKASI</span>

            <h2>Terhubung dengan Sekolah</h2>

            <p>
                Gunakan formulir di bawah untuk menyampaikan
                pertanyaan atau informasi kepada pihak sekolah.
            </p>
        </div>

        <div class="grid-2">

            <!-- FORM KONTAK -->
            <div>

                <?php if ($statusPesan): ?>

                    <div class="pesan-status pesan-<?= htmlspecialchars($statusTipe) ?>">
                        <?= htmlspecialchars($statusPesan) ?>
                    </div>

                <?php endif; ?>

                <form
                    class="formulir"
                    method="post"
                    action="<?= BASE_URL ?>/kontak.php"
                >

                    <div class="grup-form">
                        <label for="nama">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= htmlspecialchars($nama ?? '') ?>"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                    </div>

                    <div class="grup-form">
                        <label for="email">
                            Alamat Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($email ?? '') ?>"
                            placeholder="nama@email.com"
                            required
                        >
                    </div>

                    <div class="grup-form">
                        <label for="subjek">
                            Subjek
                        </label>

                        <input
                            type="text"
                            id="subjek"
                            name="subjek"
                            value="<?= htmlspecialchars($subjek ?? '') ?>"
                            placeholder="Contoh: Pertanyaan PPDB"
                        >
                    </div>

                    <div class="grup-form">
                        <label for="pesan">
                            Pesan
                        </label>

                        <textarea
                            id="pesan"
                            name="pesan"
                            placeholder="Tuliskan pesan Anda..."
                            required
                        ><?= htmlspecialchars($isi ?? '') ?></textarea>
                    </div>

                    <button
                        type="submit"
                        class="tombol tombol-utama"
                    >
                        Kirim Pesan →
                    </button>

                </form>

            </div>

            <!-- INFORMASI KONTAK -->
            <div>

                    <p>
                        Silakan gunakan informasi berikut
                        untuk menghubungi sekolah secara langsung.
                    </p>

                    <ul style="list-style:none;padding:0;margin:24px 0 0;">

                        <li style="margin-bottom:20px;">
                            <strong>Alamat</strong><br>
                            <?= nl2br(htmlspecialchars($profil['alamat'] ?? '-')) ?>
                        </li>

                        <li style="margin-bottom:20px;">
                            <strong>Telepon</strong><br>
                            <?= htmlspecialchars($profil['telepon'] ?? '-') ?>
                        </li>

                        <li style="margin-bottom:20px;">
                            <strong>Email</strong><br>
                            <?= htmlspecialchars($profil['email'] ?? '-') ?>
                        </li>

                        <li>
                            <strong>Jam Operasional</strong><br>
                            <?= htmlspecialchars($profil['jam_operasional'] ?? '-') ?>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>
</section>

<section class="bagian bagian-kertas-gelap">
    <div class="container">

        <div class="judul-bagian">

            <span class="kop">INFORMASI PPDB</span>

            <h2>Ingin Bergabung dengan Sekolah?</h2>

            <p>
                Lihat informasi penerimaan peserta didik baru
                dan persiapkan proses pendaftaran Anda.
            </p>

            <a
                href="<?= BASE_URL ?>/ppdb.php"
                class="tombol tombol-utama"
            >
                Lihat Informasi PPDB →
            </a>

        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>