<?php
$title = 'Hasil Akhir';
include '../koneksi.php';

// Ambil arsip yang aktif dari session
$id_arsip_aktif = $_SESSION['id_arsip'] ?? null;
$arsip_info = null;

// Jika ada arsip aktif, ambil nama & tanggalnya
if ($id_arsip_aktif) {
    $stmt = $koneksi->prepare("SELECT nama, archived_at FROM arsip WHERE id = ?");
    $stmt->bind_param("i", $id_arsip_aktif);
    $stmt->execute();
    $arsip_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Filter berdasarkan arsip aktif (kalau ada)
$where_arsip = $id_arsip_aktif ? "AND id_arsip = $id_arsip_aktif" : "";

/* ==================== BULK DELETE ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $ids_str = implode(',', $ids);

    if (!empty($ids_str)) {
        $koneksi->query("DELETE FROM alternatif WHERE id IN ($ids_str)");
        $redirect_params = "tab=hasil-akhir&msg=bulk_deleted";
        if ($id_arsip_aktif) $redirect_params .= "&id_arsip=$id_arsip_aktif";
        header("Location: ?$redirect_params");
        exit;
    }
}

/* ==================== BULK RESTORE ==================== */
if (isset($_POST['action']) && $_POST['action'] === 'bulk_restore' && !empty($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $ids_str = implode(',', $ids);

    if (!empty($ids_str)) {
        $koneksi->query("UPDATE alternatif SET is_archived = 0 WHERE id IN ($ids_str)");
        $redirect_params = "tab=hasil-akhir&msg=bulk_restored";
        if ($id_arsip_aktif) $redirect_params .= "&id_arsip=$id_arsip_aktif";
        header("Location: ?$redirect_params");
        exit;
    }
}

$limit = 10;
$page_net = isset($_GET['page_net']) ? (int)$_GET['page_net'] : 1;
if ($page_net < 1) $page_net = 1;
$offset = ($page_net - 1) * $limit;

// Ambil total alternatif sesuai filter arsip
$total_result = $koneksi->query("SELECT COUNT(*) as total FROM alternatif WHERE is_archived = 1 $where_arsip");
if (!$total_result) {
    die("ERROR Query total: " . $koneksi->error);
}
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil semua alternatif untuk perhitungan flow (sesuai filter arsip)
$all_alternatif_result = $koneksi->query("SELECT id FROM alternatif WHERE is_archived = 1 $where_arsip");
if (!$all_alternatif_result) {
    die("ERROR Query all_alternatif: " . $koneksi->error);
}

$all_alternatif = [];
while ($row = $all_alternatif_result->fetch_assoc()) {
    $all_alternatif[] = $row['id'];
}
$total_alternatif = count($all_alternatif);

// Hitung leaving, entering, net flow untuk semua alternatif
$flows = [];
foreach ($all_alternatif as $id) {
    // Ambil leaving flow
    $res1 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_1_id = $id");
    if (!$res1) {
        echo "<!-- ERROR Query leaving untuk id=$id: " . $koneksi->error . " -->";
        $leaving = 0;
    } else {
        $row1 = $res1->fetch_assoc();
        $leaving = ($row1['total'] ?? 0) / ($total_alternatif - 1 > 0 ? $total_alternatif - 1 : 1);
    }

    // Ambil entering flow
    $res2 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_2_id = $id");
    if (!$res2) {
        echo "<!-- ERROR Query entering untuk id=$id: " . $koneksi->error . " -->";
        $entering = 0;
    } else {
        $row2 = $res2->fetch_assoc();
        $entering = ($row2['total'] ?? 0) / ($total_alternatif - 1 > 0 ? $total_alternatif - 1 : 1);
    }

    $net = $leaving - $entering;

    $flows[$id] = [
        'leaving' => $leaving,
        'entering' => $entering,
        'net' => $net,
    ];
}

// Tambahkan ranking berdasarkan net flow DESC
uasort($flows, function($a, $b) {
    return $b['net'] <=> $a['net'];
});
$rank = 1;
foreach ($flows as $id => &$flow) {
    $flow['rank'] = $rank++;
}
unset($flow);

// SEKARANG ambil data yang akan ditampilkan (sesuai ranking)
$sorted_ids = array_keys($flows);
$paginated_ids = array_slice($sorted_ids, $offset, $limit);

$data_for_display = [];
foreach ($paginated_ids as $id) {
    // Ambil nama dari database
    $result = $koneksi->query("SELECT id, nama, status_bb_u FROM alternatif WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $data_for_display[] = $result->fetch_assoc();
    }
}

// Parameter untuk pagination & export (membawa id_arsip)
$pagination_params = "tab=hasil-akhir";
if ($id_arsip_aktif) {
    $pagination_params .= "&id_arsip={$id_arsip_aktif}";
}

$export_params = $id_arsip_aktif ? "?id_arsip={$id_arsip_aktif}" : "";
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
    <?php
    switch($_GET['msg']) {
      case 'bulk_deleted': echo 'Data berhasil dihapus!'; break;
      case 'bulk_restored': echo 'Data berhasil dikembalikan!'; break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <h3 class="m-0">Net Flow (Φ) & Ranking</h3>
        <?php if ($arsip_info): ?>
            <span class="badge bg-light text-success">
                Arsip: <?= htmlspecialchars($arsip_info['nama']) ?> – <?= htmlspecialchars(date('d M Y', strtotime($arsip_info['archived_at']))) ?>
            </span>
        <?php else: ?>
            <span class="badge bg-warning text-dark">Menampilkan Semua Arsip</span>
        <?php endif; ?>
    </div>
    <div class="mb-3 px-3">
        <button type="button" id="bulkDeleteBtn" class="btn btn-md btn-danger me-2" style="display:none;" onclick="submitBulkDelete()">
            <i class="bi bi-trash"></i> Hapus Terpilih
        </button>
        <button type="button" id="bulkRestoreBtn" class="btn btn-md btn-info me-2" style="display:none;" onclick="submitBulkRestore()">
            <i class="bi bi-arrow-counterclockwise"></i> Kembalikan Terpilih
        </button>
        <a href="/script/export-pdf.php<?= $export_params ?>" class="btn btn-md btn-warning">Export PDF</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table align-middle table-hover m-0">
        <thead class="table-light">
            <tr>
                <th>
                    <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
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
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        <?= $arsip_info ? 'Tidak ada data untuk arsip ini' : 'Belum ada data di arsip manapun' ?>
                    </td>
                </tr>
            <?php elseif (count($data_for_display) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        Belum ada data
                    </td>
                </tr>
            <?php else: 
                foreach ($data_for_display as $row): 
                    $id = $row['id'];
                    $flow = $flows[$id] ?? ['leaving'=>0,'entering'=>0,'net'=>0,'rank'=>'-'];
            ?>
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input row-check" value="<?= $id ?>">
                    </td>
                    <td><?= $flow['rank'] ?></td>
                    <td><?= htmlspecialchars($row['nama'] ?: 'Null') ?></td>
                    <td><?= number_format($flow['leaving'], 3) ?></td>
                    <td><?= number_format($flow['entering'], 3) ?></td>
                    <td><?= htmlspecialchars($row['status_bb_u'] ?: 'Null') ?></td>
                    <td><?= number_format($flow['net'], 3) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Hidden form untuk bulk delete -->
<form id="bulkDeleteForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="bulk_delete">
    <?php if ($id_arsip_aktif): ?>
        <input type="hidden" name="id_arsip" value="<?= $id_arsip_aktif ?>">
    <?php endif; ?>
    <div id="selectedContainerDelete"></div>
</form>

<!-- Hidden form untuk bulk restore -->
<form id="bulkRestoreForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="bulk_restore">
    <?php if ($id_arsip_aktif): ?>
        <input type="hidden" name="id_arsip" value="<?= $id_arsip_aktif ?>">
    <?php endif; ?>
    <div id="selectedContainerRestore"></div>
</form>

<!-- Pagination & Info -->
<div class="d-flex justify-content-between align-items-center px-3">
    <?php
    $start = $offset + 1;
    $end = min($offset + count($data_for_display), $total_data);
    ?>
    <?php if ($total_data > 0): ?>
        <div class="px-3 pt-2">
            <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data</small>
        </div>
    <?php endif; ?>

    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-end my-3 px-3">
                <li class="page-item <?= $page_net <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= $pagination_params ?>&page_net=<?= $page_net - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page_net ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= $pagination_params ?>&page_net=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page_net >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= $pagination_params ?>&page_net=<?= $page_net + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- JS untuk Pilih Semua & Bulk Actions -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('selectAll');
  const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
  const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
  const selectedContainerDelete = document.getElementById('selectedContainerDelete');
  const selectedContainerRestore = document.getElementById('selectedContainerRestore');

  function getRowCheckboxes() {
    return Array.from(document.querySelectorAll('.row-check'));
  }

  function updateBulkButtons() {
    const checkboxes = getRowCheckboxes();
    const anyChecked = checkboxes.some(cb => cb.checked);
    bulkDeleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
    bulkRestoreBtn.style.display = anyChecked ? 'inline-block' : 'none';
  }

  // Toggle semua berdasarkan selectAll
  selectAll?.addEventListener('change', function() {
    const checkboxes = getRowCheckboxes();
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkButtons();
  });

  // Event listener untuk setiap checkbox baris
  function attachRowListeners() {
    const checkboxes = getRowCheckboxes();
    checkboxes.forEach(cb => {
      cb.removeEventListener('change', updateBulkButtons);
      cb.addEventListener('change', function() {
        if (!this.checked) selectAll.checked = false;
        const allChecked = getRowCheckboxes().every(c => c.checked);
        if (allChecked) selectAll.checked = true;
        updateBulkButtons();
      });
    });
  }

  // Initial attach
  attachRowListeners();
  updateBulkButtons();

  // Fungsi submit bulk delete
  window.submitBulkDelete = function() {
    const checkboxes = getRowCheckboxes().filter(cb => cb.checked);
    selectedContainerDelete.innerHTML = '';
    
    if (checkboxes.length === 0) return;

    checkboxes.forEach(cb => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'selected_ids[]';
      input.value = cb.value;
      selectedContainerDelete.appendChild(input);
    });

    if (!confirm(`Yakin ingin MENGHAPUS PERMANEN ${checkboxes.length} data terpilih?`)) return;
    document.getElementById('bulkDeleteForm').submit();
  };

  // Fungsi submit bulk restore
  window.submitBulkRestore = function() {
    const checkboxes = getRowCheckboxes().filter(cb => cb.checked);
    selectedContainerRestore.innerHTML = '';
    
    if (checkboxes.length === 0) return;

    checkboxes.forEach(cb => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'selected_ids[]';
      input.value = cb.value;
      selectedContainerRestore.appendChild(input);
    });

    if (!confirm(`Yakin ingin mengembalikan ${checkboxes.length} data terpilih?`)) return;
    document.getElementById('bulkRestoreForm').submit();
  };
});
</script>