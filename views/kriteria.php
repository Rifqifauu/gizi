<?php
$title = 'Kriteria';

// Mulai "menangkap" konten halaman
ob_start();
?>
<?php
include '../koneksi.php';
$title = 'Data Kriteria';

// Fungsi hapus data
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $koneksi->query("DELETE FROM kriteria WHERE id = $id");
    header("Location: ?msg=deleted");
    exit;
}

// Fungsi tambah data
if (isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $bobot = $_POST['bobot'];

    $stmt = $koneksi->prepare("INSERT INTO kriteria (kode, nama, jenis, bobot) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $kode, $nama, $jenis, $bobot);
    $stmt->execute();
    header("Location: ?msg=added");
    exit;
}

// Fungsi update data
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $bobot = $_POST['bobot'];

    $stmt = $koneksi->prepare("UPDATE kriteria SET kode=?, nama=?, jenis=?, bobot=? WHERE id=?");
    $stmt->bind_param("sssdi", $kode, $nama, $jenis, $bobot, $id);
    $stmt->execute();
    header("Location: ?msg=updated");
    exit;
}

// Ambil data (read)
$data = $koneksi->query("SELECT * FROM kriteria ORDER BY id DESC");
?>

<div class="container py-4">
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
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
      <h3 class="m-0">Data Kriteria</h3>
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah</button>
    </div>

    <div class="table-responsive">
      <table class="table align-middle table-hover m-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Jenis</th>
            <th>Bobot</th>
            <th width="90" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data->num_rows === 0): ?>
            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data</td></tr>
          <?php else: $no=1; while($row=$data->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['kode']) ?></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['jenis']) ?></td>
              <td><?= htmlspecialchars($row['bobot']) ?></td>
              <td class="d-flex justify-content-center items-center gap-2 text-center">
                 <a href="sub-kriteria.php?id_kriteria=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success"
                 >
                   <i class="bi bi-eye"></i>
                </a>
                <button class="btn btn-sm btn-outline-success" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalEdit<?= $row['id'] ?>">
                        <i class="bi bi-pencil"></i>
                </button>
                <a href="?action=hapus&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Yakin ingin hapus?')">
                   <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST">
                    <div class="modal-header bg-success text-white">
                      <h5 class="modal-title">Edit Kriteria</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="action" value="edit">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <div class="mb-3">
                        <label>Kode</label>
                        <input type="text" name="kode" value="<?= $row['kode'] ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" value="<?= $row['nama'] ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Jenis</label>
                        <input type="text" name="jenis" value="<?= $row['jenis'] ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Bobot</label>
                        <input type="number" step="0.01" name="bobot" value="<?= $row['bobot'] ?>" class="form-control" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-success">Simpan</button>
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
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Tambah Kriteria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3">
              <label>Kode</label>
              <input type="text" name="kode" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Jenis</label>
              <input type="text" name="jenis" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Bobot</label>
              <input type="number" step="0.01" name="bobot" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" type="submit">Simpan</button>
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
<?php
// Ambil hasil output ke variabel $content
$content = ob_get_clean();

// Panggil layout utama
include __DIR__ . '/layout.php';
