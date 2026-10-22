<footer class="footer">
  <div class="container">
    <div class="grid-footer">
      <div>
        <h4<?php
$namaSekolah = $namaSekolah ?? 'SMK Infokom Kota Bogor';
?>
        <p><?= nl2br(htmlspecialchars($profil['alamat'] ?? '')) ?></p>
        <p>
          <?= htmlspecialchars($profil['telepon'] ?? '') ?><br>
          <?= htmlspecialchars($profil['email'] ?? '') ?>
        </p>
      </div>
      <div>
        <h4>Tautan</h4>
        <ul>
          <li><a href="<?= BASE_URL ?>/profil.php">Profil Sekolah</a></li>
          <li><a href="<?= BASE_URL ?>/guru.php">Guru & Staf</a></li>
          <li><a href="<?= BASE_URL ?>/galeri.php">Galeri Kegiatan</a></li>
          <li><a href="<?= BASE_URL ?>/berita.php">Berita</a></li>
        </ul>
      </div>
      <div>
        <h4>Jam Operasional</h4>
        <p><?= htmlspecialchars($profil['jam_operasional'] ?? '') ?></p>
      </div>
    </div>
    <div class="footer-bawah">
      <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($namaSekolah) ?>. Hak cipta dilindungi.</span>
  </div>
</footer>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>
</html>
