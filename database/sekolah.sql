-- =========================================================
-- DATABASE WEBSITE PENGENALAN SEKOLAH
-- Import file ini melalui phpMyAdmin atau perintah:
--   mysql -u root -p < sekolah.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_sekolah;

-- ---------------------------------------------------------
-- Tabel admin (untuk login ke panel admin)
-- ---------------------------------------------------------
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Akun default -> username: admin | password: admin123
-- (hash dibuat dengan password_hash('admin123', PASSWORD_DEFAULT))
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2b$10$uLgFuM2fqnoA52oX0XLOrO7wiBc0xvc7Pfn51KU/GIQ0Vi6MGsroC', 'Administrator');

-- ---------------------------------------------------------
-- Tabel profil sekolah (data tunggal, selalu id = 1)
-- ---------------------------------------------------------
CREATE TABLE profil_sekolah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sekolah VARCHAR(150) NOT NULL,
    npsn VARCHAR(50) DEFAULT NULL,
    alamat TEXT,
    telepon VARCHAR(50),
    email VARCHAR(100),
    jam_operasional VARCHAR(150),
    sejarah TEXT,
    visi TEXT,
    misi TEXT,
    logo VARCHAR(255) DEFAULT NULL,
    foto_gedung VARCHAR(255) DEFAULT NULL,
    peta_embed TEXT
);

INSERT INTO profil_sekolah
(nama_sekolah, npsn, alamat, telepon, email, jam_operasional, sejarah, visi, misi)
VALUES
(
  'SMA Negeri 1 Harapan Bangsa',
  '20123456',
  'Jl. Pendidikan No. 45, Bogor, Jawa Barat',
  '(0251) 123-4567',
  'info@sman1harapanbangsa.sch.id',
  'Senin - Jumat, 07.00 - 15.30 WIB',
  'Sekolah ini berdiri sejak tahun 1985 dan telah meluluskan ribuan siswa yang tersebar di berbagai bidang. Bermula dari beberapa ruang kelas sederhana, kini sekolah berkembang dengan fasilitas yang lebih lengkap untuk mendukung proses belajar mengajar.',
  'Menjadi lembaga pendidikan yang unggul dalam prestasi, berkarakter, dan berwawasan lingkungan.',
  '1. Menyelenggarakan pembelajaran yang aktif, kreatif, dan menyenangkan.\n2. Membina akhlak mulia dan disiplin siswa.\n3. Mengembangkan potensi siswa melalui kegiatan ekstrakurikuler.\n4. Menjalin kerja sama dengan orang tua dan masyarakat.'
);

-- ---------------------------------------------------------
-- Tabel guru & staf
-- ---------------------------------------------------------
CREATE TABLE guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nip VARCHAR(50) DEFAULT NULL,
    jabatan VARCHAR(100) NOT NULL,
    mata_pelajaran VARCHAR(100) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO guru (nama, nip, jabatan, mata_pelajaran, foto) VALUES
('Dra. Sri Wahyuni, M.Pd.', '196705121990032001', 'Kepala Sekolah', NULL, NULL),
('Budi Santoso, S.Pd.', '197803242005011002', 'Wakil Kepala Sekolah', 'Matematika', NULL),
('Rina Marlina, S.Pd.', '198207152009022003', 'Guru', 'Bahasa Indonesia', NULL);

-- ---------------------------------------------------------
-- Tabel galeri (foto kegiatan sekolah)
-- ---------------------------------------------------------
CREATE TABLE galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255) NOT NULL,
    tanggal DATE DEFAULT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Tabel berita (berita / pengumuman sekolah)
-- ---------------------------------------------------------
CREATE TABLE berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    ringkasan VARCHAR(255) DEFAULT NULL,
    isi TEXT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    penulis VARCHAR(100) DEFAULT 'Admin',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO berita (judul, slug, ringkasan, isi, penulis) VALUES
('Penerimaan Peserta Didik Baru Tahun Ajaran 2026/2027',
 'ppdb-2026-2027',
 'Pendaftaran siswa baru dibuka mulai bulan depan, simak jadwal dan syaratnya.',
 'Sekolah membuka pendaftaran Peserta Didik Baru (PPDB) untuk tahun ajaran 2026/2027. Calon siswa dapat mendaftar secara daring melalui website ini dengan menyiapkan dokumen yang diperlukan seperti kartu keluarga, ijazah, dan pas foto. Informasi lebih lanjut dapat menghubungi bagian tata usaha.',
 'Admin');

-- ---------------------------------------------------------
-- Tabel pesan (pesan masuk dari formulir kontak)
-- ---------------------------------------------------------
CREATE TABLE pesan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subjek VARCHAR(150) DEFAULT NULL,
    isi_pesan TEXT NOT NULL,
    sudah_dibaca TINYINT(1) DEFAULT 0,
    dikirim_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
