<?php
require_once('../koneksi.php');
require_once('../dompdf/autoload.inc.php'); 

use Dompdf\Dompdf;
use Dompdf\Options;

$title = "Hasil Perhitungan";

// Ambil parameter id_arsip dari URL (jika ada)
$id_arsip = isset($_GET['id_arsip']) ? (int)$_GET['id_arsip'] : null;
$arsip_info = null;

// Jika ada id_arsip, ambil info arsipnya
if ($id_arsip) {
    $stmt = $koneksi->prepare("SELECT nama, archived_at FROM arsip WHERE id = ?");
    $stmt->bind_param("i", $id_arsip);
    $stmt->execute();
    $arsip_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($arsip_info) {
        $title = "Hasil Perhitungan - Arsip: " . $arsip_info['nama'];
    }
}

// Filter berdasarkan arsip (jika ada)
$where_arsip = "";
if ($id_arsip) {
    $where_arsip = "WHERE is_archived = 1 AND id_arsip = $id_arsip";
} else {
    // Jika tidak ada id_arsip, tampilkan semua data yang diarsipkan
    $where_arsip = "WHERE is_archived = 1";
}

// Ambil semua alternatif sesuai filter
$all_alternatif_result = $koneksi->query("SELECT id, nama, status_bb_u FROM alternatif $where_arsip");
if (!$all_alternatif_result) {
    die("ERROR Query alternatif: " . $koneksi->error);
}

$all_alternatif = [];
while ($row = $all_alternatif_result->fetch_assoc()) {
    $all_alternatif[$row['id']] = $row;
}
$total_alternatif = count($all_alternatif);

// Jika tidak ada data
if ($total_alternatif === 0) {
    $message = $id_arsip && $arsip_info 
        ? "Tidak ada data alternatif untuk arsip: " . htmlspecialchars($arsip_info['nama'])
        : "Tidak ada data alternatif untuk diekspor.";
    die($message);
}

// Hitung flow untuk tiap alternatif
$flows = [];
foreach ($all_alternatif as $id => $alt) {
    // Leaving Flow
    $res1 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_1_id = $id");
    $leaving = ($res1 && $row1 = $res1->fetch_assoc()) 
        ? ($row1['total'] ?? 0) / max(1, $total_alternatif - 1)
        : 0;

    // Entering Flow
    $res2 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_2_id = $id");
    $entering = ($res2 && $row2 = $res2->fetch_assoc()) 
        ? ($row2['total'] ?? 0) / max(1, $total_alternatif - 1)
        : 0;

    $net = $leaving - $entering;

    $flows[$id] = [
        'nama' => $alt['nama'],
        'status' => $alt['status_bb_u'],
        'leaving' => $leaving,
        'entering' => $entering,
        'net' => $net
    ];
}

// Urutkan berdasarkan Net Flow DESC
uasort($flows, function($a, $b) {
    return $b['net'] <=> $a['net'];
});

// Tambahkan ranking
$rank = 1;
foreach ($flows as &$f) {
    $f['rank'] = $rank++;
}
unset($f);

// Info tambahan untuk header PDF
$info_arsip = '';
if ($arsip_info) {
    $tanggal = date('d F Y', strtotime($arsip_info['archived_at']));
    $info_arsip = '<p style="text-align: center; margin: 5px 0; font-size: 11px; color: #555;">
        Arsip: <strong>' . htmlspecialchars($arsip_info['nama']) . '</strong> | 
        Tanggal: <strong>' . $tanggal . '</strong>
    </p>';
}

// Buat HTML untuk PDF
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
h2 { text-align: center; margin-bottom: 5px; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { border: 1px solid #555; padding: 6px; text-align: center; }
th { background-color: #f2f2f2; }
.footer { margin-top: 20px; font-size: 10px; text-align: right; color: #777; }
</style>

<h2>' . htmlspecialchars($title) . '</h2>
' . $info_arsip . '
<table>
    <thead>
        <tr>
            <th>Ranking</th>
            <th>Nama Alternatif</th>
            <th>Leaving Flow</th>
            <th>Entering Flow</th>
            <th>Status Gizi</th>
            <th>Net Flow</th>
        </tr>
    </thead>
    <tbody>';

foreach ($flows as $id => $f) {
    $html .= '<tr>
        <td>' . $f['rank'] . '</td>
        <td>' . htmlspecialchars($f['nama'] ?? '-') . '</td>
        <td>' . number_format($f['leaving'], 3) . '</td>
        <td>' . number_format($f['entering'], 3) . '</td>
        <td>' . htmlspecialchars($f['status'] ?? '-') . '</td>
        <td>' . number_format($f['net'], 3) . '</td>
    </tr>';
}

$html .= '
    </tbody>
</table>
<div class="footer">
    Dicetak pada: ' . date('d F Y, H:i') . ' WIB
</div>';

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nama file PDF
$filename = 'Laporan_Hasil_Perhitungan';
if ($arsip_info) {
    $filename .= '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $arsip_info['nama']);
}
$filename .= '.pdf';

// Output PDF ke browser
$dompdf->stream($filename, ['Attachment' => 0]);
exit;
?>