# Website Pengenalan Sekolah

Website profil sekolah lengkap dengan panel admin, upload foto, dan koneksi database MySQL.
Dibangun dengan **PHP native (PDO)** + **MySQL**, tanpa framework, agar mudah dijalankan di
XAMPP/Laragon maupun hosting biasa.

## Fitur

**Halaman publik**
- Beranda (profil singkat, statistik, berita & galeri terbaru)
- Profil Sekolah (sejarah, visi, misi, data sekolah)
- Guru & Staf (dengan foto)
- Galeri Kegiatan (dengan foto)
- Berita & Pengumuman (daftar + halaman detail)
- Kontak (formulir yang tersimpan ke database)

**Panel Admin** (`/admin`)
- Login aman (password di-hash dengan `password_hash`)
- Dashboard ringkasan
- Kelola Profil Sekolah (termasuk upload logo)
- Kelola Guru & Staf (tambah, edit, hapus, upload foto)
- Kelola Galeri Foto (tambah, hapus, upload foto)
- Kelola Berita (tulis, edit, hapus, upload foto sampul)
- Lihat pesan yang dikirim pengunjung lewat formulir kontak

Semua foto yang diunggah disimpan di folder `uploads/` dan otomatis divalidasi
(hanya JPG/PNG/WEBP, maksimal 3MB) agar aman.

## Cara Instalasi (XAMPP / Laragon)

1. **Salin folder proyek**
   Ekstrak file zip ini, lalu salin seluruh folder `sekolah-website` ke:
   - XAMPP: `C:\xampp\htdocs\sekolah-website`
   - Laragon: `C:\laragon\www\sekolah-website`

2. **Jalankan Apache & MySQL**
   Buka XAMPP Control Panel / Laragon, nyalakan modul **Apache** dan **MySQL**.

3. **Buat database**
   - Buka `http://localhost/phpmyadmin`
   - Klik menu **Import**, pilih file `database/sekolah.sql` dari folder proyek, lalu klik **Go**.
   - Database bernama `db_sekolah` beserta semua tabel dan data contoh akan otomatis terbuat.

4. **Atur koneksi database (jika perlu)**
   Buka file `config/koneksi.php`. Secara default sudah cocok untuk XAMPP/Laragon:
   ```php
   $db_host = 'localhost';
   $db_name = 'db_sekolah';
   $db_user = 'root';
   $db_pass = '';
   ```
   Ubah `$db_user` dan `$db_pass` jika MySQL Anda memakai akun/password berbeda.

5. **Buka website**
   - Website publik: `http://localhost/sekolah-website/index.php`
   - Panel admin: `http://localhost/sekolah-website/admin/login.php`

6. **Login admin default**
   - Username: `admin`
   - Password: `admin123`

   **Segera ganti password ini** setelah login pertama kali (lihat bagian "Mengganti Password Admin" di bawah).

## Struktur Folder

```
sekolah-website/
├── admin/              -> Semua halaman panel admin
│   ├── includes/       -> Layout & fungsi bantu (upload foto, dsb)
│   └── assets/css/     -> Tampilan khusus admin
├── assets/             -> CSS & JS untuk website publik
├── config/
│   └── koneksi.php     -> Pengaturan koneksi database
├── database/
│   └── sekolah.sql     -> Struktur tabel + data contoh
├── includes/           -> Header, navbar, footer untuk website publik
├── uploads/            -> Tempat menyimpan foto yang diunggah admin
│   ├── guru/
│   ├── galeri/
│   ├── berita/
│   └── profil/
├── index.php            -> Beranda
├── profil.php           -> Halaman Profil Sekolah
├── guru.php              -> Halaman Guru & Staf
├── galeri.php            -> Halaman Galeri
├── berita.php            -> Daftar Berita
├── berita_detail.php     -> Detail satu berita
└── kontak.php            -> Formulir Kontak
```

## Mengganti Password Admin

Belum ada halaman khusus ganti password di versi ini. Cara tercepat mengganti password:

1. Buat hash password baru. Buat file sementara `buat_hash.php` di folder mana saja, isi:
   ```php
   <?php echo password_hash('password_baru_anda', PASSWORD_DEFAULT);
   ```
   Jalankan lewat browser, salin hasilnya (teks panjang diawali `$2y$...`).
2. Buka phpMyAdmin -> tabel `admin` -> edit baris admin -> tempel hash tadi ke kolom `password`.
3. Hapus file `buat_hash.php` setelah selesai (jangan sampai tertinggal di server).

## Kustomisasi

- **Ganti nama sekolah, alamat, sejarah, visi/misi** lewat menu **Profil Sekolah** di panel admin —
  tidak perlu mengedit kode.
- **Warna & tampilan** bisa diubah di `assets/css/style.css` (variabel warna ada di bagian paling atas file, `:root`).
- **Menu navigasi** bisa diedit di `includes/navbar.php`.

## Catatan Keamanan untuk Hosting Online

- Folder `uploads/` sudah diberi `.htaccess` agar file PHP tidak bisa dieksekusi dari sana.
- Selalu gunakan HTTPS di hosting produksi.
- Jangan lupa ganti password admin default sebelum website dipakai publik.
- Backup database secara berkala lewat phpMyAdmin (Export -> SQL).

## Kebutuhan Server

- PHP 7.4 ke atas (disarankan PHP 8.x), dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`.
- MySQL / MariaDB.
- Modul Apache `mod_rewrite` tidak wajib (tidak dipakai di versi ini).
