<?php
$title = 'Status Alternatif';
include '../koneksi.php';
$limit = 10;
$page_status = isset($_GET['page_status']) ? (int) $_GET['page_status'] : 1;
if ($page_status < 1) $page_status = 1;
$offset = ($page_status - 1) * $limit;


$total_data = $koneksi->query("SELECT COUNT(*) as total FROM alternatif")->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

$data = $koneksi->query("SELECT * FROM alternatif ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>
<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Status Kriteria Pada Setiap Alternatif</h3>
</div>
<div class="table-responsive">
    <table class="table align-middle table-hover m-0">
        <thead class="table-light">
            <tr>
                <th >Nama</th>
               <th>BB/U</th>
                <th>TB/U</th>
                <th>BB/TB</th>
                <th>IMT</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($data->num_rows === 0): ?>
                <tr>
                    <td colspan="18" class="text-center text-muted py-3">Belum ada data</td>
                </tr>
                <?php else: $no = $offset + 1;
                while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama'] ?: 'Null') ?></td>
                        <td><?= htmlspecialchars($row['status_bb_u'] ?: 'Null') ?></td>
                        <td><?= htmlspecialchars($row['status_tb_u'] ?: 'Null') ?></td>
                        <td><?= htmlspecialchars($row['status_bb_tb'] ?: 'Null') ?></td>
                        <td><?= htmlspecialchars($row['status_imt'] ?: 'Null') ?></td>
                    </tr>

            <?php endwhile;
            endif; ?>
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
            <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data</small>
        </div>
    <?php endif; ?>

    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-end my-3 px-3">
                <li class="page-item <?= $page_status <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=status&page_status=<?= $page_status - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page_status ? 'active' : '' ?>">
                        <a class="page-link" href="?tab=status&page_status=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page_ >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=status&page_status=<?= $page_status + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>