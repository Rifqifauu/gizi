<?php
$title = 'Derajat Preferensi';
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

// --- 1. Ambil semua alternatif sesuai filter arsip ---
$alternatif_result = $koneksi->query("SELECT * FROM alternatif WHERE is_archived = 1 $where_arsip ORDER BY id ASC");
$alternatif = [];
while ($row = $alternatif_result->fetch_assoc()) {
    $alternatif[$row['id']] = $row['nama'];
}

// --- 2. Ambil semua nilai (hanya untuk alternatif yang sesuai filter) ---
$alternatif_ids = array_keys($alternatif);
$nilai = [];

if (!empty($alternatif_ids)) {
    $ids_string = implode(',', $alternatif_ids);
    $nilai_result = $koneksi->query("SELECT * FROM konversi_nilai WHERE alternatif_id IN ($ids_string) ORDER BY alternatif_id ASC");
    while ($row = $nilai_result->fetch_assoc()) {
        $nilai[$row['alternatif_id']] = [
            'k1' => $row['k1'],
            'k2' => $row['k2'],
            'k3' => $row['k3'],
            'k4' => $row['k4'],
        ];
    }
}

// --- 3. Filter alternatif yang punya nilai ---
$alternatif_valid = array_intersect_key($alternatif, $nilai);
$ids = array_keys($alternatif_valid);

// --- 4. Hitung semua derajat preferensi ---
$preferensi = [];
foreach ($ids as $id1) {
    foreach ($ids as $id2) {
        if ($id1 === $id2) continue;

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

// --- 5. Simpan ke database (hanya jika ada data) ---
if (!empty($preferensi)) {
    // Hapus data lama untuk alternatif yang diproses (opsional, untuk menghindari duplikasi)
    if (!empty($ids)) {
        $ids_string = implode(',', $ids);
        $koneksi->query("DELETE FROM derajat_preferensi WHERE alternatif_1_id IN ($ids_string)");
    }
    
    // Insert data baru
    foreach ($preferensi as $row) {
        $stmt = $koneksi->prepare("INSERT INTO derajat_preferensi (alternatif_1_id, alternatif_2_id, nilai) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $row['alternatif1_id'], $row['alternatif2_id'], $row['total']);
        $stmt->execute();
    }
}

// --- 6. Pagination ---
$limit = 10;
$page_derajat = isset($_GET['page_derajat']) ? (int)$_GET['page_derajat'] : 1;
if ($page_derajat < 1) $page_derajat = 1;
$total_data = count($preferensi);
$total_pages = ceil($total_data / $limit);
$offset = ($page_derajat - 1) * $limit;
$preferensi_page = array_slice($preferensi, $offset, $limit);

// Parameter untuk pagination (membawa id_arsip)
$pagination_params = "tab=derajat";
if ($id_arsip_aktif) {
    $pagination_params .= "&id_arsip={$id_arsip_aktif}";
}
?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Derajat Preferensi</h3>
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
                    <td colspan="6" class="text-center text-muted py-3">
                        <?= $arsip_info ? 'Tidak ada data untuk arsip ini' : 'Belum ada data di arsip manapun' ?>
                    </td>
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
                    <a class="page-link" href="?<?= $pagination_params ?>&page_derajat=<?= $page_derajat - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page_derajat ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= $pagination_params ?>&page_derajat=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page_derajat >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= $pagination_params ?>&page_derajat=<?= $page_derajat + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>