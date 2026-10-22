document.addEventListener('DOMContentLoaded', function () {
  // Toggle menu navigasi di layar kecil
  var tombolToggle = document.querySelector('.navbar-toggle');
  var menu = document.querySelector('.navbar-menu');
  if (tombolToggle && menu) {
    tombolToggle.addEventListener('click', function () {
      menu.classList.toggle('terbuka');
    });
  }

  // Tutup pesan status otomatis setelah beberapa detik
  document.querySelectorAll('.pesan-status').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 5000);
  });

  // Konfirmasi sebelum menghapus data (dipakai di panel admin)
  document.querySelectorAll('.konfirmasi-hapus').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm('Yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.')) {
        e.preventDefault();
      }
    });
  });

  // Pratinjau foto sebelum diunggah (form tambah/edit dengan input file)
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var target = document.querySelector(input.dataset.preview);
      if (!target || !input.files || !input.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (e) { target.style.backgroundImage = 'url(' + e.target.result + ')'; };
      reader.readAsDataURL(input.files[0]);
    });
  });
});
