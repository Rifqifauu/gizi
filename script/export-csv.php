<?php

include '../koneksi.php';

// --- Bersihkan buffer ---
if (ob_get_length()) ob_end_clean();

// --- Ambil semua alternatif ---
$all_alternatif_result = $koneksi->query("SELECT id, nama FROM alternatif");
if (!$all_alternatif_result) die("ERROR: ".$koneksi->error);

$all_alternatif = [];
while ($row = $all_alternatif_result->fetch_assoc()) {
    $all_alternatif[$row['id']] = $row['nama'];
}

// --- Hitung leaving, entering, net flow ---
$flows = [];
$total_alt = count($all_alternatif);
foreach ($all_alternatif as $id => $nama) {
    $res1 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_1_id = $id");
    $row1 = $res1 ? $res1->fetch_assoc() : null;
    $leaving = ($row1['total'] ?? 0) / ($total_alt > 1 ? $total_alt - 1 : 1);

    $res2 = $koneksi->query("SELECT SUM(nilai) as total FROM derajat_preferensi WHERE alternatif_2_id = $id");
    $row2 = $res2 ? $res2->fetch_assoc() : null;
    $entering = ($row2['total'] ?? 0) / ($total_alt > 1 ? $total_alt - 1 : 1);

    $flows[$id] = [
        'nama' => $nama,
        'leaving' => $leaving,
        'entering' => $entering,
        'net' => $leaving - $entering,
    ];
}

// --- Ranking ---
uasort($flows, fn($a,$b)=>$b['net']<=>$a['net']);
$rank = 1;
foreach ($flows as &$f) { $f['rank'] = $rank++; } unset($f);

// --- Output CSV ---
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="net_flow.csv"');

if (ob_get_length()) ob_end_clean();
$output = fopen('php://output','w');
echo "\xEF\xBB\xBF"; // BOM UTF-8

fputcsv($output, ['Ranking','Nama Alternatif','Leaving Flow (Φ⁺)','Entering Flow (Φ⁻)','Net Flow (Φ)'], ',', '"', "\\");

foreach ($flows as $f) {
   fputcsv($output, [
    $f['rank'],
    $f['nama'],
    number_format($f['leaving'],3),
    number_format($f['entering'],3),
    number_format($f['net'],3),
], ',', '"', "\\");

}

fclose($output);
exit;
header('Location: ../views/cek-gizi.php');
