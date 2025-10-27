<?php
$title = 'Data Balita';
include '../koneksi.php';
ob_start();

function getStatus($koneksi, $id_kriteria, $nilai) {
    if ($nilai === null) {
        return 'Tidak Diketahui';
    }

    $stmt = $koneksi->prepare("
        SELECT nama FROM sub_kriteria 
        WHERE id_kriteria=? 
        AND (
            (batas_bawah IS NULL AND batas_atas IS NULL) 
            OR (batas_bawah IS NULL AND ? <= batas_atas) 
            OR (batas_atas IS NULL AND ? >= batas_bawah)
            OR (batas_bawah IS NOT NULL AND batas_atas IS NOT NULL AND ? BETWEEN batas_bawah AND batas_atas)
        )
        LIMIT 1
    ");
    // id_kriteria terkadang dipakai sebagai string di DB, tapi bind sebagai integer lebih aman.
    // Jika id_kriteria memang string, ubah tipe di bind_param.
    $stmt->bind_param("iddd", $id_kriteria, $nilai, $nilai, $nilai);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row ) {
        return $row['nama'];
    }
    return 'Tidak Diketahui';
}

// Hitung Z-Score IMT berdasarkan WHO reference (tabel who_imt_ref)
function getZScoreIMT($koneksi, $sex, $umur_bulan, $imt) {
    $stmt = $koneksi->prepare("
        SELECT median, sd 
        FROM who_imt_ref 
        WHERE sex = ? AND umur_bln = ? 
        LIMIT 1
    ");
    $stmt->bind_param("si", $sex, $umur_bulan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $median = $row['median'];
        $sd = $row['sd'];
        if ($sd != 0) {
            $z = ($imt - $median) / $sd;
            return round($z, 2);
        }
    }

    return null; // kalau referensi tidak ditemukan
}

/* ==================== HAPUS DATA SATUAN ==================== */
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $koneksi->query("DELETE FROM alternatif WHERE id = $id");
    header("Location: ?msg=deleted");
    exit;
}

/* ==================== BULK DELETE ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $ids_str = implode(',', $ids);

    if (!empty($ids_str)) {
        $koneksi->query("DELETE FROM alternatif WHERE id IN ($ids_str)");
        header("Location: ?msg=bulk_deleted");
        exit;
    }
}

/* ==================== TAMBAH DATA ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama = $_POST['nama'];
    $sex = $_POST['sex'];
    $tgl_timbang = $_POST['tgl_timbang'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $umur = $_POST['umur'];
    $bb = $_POST['bb'];
    $tb = $_POST['tb'];
    $z_score_tb_u = $_POST['z_score_tb_u'];
    $z_score_bb_u = $_POST['z_score_bb_u'];
    $z_score_bb_tb = $_POST['z_score_bb_tb'];

    $imt = $tb > 0 ? $bb / (($tb / 100) ** 2) : 0;
    $z_score_imt = getZScoreIMT($koneksi, $sex, $umur, $imt);

    $status_bb_u = getStatus($koneksi, '1', $z_score_bb_u);
    $status_tb_u = getStatus($koneksi, '2', $z_score_tb_u);
    $status_bb_tb = getStatus($koneksi, '3', $z_score_bb_tb);
    $status_imt_u = getStatus($koneksi, '4', $z_score_imt);

    $stmt = $koneksi->prepare("
        INSERT INTO alternatif (
            nama, sex, tgl_timbang, tgl_lahir, umur, bb, tb,
            z_score_tb_u, z_score_bb_u, z_score_bb_tb,
            status_tb_u, status_bb_u, status_bb_tb,
            imt, z_score_imt, status_imt
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssidddddsssdds",
        $nama, $sex, $tgl_timbang, $tgl_lahir, $umur, $bb, $tb,
        $z_score_tb_u, $z_score_bb_u, $z_score_bb_tb,
        $status_tb_u, $status_bb_u, $status_bb_tb,
        $imt, $z_score_imt, $status_imt_u
    );
    $stmt->execute();

    header("Location: ?msg=added");
    exit;
}

/* ==================== EDIT DATA ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int) $_POST['id'];
    $nama = $_POST['nama'];
    $sex = $_POST['sex'];
    $tgl_timbang = $_POST['tgl_timbang'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $umur = $_POST['umur'];
    $bb = $_POST['bb'];
    $tb = $_POST['tb'];
    $z_score_tb_u = $_POST['z_score_tb_u'];
    $z_score_bb_u = $_POST['z_score_bb_u'];
    $z_score_bb_tb = $_POST['z_score_bb_tb'];

    $imt = $tb > 0 ? $bb / (($tb / 100) ** 2) : 0;
    $z_score_imt = getZScoreIMT($koneksi, $sex, $umur, $imt);

    $status_bb_u = getStatus($koneksi, '1', $z_score_bb_u);
    $status_tb_u = getStatus($koneksi, '2', $z_score_tb_u);
    $status_bb_tb = getStatus($koneksi, '3', $z_score_bb_tb);
    $status_imt_u = getStatus($koneksi, '4', $z_score_imt);

    $stmt = $koneksi->prepare("
        UPDATE alternatif 
        SET nama=?, sex=?, tgl_timbang=?, tgl_lahir=?, umur=?, bb=?, tb=?,
            z_score_tb_u=?, z_score_bb_u=?, z_score_bb_tb=?,
            status_tb_u=?, status_bb_u=?, status_bb_tb=?,
            imt=?, z_score_imt=?, status_imt=?
        WHERE id=?
    ");
    $stmt->bind_param(
        "ssssidddddsssddsi",
        $nama, $sex, $tgl_timbang, $tgl_lahir, $umur, $bb, $tb,
        $z_score_tb_u, $z_score_bb_u, $z_score_bb_tb,
        $status_tb_u, $status_bb_u, $status_bb_tb,
        $imt, $z_score_imt, $status_imt_u, $id
    );
    $stmt->execute();

    header("Location: ?msg=updated");
    exit;
}

/* ==================== PAGINATION ==================== */
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_data = $koneksi->query("SELECT COUNT(*) as total FROM alternatif")->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

$data = $koneksi->query("SELECT * FROM alternatif ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>

<div class="container py-4">
<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php
    switch($_GET['msg']) {
      case 'added': echo 'Data berhasil ditambahkan!'; break;
      case 'updated': echo 'Data berhasil diperbarui!'; break;
      case 'deleted': echo 'Data berhasil dihapus!'; break;
      case 'bulk_deleted': echo 'Beberapa data berhasil dihapus!'; break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Data Balita</h3>
    <div>
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah</button>
      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">Import Excel</button>
      <!-- Tombol bulk delete, awalnya disembunyikan -->
      <button id="bulkDeleteBtn"
        type="button"
        class="btn btn-danger btn-sm"
        style="display: none;"
        onclick="submitBulkDelete()">
        Hapus Terpilih
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover m-0">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>No</th>
          <th>Nama</th>
          <th>JK</th>
          <th>Tgl Timbang</th>
          <th>Tgl Lahir</th>
          <th>Umur (bln)</th>
          <th>BB (kg)</th>
          <th>TB (cm)</th>
          <th>TB/U</th>
          <th>BB/U</th>
          <th>BB/TB</th>
          <th>Status TB/U</th>
          <th>Status BB/U</th>
          <th>Status BB/TB</th>
          <th>IMT</th>
          <th>Z IMT</th>
          <th>Status IMT</th>
          <th width="90" class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($data->num_rows === 0): ?>
        <tr><td colspan="19" class="text-center text-muted py-3">Belum ada data</td></tr>
      <?php else: $no = $offset + 1; while($row = $data->fetch_assoc()): ?>
        <tr>
          <td><input type="checkbox" class="row-check" value="<?= $row['id'] ?>"></td>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['sex'] ?: 'Null') ?></td>
          <td><?= !empty($row['tgl_timbang']) ? date('d-m-Y', strtotime($row['tgl_timbang'])) : 'Null' ?></td>
          <td><?= !empty($row['tgl_lahir']) ? date('d-m-Y', strtotime($row['tgl_lahir'])) : 'Null' ?></td>
          <td><?= htmlspecialchars($row['umur'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['bb'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['tb'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['z_score_tb_u'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['z_score_bb_u'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['z_score_bb_tb'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['status_tb_u'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['status_bb_u'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['status_bb_tb'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['imt'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['z_score_imt'] ?: 'Null') ?></td>
          <td><?= htmlspecialchars($row['status_imt'] ?: 'Null') ?></td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
              <i class="bi bi-pencil"></i>
            </button>
            <a href="?action=hapus&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin hapus data ini?')">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>

        <!-- Modal Edit untuk setiap baris -->
        <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST">
                <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">Edit Alternatif</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">

                  <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Jenis Kelamin</label>
                    <select name="sex" class="form-select" required>
                      <option value="L" <?= $row['sex']=='L'?'selected':'' ?>>Laki-laki</option>
                      <option value="P" <?= $row['sex']=='P'?'selected':'' ?>>Perempuan</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label>Tanggal Timbang</label>
                    <input type="date" name="tgl_timbang" value="<?= $row['tgl_timbang'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" value="<?= $row['tgl_lahir'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Umur (bulan)</label>
                    <input type="number" name="umur" value="<?= $row['umur'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>BB (kg)</label>
                    <input type="number" step="0.1" name="bb" value="<?= $row['bb'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>TB (cm)</label>
                    <input type="number" step="0.1" name="tb" value="<?= $row['tb'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Z-Score TB/U</label>
                    <input type="number" step="0.01" name="z_score_tb_u" value="<?= $row['z_score_tb_u'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Z-Score BB/U</label>
                    <input type="number" step="0.01" name="z_score_bb_u" value="<?= $row['z_score_bb_u'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Z-Score BB/TB</label>
                    <input type="number" step="0.01" name="z_score_bb_tb" value="<?= $row['z_score_bb_tb'] ?>" class="form-control" required>
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

  <!-- Hidden form untuk bulk delete (tidak menumpuk/nest dengan form lain) -->
  <form id="bulkDeleteForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="bulk_delete">
    <div id="selectedContainer"></div>
  </form>

  <!-- Pagination & Info -->
  <div class="d-flex justify-content-between align-items-center px-3">
    <?php
    $start = $offset + 1;
    $end = $offset + $data->num_rows;
    ?>
    <?php if ($total_data > 0): ?>
    <div class="px-3 pt-2">
      <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data</small>
    </div>
    <?php endif; ?>

    <?php if ($total_pages > 1): ?>
    <nav>
      <ul class="pagination justify-content-end my-3 px-3">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
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
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Tambah Alternatif</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="tambah">
          <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="sex" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Tanggal Timbang</label>
            <input type="date" name="tgl_timbang" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Umur (bulan)</label>
            <input type="number" name="umur" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>BB (kg)</label>
            <input type="number" step="0.1" name="bb" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>TB (cm)</label>
            <input type="number" step="0.1" name="tb" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Z-Score TB/U</label>
            <input type="number" step="0.01" name="z_score_tb_u" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Z-Score BB/U</label>
            <input type="number" step="0.01" name="z_score_bb_u" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Z-Score BB/TB</label>
            <input type="number" step="0.01" name="z_score_bb_tb" class="form-control" required>
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

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" action="/import.php">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Import Data Balita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>File Excel (.xlsx)</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
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

<!-- JS untuk Pilih Semua, show/hide tombol bulk, dan submit via hidden form -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('selectAll');
  const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
  const selectedContainer = document.getElementById('selectedContainer');

  // query selector dinamis: ambil semua dengan class row-check
  function getRowCheckboxes() {
    return Array.from(document.querySelectorAll('.row-check'));
  }

  function updateBulkDeleteBtn() {
    const checkboxes = getRowCheckboxes();
    const anyChecked = checkboxes.some(cb => cb.checked);
    bulkDeleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
  }

  // toggle semua berdasarkan selectAll
  selectAll.addEventListener('change', function() {
    const checkboxes = getRowCheckboxes();
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkDeleteBtn();
  });

  // tambahkan event listener ke setiap checkbox baris (diperbarui dinamis)
  function attachRowListeners() {
    const checkboxes = getRowCheckboxes();
    checkboxes.forEach(cb => {
      cb.removeEventListener('change', updateBulkDeleteBtn);
      cb.addEventListener('change', function() {
        // jika ada satu tidak dicentang, uncheck selectAll
        if (!this.checked) selectAll.checked = false;
        // jika semua centang, cek selectAll
        const allChecked = getRowCheckboxes().every(c => c.checked);
        if (allChecked) selectAll.checked = true;
        updateBulkDeleteBtn();
      });
    });
  }

  // initial attach
  attachRowListeners();
  updateBulkDeleteBtn();

  // fungsi submit bulk: isi selectedContainer lalu submit form
  window.submitBulkDelete = function() {
    const checkboxes = getRowCheckboxes().filter(cb => cb.checked);
    // clear old inputs
    selectedContainer.innerHTML = '';
    if (checkboxes.length === 0) return;

    checkboxes.forEach(cb => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'selected_ids[]';
      input.value = cb.value;
      selectedContainer.appendChild(input);
    });

    if (!confirm(`Yakin ingin menghapus ${checkboxes.length} data terpilih?`)) return;
    document.getElementById('bulkDeleteForm').submit();
  };

  // Jika kamu menambahkan row secara dinamis via JS, panggil attachRowListeners() lagi setelah menambah row.
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
