<?php
$title = 'Hasil Akhir';
include '../koneksi.php';

/* ==================== BULK ARCHIVE (DENGAN FORM ARSIP) ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'bulk_archive' && !empty($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $ids_str = implode(',', $ids);

    $nama_arsip = $koneksi->real_escape_string($_POST['nama_arsip']);
    $archived_at = $koneksi->real_escape_string($_POST['archived_at']);

    // 1. Insert arsip baru
    $insertArsip = $koneksi->query("INSERT INTO arsip (nama, archived_at) VALUES ('$nama_arsip', '$archived_at')");
    if (!$insertArsip) {
        die("Gagal membuat arsip: " . $koneksi->error);
    }
    $arsip_id = $koneksi->insert_id;

    // 2. Update alternatif terpilih agar masuk ke arsip
    if (!empty($ids_str)) {
        $update = $koneksi->query("
            UPDATE alternatif 
            SET is_archived = 1, id_arsip = $arsip_id 
            WHERE id IN ($ids_str)
        ");
        if (!$update) {
            die("Gagal mengarsipkan data: " . $koneksi->error);
        }
    }

    header("Location: ?tab=hasil-akhir&msg=bulk_archived");
    exit;
}

/* ==================== PAGINATION ==================== */
$limit = 10;
$page_net = isset($_GET['page_net']) ? (int)$_GET['page_net'] : 1;
if ($page_net < 1) $page_net = 1;
$offset = ($page_net - 1) * $limit;

// Ambil total alternatif aktif
$total_result = $koneksi->query("SELECT COUNT(*) as total FROM alternatif WHERE is_archived = 0");
if (!$total_result) die("ERROR Query total: " . $koneksi->error);
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil semua alternatif aktif untuk perhitungan flow
$all_alternatif_result = $koneksi->query("SELECT id FROM alternatif WHERE is_archived = 0");
if (!$all_alternatif_result) die("ERROR Query all_alternatif: " . $koneksi->error);

$all_alternatif = [];
while ($row = $all_alternatif_result->fetch_assoc()) {
    $all_alternatif[] = $row['id'];
}
$total_alternatif = count($all_alternatif);

/* ==================== PERHITUNGAN FLOW ==================== */
$flows = [];
foreach ($all_alternatif as $id) {
    // Leaving flow
    $res1 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_1_id = $id");
    $leaving = ($res1 && $row1 = $res1->fetch_assoc()) 
        ? ($row1['total'] ?? 0) / max($total_alternatif - 1, 1)
        : 0;

    // Entering flow
    $res2 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_2_id = $id");
    $entering = ($res2 && $row2 = $res2->fetch_assoc()) 
        ? ($row2['total'] ?? 0) / max($total_alternatif - 1, 1)
        : 0;

    $flows[$id] = [
        'leaving' => $leaving,
        'entering' => $entering,
        'net' => $leaving - $entering,
    ];
}

// Urutkan berdasarkan Net Flow DESC dan beri ranking
uasort($flows, fn($a, $b) => $b['net'] <=> $a['net']);
$rank = 1;
foreach ($flows as $id => &$flow) {
    $flow['rank'] = $rank++;
}
unset($flow);

// Ambil data sesuai halaman
$sorted_ids = array_keys($flows);
$paginated_ids = array_slice($sorted_ids, $offset, $limit);

$data_for_display = [];
foreach ($paginated_ids as $id) {
    $result = $koneksi->query("SELECT id, nama, status_bb_u FROM alternatif WHERE id = $id");
    if ($result && $result->num_rows > 0) $data_for_display[] = $result->fetch_assoc();
}
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'bulk_archived'): ?>
  <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
    Data berhasil diarsipkan!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Net Flow (Φ) & Ranking</h3>
    <div class="mb-3 px-3">
        <button type="button" id="bulkArchiveBtn" class="btn btn-md btn-warning me-2" style="display:none;" data-bs-toggle="modal" data-bs-target="#arsipModal">
            <i class="bi bi-archive"></i> Arsipkan Terpilih
        </button>
        <a href="/script/export-pdf.php" class="btn btn-md btn-danger">Export PDF</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table align-middle table-hover m-0">
        <thead class="table-light">
            <tr>
                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                <th>Ranking</th>
                <th>Nama Alternatif</th>
                <th>Leaving Flow (Φ⁺)</th>
                <th>Entering Flow (Φ⁻)</th>
                <th>Status Gizi</th>
                <th>Net Flow (Φ)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_data === 0): ?>
                <tr><td colspan="7" class="text-center text-danger py-3"><strong>⚠️ Tidak ada data di tabel alternatif</strong></td></tr>
            <?php elseif (count($data_for_display) === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data</td></tr>
            <?php else: foreach ($data_for_display as $row): 
                $id = $row['id'];
                $flow = $flows[$id] ?? ['leaving'=>0,'entering'=>0,'net'=>0,'rank'=>'-'];
            ?>
                <tr>
                    <td><input type="checkbox" class="form-check-input row-check" value="<?= $id ?>"></td>
                    <td><?= $flow['rank'] ?></td>
                    <td><?= htmlspecialchars($row['nama'] ?: 'Null') ?></td>
                    <td><?= number_format($flow['leaving'], 3) ?></td>
                    <td><?= number_format($flow['entering'], 3) ?></td>
                    <td><?= htmlspecialchars($row['status_bb_u'] ?: 'Null') ?></td>
                    <td><?= number_format($flow['net'], 3) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Arsip -->
<div class="modal fade" id="arsipModal" tabindex="-1" aria-labelledby="arsipModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="bulkArchiveForm" method="POST" class="modal-content">
      <input type="hidden" name="action" value="bulk_archive">
      <div id="selectedContainer"></div>
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="arsipModalLabel">Buat Arsip Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="nama_arsip" class="form-label">Nama Arsip</label>
          <input type="text" class="form-control" id="nama_arsip" name="nama_arsip" placeholder="Contoh: Arsip November 2025" required>
        </div>
        <div class="mb-3">
          <label for="archived_at" class="form-label">Tanggal Arsip</label>
          <input type="datetime-local" class="form-control" id="archived_at" name="archived_at" value="<?= date('Y-m-d\TH:i') ?>" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Simpan & Arsipkan</button>
      </div>
    </form>
  </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center px-3">
    <?php
    $start = $offset + 1;
    $end = min($offset + count($data_for_display), $total_data);
    if ($total_data > 0):
    ?>
    <div class="px-3 pt-2">
        <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data</small>
    </div>
    <?php endif; ?>

    <?php if ($total_pages > 1): ?>
    <nav>
        <ul class="pagination justify-content-end my-3 px-3">
            <li class="page-item <?= $page_net <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?tab=hasil-akhir&page_net=<?= $page_net - 1 ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page_net ? 'active' : '' ?>">
                    <a class="page-link" href="?tab=hasil-akhir&page_net=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page_net >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?tab=hasil-akhir&page_net=<?= $page_net + 1 ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('selectAll');
  const bulkArchiveBtn = document.getElementById('bulkArchiveBtn');
  const selectedContainer = document.getElementById('selectedContainer');

  function getRowCheckboxes() {
    return Array.from(document.querySelectorAll('.row-check'));
  }

  function updateBulkArchiveBtn() {
    const checkboxes = getRowCheckboxes();
    const anyChecked = checkboxes.some(cb => cb.checked);
    bulkArchiveBtn.style.display = anyChecked ? 'inline-block' : 'none';
  }

  // Toggle semua berdasarkan selectAll
  selectAll?.addEventListener('change', function() {
    const checkboxes = getRowCheckboxes();
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkArchiveBtn();
  });

  // Event listener untuk setiap checkbox baris
  function attachRowListeners() {
    const checkboxes = getRowCheckboxes();
    checkboxes.forEach(cb => {
      cb.removeEventListener('change', updateBulkArchiveBtn);
      cb.addEventListener('change', function() {
        if (!this.checked) selectAll.checked = false;
        const allChecked = getRowCheckboxes().every(c => c.checked);
        if (allChecked) selectAll.checked = true;
        updateBulkArchiveBtn();
      });
    });
  }

  // Initial attach
  attachRowListeners();
  updateBulkArchiveBtn();

  // Saat modal akan dibuka, tambahkan ID terpilih ke form
  const arsipModal = document.getElementById('arsipModal');
  arsipModal.addEventListener('show.bs.modal', function () {
    const checkboxes = getRowCheckboxes().filter(cb => cb.checked);
    selectedContainer.innerHTML = '';
    checkboxes.forEach(cb => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'selected_ids[]';
      input.value = cb.value;
      selectedContainer.appendChild(input);
    });
  });
});
</script>
