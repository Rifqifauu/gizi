<?php
$title = 'User';
include '../koneksi.php';
ob_start();

// --- Hapus data ---
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $koneksi->query("DELETE FROM user WHERE id = $id");
    header("Location: ?msg=deleted");
    exit;
}

// --- Tambah user ---
if (isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = $_POST['role'];

    $stmt = $koneksi->prepare("INSERT INTO user (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $username, $password, $role);
    $stmt->execute();
    header("Location: ?msg=added");
    exit;
}

// --- Edit user ---
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int) $_POST['id'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $koneksi->prepare("UPDATE user SET username=?, name=?, password=?, role=? WHERE id=?");
    $stmt->bind_param("ssssi", $username, $name, $password, $role, $id);
    $stmt->execute();
    header("Location: ?msg=updated");
    exit;
}

// --- Pagination ---
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_data = $koneksi->query("SELECT COUNT(*) as total FROM user")->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data dengan limit dan offset
$data = $koneksi->query("SELECT * FROM user ORDER BY id DESC LIMIT $limit OFFSET $offset");

$start = $offset + 1;
$end = $offset + $data->num_rows;
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
      <h3 class="m-0">Data User</h3>
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah</button>
    </div>

    <div class="table-responsive">
      <table class="table align-middle table-hover m-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th width="90" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data->num_rows === 0): ?>
            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data</td></tr>
          <?php else: $no = $offset + 1; while($row=$data->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['role']) ?></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="?action=hapus&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin hapus?')">
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
                      <h5 class="modal-title">Edit User</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="action" value="edit">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" value="<?= $row['name'] ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= $row['username'] ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                          <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                          <option value="kepala" <?= $row['role'] === 'kepala' ? 'selected' : '' ?>>Kepala</option>
                        </select>
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
<div class="d-flex justify-content-between align-items-center px-3">
      <?php
$start = $offset + 1;
$end = $offset + $data->num_rows;
?>

<!-- Info data -->
<?php if ($total_data > 0): ?>
  <div class="px-3 pt-2">
    <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data</small>
  </div>
<?php endif; ?>

<!-- Pagination -->
<?php if ($total_pages > 0): ?>
  <div>
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-end my-3 px-3">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">Previous</a>
      </li>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
      </li>
    </ul>
  </nav>
  </div>
<?php endif; ?>
</div>
  </div>

  <!-- Modal Tambah -->
  <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Tambah User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Role</label>
              <select name="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="kepala">Kepala</option>
              </select>
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
$content = ob_get_clean();
include __DIR__ . '/layout.php';
