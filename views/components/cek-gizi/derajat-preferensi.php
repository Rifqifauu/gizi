<?php
$title = 'Derajat Preferensi';
include '../koneksi.php';

// --- 1. Ambil semua alternatif ---
$alternatif_result = $koneksi->query("SELECT * FROM alternatif ORDER BY id ASC");
$alternatif = [];
while ($row = $alternatif_result->fetch_assoc()) {
    $alternatif[$row['id']] = $row['nama'];
}

// --- 2. Ambil semua nilai ---
$nilai_result = $koneksi->query("SELECT * FROM konversi_nilai ORDER BY alternatif_id ASC");
$nilai = [];
while ($row = $nilai_result->fetch_assoc()) {
    $nilai[$row['alternatif_id']] = [
        'k1' => $row['k1'],
        'k2' => $row['k2'],
        'k3' => $row['k3'],
        'k4' => $row['k4'],
    ];
}

// --- 3. Filter alternatif yang punya nilai ---
$alternatif_valid = array_intersect_key($alternatif, $nilai);
$ids = array_keys($alternatif_valid);

// --- 4. Hitung semua pasangan unik ---
$preferensi = [];
foreach ($ids as $i => $id1) {
    for ($j = $i + 1; $j < count($ids); $j++) {
        $id2 = $ids[$j];
        $p1 = $nilai[$id1];
        $p2 = $nilai[$id2];

        $k1 = max(0, $p1['k1'] - $p2['k1']);
        $k2 = max(0, $p1['k2'] - $p2['k2']);
        $k3 = max(0, $p1['k3'] - $p2['k3']);
        $k4 = max(0, $p1['k4'] - $p2['k4']);
        $total = ($k1 + $k2 + $k3 + $k4) / 4;

        $preferensi[] = [
            'alternatif1_id' => $id1,
            'alternatif2_id' => $id2,
            'alternatif1' => $alternatif[$id1],
            'alternatif2' => $alternatif[$id2],
            'k1' => $k1,
            'k2' => $k2,
            'k3' => $k3,
            'k4' => $k4,
            'total' => $total,
        ];
    }
}

// --- 5. Simpan ke database ---
foreach ($preferensi as $row) {
    $stmt = $koneksi->prepare("INSERT INTO derajat_preferensi (alternatif_1_id, alternatif_2_id, nilai) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $row['alternatif1_id'], $row['alternatif2_id'], $row['total']);
    $stmt->execute();
}

// --- 6. Pagination ---
$limit = 10; // baris per halaman
$page_derajat = isset($_GET['page_derajat']) ? (int)$_GET['page_derajat'] : 1;
if ($page_derajat < 1) $page_derajat = 1;
$total_data = count($preferensi);
$total_pages = ceil($total_data / $limit);
$offset = ($page_derajat - 1) * $limit;
$preferensi_page = array_slice($preferensi, $offset, $limit);
?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Derajat Preferensi</h3>
</div>
<div class="table-responsive">
    <table class="table align-middle table-hover m-0">
        <thead class="table-light">
            <tr>
                <th>Alternatif 1 | Alternatif 2</th>
                <th>K1</th>
                <th>K2</th>
                <th>K3</th>
                <th>K4</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($preferensi_page)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Belum ada data</td>
                </tr>
            <?php else: ?>
                <?php foreach ($preferensi_page as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['alternatif1'] . ' | ' . $row['alternatif2']) ?></td>
                        <td><?= number_format($row['k1'], 2) ?></td>
                        <td><?= number_format($row['k2'], 2) ?></td>
                        <td><?= number_format($row['k3'], 2) ?></td>
                        <td><?= number_format($row['k4'], 2) ?></td>
                        <td><?= number_format($row['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center px-3">
    <?php
    $start = $offset + 1;
    $end = min($offset + count($preferensi_page), $total_data);
    ?>
    <?php if ($total_data > 0): ?>
        <div class="px-3 pt-2">
            <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_data ?> data</small>
        </div>
    <?php endif; ?>

    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-end my-3 px-3">
                <li class="page-item <?= $page_derajat <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=derajat&page_derajat=<?= $page_derajat - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page_derajat ? 'active' : '' ?>">
                        <a class="page-link" href="?tab=derajat&page_derajat=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page_derajat >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=derajat&page_derajat=<?= $page_derajat + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
