<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$pesanError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $koneksi->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];
        header('Location: dashboard.php');
        exit;
    }
    $pesanError = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --papan:#1C2B39; --kapur:#E3A93B; --kertas:#F6F2E9; --garis:#DCD5C4; --teks-muda:#5C574C;
  }
  * { box-sizing: border-box; }
  body {
    margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
    background: var(--papan); font-family:'Inter',sans-serif;
  }
  .kotak-login {
    background:#fff; padding:40px 36px; border-radius:8px; width:100%; max-width:380px;
    box-shadow:0 20px 50px rgba(0,0,0,0.3);
  }
  .kotak-login h1 { font-family:'Fraunces',serif; font-size:1.5rem; margin:0 0 6px; color:var(--papan); }
  .kotak-login p.sub { color: var(--teks-muda); font-size:0.9rem; margin:0 0 26px; }
  .grup-form { margin-bottom:16px; }
  .grup-form label { display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--papan); }
  .grup-form input {
    width:100%; padding:11px 13px; border:1px solid var(--garis); border-radius:6px; font-size:0.95rem;
  }
  .tombol-masuk {
    width:100%; padding:12px; border:none; border-radius:6px; background:var(--kapur); color:var(--papan);
    font-weight:700; font-size:0.95rem; cursor:pointer; margin-top:6px;
  }
  .tombol-masuk:hover { opacity:0.92; }
  .galat { background:#FBE6E4; color:#8A2C22; padding:10px 14px; border-radius:6px; font-size:0.88rem; margin-bottom:18px; }
  .kembali { display:block; text-align:center; margin-top:20px; font-size:0.85rem; color:var(--teks-muda); text-decoration:none; }
</style>
</head>
<body>
  <div class="kotak-login">
    <h1>Masuk ke Panel Admin</h1>
    <p class="sub">Kelola konten website sekolah Anda.</p>
    <?php if ($pesanError): ?>
      <div class="galat"><?= htmlspecialchars($pesanError) ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
      <div class="grup-form">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>
      <div class="grup-form">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="tombol-masuk">Masuk</button>
    </form>
    <a class="kembali" href="../index.php">&larr; Kembali ke website</a>
  </div>
</body>
</html>
