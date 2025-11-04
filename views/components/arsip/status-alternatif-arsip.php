<?php
$title = 'Status Alternatif';
include '../koneksi.php';

$limit = 10;
$page_status = isset($_GET['page_status']) ? (int) $_GET['page_status'] : 1;
if ($page_status < 1) $page_status = 1;
$offset = ($page_status - 1) * $limit;

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

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM alternatif WHERE is_archived = 1 $where_arsip";
$total_data = $koneksi->query($total_query)->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data alternatif sesuai arsip yang aktif
$data_query = "SELECT * FROM alternatif WHERE is_archived = 1 $where_arsip ORDER BY id DESC LIMIT $limit OFFSET $offset";
$data = $koneksi->query($data_query);

// Parameter untuk pagination (membawa id_arsip)
$pagination_params = "tab=status";
if ($id_arsip_aktif) {
    $pagination_params .= "&id_arsip={$id_arsip_aktif}";
}
?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
  <h3 class="m-0">Status Kriteria Pada Setiap Alternatif</h3>
  <?php if ($arsip_info): ?>
    <span class="badge bg-light text-success">
      Arsip: <?= htmlspecialchars($arsip_info['nama']) ?> – <?= htmlspecialchars(date('d M Y', strtotime($arsip_info['archived_at']))) ?>
    </span>
  <?php else: ?>
    <span class="badge bg-warning text-dark">Menampilkan Semua Arsip</span>
  <?php endif; ?>
</div>

<div class="table-responsive">
  <table class="table align-middle table-hover m-0">
    <thead class="table-light">
      <tr>
        <th>Nama</th>
        <th>BB/U</th>
        <th>TB/U</th>
        <th>BB/TB</th>
        <th>IMT</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($data->num_rows === 0): ?>
        <tr>
          <td colspan="5" class="text-center text-muted py-3">
            <?= $arsip_info ? 'Tidak ada data untuk arsip ini' : 'Belum ada data di arsip manapun' ?>
          </td>
        </tr>
      <?php else: ?>
        <?php while ($row = $data->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama'] ?: 'Null') ?></td>
            <td><?= htmlspecialchars($row['status_bb_u'] ?: 'Null') ?></td>
            <td><?= htmlspecialchars($row['status_tb_u'] ?: 'Null') ?></td>
            <td><?= htmlspecialchars($row['status_bb_tb'] ?: 'Null') ?></td>
            <td><?= htmlspecialchars($row['status_imt'] ?: 'Null') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination & Info -->
<div class="d-flex justify-content-between align-items-center px-3">
  <?php
  $start = $offset + 1;
  $end = $offset + $data->num_rows;
  ?>
  <?php if ($total_data > 0): ?>
    <div class="px-3 pt-2">
      <small class="text-muted">
        Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data
      </small>
    </div>
  <?php endif; ?>

  <?php if ($total_pages > 1): ?>
    <nav>
      <ul class="pagination justify-content-end my-3 px-3">
        <li class="page-item <?= $page_status <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= $pagination_params ?>&page_status=<?= $page_status - 1 ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $i == $page_status ? 'active' : '' ?>">
            <a class="page-link" href="?<?= $pagination_params ?>&page_status=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page_status >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= $pagination_params ?>&page_status=<?= $page_status + 1 ?>">Next</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>