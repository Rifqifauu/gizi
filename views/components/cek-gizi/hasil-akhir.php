

<?php
$title = 'Net Flow';
include '../koneksi.php';

$limit = 10;
$page_net = isset($_GET['page_net']) ? (int)$_GET['page_net'] : 1;
if ($page_net < 1) $page_net = 1;
$offset = ($page_net - 1) * $limit;

// Ambil total alternatif
$total_result = $koneksi->query("SELECT COUNT(*) as total FROM alternatif");
if (!$total_result) {
    die("ERROR Query total: " . $koneksi->error);
}
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil semua alternatif untuk perhitungan flow
$all_alternatif_result = $koneksi->query("SELECT id FROM alternatif");
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
    $result = $koneksi->query("SELECT id, nama,status_bb_u FROM alternatif WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $data_for_display[] = $result->fetch_assoc();
    }
}
?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Net Flow (Φ) & Ranking</h3>
    <div class="mb-3 px-3">
    <a href="/script/export-pdf.php" class="btn btn-md btn-danger">Export PDF</a>
</div>
</div>



<div class="table-responsive">
    <table class="table align-middle table-hover m-0">
        <thead class="table-light">
            <tr>
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
                    <td colspan="5" class="text-center text-danger py-3">
                        <strong>⚠️ Tidak ada data di tabel alternatif</strong>
                    </td>
                </tr>
            <?php elseif (count($data_for_display) === 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        Belum ada data
                    </td>
                </tr>
            <?php else: 
                foreach ($data_for_display as $row): 
                    $id = $row['id'];
                    $flow = $flows[$id] ?? ['leaving'=>0,'entering'=>0,'net'=>0,'rank'=>'-'];
            ?>
                <tr>
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

<!-- Pagination & Info -->
<div class="d-flex justify-content-between align-items-center px-3">
    <?php
    $start = $offset + 1;
$end = min($offset + count($data_for_display), $total_data);    ?>
    <?php if ($total_data > 0): ?>
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