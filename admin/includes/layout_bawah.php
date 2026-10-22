    </div>
  </div>
</div>
<script>
document.querySelectorAll('.konfirmasi-hapus').forEach(function (el) {
  el.addEventListener('click', function (e) {
    if (!confirm('Yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.')) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
