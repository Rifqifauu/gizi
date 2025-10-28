<?php
$title = 'Konversi Skala';
include '../koneksi.php';
include_once __DIR__ . '/../../../script/helpers.php';

// Pagination
$limit = 10;
$page_skala = isset($_GET['page_skala']) ? (int) $_GET['page_skala'] : 1;
if ($page_skala < 1) $page_skala = 1;
$offset = ($page_skala - 1) * $limit;

$total_alternatif = $koneksi->query("SELECT COUNT(*) AS total FROM alternatif")->fetch_assoc()['total'];
$total_page_skala = ceil($total_alternatif / $limit);

// Ambil data alternatif
$alternatif = $koneksi->query("SELECT * FROM alternatif ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Cek jika tombol proses ditekan
if (isset($_POST['proses_hitung'])) {
    // Ambil semua alternatif (bukan hanya yang di halaman ini)
    $all_alt = $koneksi->query("SELECT * FROM alternatif");
    $count = 0;
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
        $count++;
    }

    // Redirect dengan pesan sukses agar tidak repost form
    header("Location: ?tab=konversi&success=$count");
    exit;
}
?>

<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Konversi Nilai Skala</h3>

    <form method="POST" class="m-0">
        <button type="submit" name="proses_hitung" class="btn btn-light text-success fw-bold">
            <i class="bi bi-calculator"></i> Proses Hitung
        </button>
    </form>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show m-3">
        ✅ Berhasil memproses <?= htmlspecialchars($_GET['success']) ?> data alternatif.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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
<?php
if ($alternatif->num_rows === 0): ?>
    <tr>
        <td colspan="5" class="text-center text-muted py-3">Belum ada alternatif</td>
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
                    <a class="page-link" href="?tab=konversi&page_skala=<?= $page_skala - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_page_skala; $i++): ?>
                    <li class="page-item <?= $i == $page_skala ? 'active' : '' ?>">
                        <a class="page-link" href="?tab=konversi&page_skala=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page_skala >= $total_page_skala ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=konversi&page_skala=<?= $page_skala + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
