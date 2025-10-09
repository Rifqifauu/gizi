<?php
include '../koneksi.php';
$title = 'Data Sub Kriteria';

// Pastikan id_kriteria ada di URL
if (!isset($_GET['id_kriteria'])) {
    die('ID Kriteria tidak ditemukan');
}
$id_kriteria = (int) $_GET['id_kriteria'];

// Ambil data kriteria induk (untuk judul atau breadcrumb)
$kriteria = $koneksi->query("SELECT * FROM kriteria WHERE id = $id_kriteria")->fetch_assoc();
if (!$kriteria) {
    die('Kriteria tidak valid');
}

// --- Fungsi hapus data ---
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $koneksi->query("DELETE FROM sub_kriteria WHERE id = $id AND id_kriteria = $id_kriteria");
    header("Location: sub-kriteria.php?id_kriteria=$id_kriteria&msg=deleted");
    exit;
}

// --- Fungsi tambah data ---
if (isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama = $_POST['nama'];
    // Konversi string kosong menjadi NULL
    $batas_bawah = !empty($_POST['batas_bawah']) ? $_POST['batas_bawah'] : null;
    $batas_atas = !empty($_POST['batas_atas']) ? $_POST['batas_atas'] : null;
    $nilai_skala = $_POST['nilai_skala'];

    $stmt = $koneksi->prepare("INSERT INTO sub_kriteria (id_kriteria, nama, batas_bawah, batas_atas, nilai_skala) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isddi", $id_kriteria, $nama, $batas_bawah, $batas_atas, $nilai_skala);
    $stmt->execute();
    header("Location: sub-kriteria.php?id_kriteria=$id_kriteria&msg=added");
    exit;
}

// --- Fungsi edit data ---
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int) $_POST['id'];
    $nama = $_POST['nama'];
    // Konversi string kosong menjadi NULL
    $batas_bawah = !empty($_POST['batas_bawah']) ? $_POST['batas_bawah'] : null;
    $batas_atas = !empty($_POST['batas_atas']) ? $_POST['batas_atas'] : null;
    $nilai_skala = $_POST['nilai_skala'];

    $stmt = $koneksi->prepare("UPDATE sub_kriteria SET nama=?, batas_bawah=?, batas_atas=?, nilai_skala=? WHERE id=? AND id_kriteria=?");
    $stmt->bind_param("sddiii", $nama, $batas_bawah, $batas_atas, $nilai_skala, $id, $id_kriteria);
    $stmt->execute();
    header("Location: sub-kriteria.php?id_kriteria=$id_kriteria&msg=updated");
    exit;
}

// --- Ambil data sub kriteria ---
$data = $koneksi->query("SELECT * FROM sub_kriteria WHERE id_kriteria = $id_kriteria ORDER BY nilai_skala ASC");

// Mulai output
ob_start();
?>

<div class="container py-4">
    <div class="d-flex justify-content-end p-4"><a href="/views/kriteria.php"><span><i class="bi bi-arrow-left ">&nbsp;</i></span>Kembali</a></div>
    
    <?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php
    switch($_GET['msg']) {
      case 'added': echo 'Data berhasil ditambahkan!'; break;
      case 'updated': echo 'Data berhasil diperbarui!'; break;
      case 'deleted': echo 'Data berhasil dihapus!'; break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
  <div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <div>
        <h3 class="m-0">Sub Kriteria: <?= htmlspecialchars($kriteria['nama']) ?></h3>
      </div>
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah</button>
    </div>

    <div class="table-responsive">
      <table class="table align-middle table-hover m-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Batas Bawah</th>
            <th>Batas Atas</th>
            <th>Nilai Skala</th>
            <th width="90" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data->num_rows === 0): ?>
            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada sub kriteria</td></tr>
          <?php else: $no=1; while($row=$data->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= $row['batas_bawah'] !== null ? htmlspecialchars($row['batas_bawah']) : '-' ?></td>
              <td><?= $row['batas_atas'] !== null ? htmlspecialchars($row['batas_atas']) : '-' ?></td>
              <td><?= htmlspecialchars($row['nilai_skala']) ?></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="?action=hapus&id=<?= $row['id'] ?>&id_kriteria=<?= $id_kriteria ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin hapus?')">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST">
                    <div class="modal-header bg-primary text-white">
                      <h5 class="modal-title">Edit Sub Kriteria</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="action" value="edit">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" value="<?= $row['nama'] ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Batas Bawah (Z-score)</label>
                        <input type="number" step="0.01" name="batas_bawah" value="<?= $row['batas_bawah'] ?>" class="form-control" placeholder="Kosongkan jika tidak ada">
                      </div>
                      <div class="mb-3">
                        <label>Batas Atas (Z-score)</label>
                        <input type="number" step="0.01" name="batas_atas" value="<?= $row['batas_atas'] ?>" class="form-control" placeholder="Kosongkan jika tidak ada">
                      </div>
                      <div class="mb-3">
                        <label>Nilai Skala</label>
                        <input type="number" name="nilai_skala" value="<?= $row['nilai_skala'] ?>" class="form-control" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Simpan</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Tambah -->
  <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Tambah Sub Kriteria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Batas Bawah (Z-score)</label>
              <input type="number" step="0.01" name="batas_bawah" class="form-control" placeholder="Kosongkan jika tidak ada">
            </div>
            <div class="mb-3">
              <label>Batas Atas (Z-score)</label>
              <input type="number" step="0.01" name="batas_atas" class="form-control" placeholder="Kosongkan jika tidak ada">
            </div>
            <div class="mb-3">
              <label>Nilai Skala</label>
              <input type="number" name="nilai_skala" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>