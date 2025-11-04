<?php
$title = 'Konversi Skala';
include '../koneksi.php';
include_once __DIR__ . '/../../../script/helpers.php';

// Pagination
$limit = 10;
$page_skala = isset($_GET['page_skala']) ? (int) $_GET['page_skala'] : 1;
if ($page_skala < 1) $page_skala = 1;
$offset = ($page_skala - 1) * $limit;

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
$total_alternatif = $koneksi->query($total_query)->fetch_assoc()['total'];
$total_page_skala = ceil($total_alternatif / $limit);

// PROSES HITUNG OTOMATIS (tanpa tombol)
// Ambil semua alternatif sesuai filter arsip
$all_alt = $koneksi->query("SELECT * FROM alternatif WHERE is_archived = 1 $where_arsip");
while ($row = $all_alt->fetch_assoc()) {
    $id_alt = $row['id'];
    $k1 = getNilaiSkala($koneksi, $row['status_tb_u']);
    $k2 = getNilaiSkala($koneksi, $row['status_bb_u']);
    $k3 = getNilaiSkala($koneksi, $row['status_bb_tb']);
    $k4 = getNilaiSkala($koneksi, $row['status_imt']);

    $koneksi->query("
        INSERT INTO konversi_nilai (alternatif_id, k1, k2, k3, k4)
        VALUES ($id_alt, '$k1', '$k2', '$k3', '$k4')
        ON DUPLICATE KEY UPDATE
            k1='$k1', k2='$k2', k3='$k3', k4='$k4'
    ");
}

// Ambil data alternatif untuk ditampilkan (dengan pagination)
$data_query = "SELECT * FROM alternatif WHERE is_archived = 1 $where_arsip ORDER BY id DESC LIMIT $limit OFFSET $offset";
$alternatif = $koneksi->query($data_query);

// Parameter untuk pagination (membawa id_arsip)
$pagination_params = "tab=konversi";
if ($id_arsip_aktif) {
    $pagination_params .= "&id_arsip={$id_arsip_aktif}";
}
?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Konversi Nilai Skala</h3>
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
            <?php if ($alternatif->num_rows === 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        <?= $arsip_info ? 'Tidak ada data untuk arsip ini' : 'Belum ada data di arsip manapun' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php while ($row = $alternatif->fetch_assoc()): ?>
                    <?php
                        $id_alt = $row['id'];
                        $konv = $koneksi->query("SELECT * FROM konversi_nilai WHERE alternatif_id = $id_alt")->fetch_assoc();
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama'] ?: 'Null') ?></td>
                        <td><?= htmlspecialchars($konv['k2'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($konv['k1'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($konv['k3'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($konv['k4'] ?? '-') ?></td>
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
    $end = $offset + ($alternatif->num_rows ?? 0);
    ?>
    <?php if ($total_alternatif > 0): ?>
        <div class="px-3 pt-2">
            <small class="text-muted">Menampilkan <?= $start ?>–<?= $end ?> dari <?= $total_alternatif ?> alternatif</small>
        </div>
    <?php endif; ?>

    <?php if ($total_page_skala > 1): ?>
        <nav>
            <ul class="pagination justify-content-end my-3 px-3">
                <li class="page-item <?= $page_skala <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= $pagination_params ?>&page_skala=<?= $page_skala - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_page_skala; $i++): ?>
                    <li class="page-item <?= $i == $page_skala ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= $pagination_params ?>&page_skala=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page_skala >= $total_page_skala ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= $pagination_params ?>&page_skala=<?= $page_skala + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>