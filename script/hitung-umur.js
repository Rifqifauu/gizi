function hitungUmur(tglLahir, tglTimbang, targetInput) {
  if (!tglLahir || !tglTimbang) return;
  const lahir = new Date(tglLahir);
  const timbang = new Date(tglTimbang);

  if (timbang < lahir) {
    targetInput.value = '';
    return;
  }

  let tahun = timbang.getFullYear() - lahir.getFullYear();
  let bulan = timbang.getMonth() - lahir.getMonth();
  let totalBulan = tahun * 12 + bulan;

  // Koreksi kalau tanggal timbang masih sebelum tanggal lahir dalam bulan
  if (timbang.getDate() < lahir.getDate()) {
    totalBulan -= 1;
  }

  targetInput.value = totalBulan >= 0 ? totalBulan : 0;
}

document.getElementById('tgl_timbang_tambah').addEventListener('change', function() {
  hitungUmur(
    document.getElementById('tgl_lahir_tambah').value,
    this.value,
    document.getElementById('umur_tambah')
  );
});
document.getElementById('tgl_lahir_tambah').addEventListener('change', function() {
  hitungUmur(
    this.value,
    document.getElementById('tgl_timbang_tambah').value,
    document.getElementById('umur_tambah')
  );
});

// Untuk modal edit, pakai event delegation (karena modal banyak)
document.addEventListener('change', function(e) {
  if (e.target.matches('[id^="tgl_timbang_edit_"], [id^="tgl_lahir_edit_"]')) {
    const id = e.target.id.split('_').pop();
    hitungUmur(
      document.getElementById('tgl_lahir_edit_' + id).value,
      document.getElementById('tgl_timbang_edit_' + id).value,
      document.getElementById('umur_edit_' + id)
    );
  }
});