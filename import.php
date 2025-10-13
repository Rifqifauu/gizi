<?php
session_start();

include __DIR__ . '/script/SimpleXLSX.php';
include __DIR__ . '/koneksi.php';

// --- Fungsi untuk menghitung Z-Score IMT dari WHO reference ---
function getZScoreIMT($koneksi, $sex, $umur_bulan, $imt) {
    if (empty($sex) || empty($umur_bulan) || $imt === null || $imt === 0) {
        return null;
    }
    
    $stmt = $koneksi->prepare("
        SELECT median, sd 
        FROM who_imt_ref 
        WHERE sex = ? AND umur_bln = ? 
        LIMIT 1
    ");
    
    if (!$stmt) {
        echo "[ERROR] Prepare query: " . $koneksi->error . "<br>";
        return null;
    }
    
    $stmt->bind_param("si", $sex, $umur_bulan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $median = $row['median'];
        $sd = $row['sd'];
        
        if ($sd != 0) {
            $z = ($imt - $median) / $sd;
            return round($z, 2);
        }
    }

    return null;
}

// --- Fungsi untuk menentukan status gizi dari z-score dan kriteria ---
function getStatus($koneksi, $id_kriteria, $nilai) {
    if ($nilai === null) {
        return 'Tidak Diketahui';
    }
    
    $stmt = $koneksi->prepare("
        SELECT nama FROM sub_kriteria 
        WHERE id_kriteria=? 
        AND (
            (batas_bawah IS NULL AND batas_atas IS NULL) 
            OR (batas_bawah IS NULL AND ? <= batas_atas) 
            OR (batas_atas IS NULL AND ? >= batas_bawah)
            OR (batas_bawah IS NOT NULL AND batas_atas IS NOT NULL AND ? BETWEEN batas_bawah AND batas_atas)
        )
        LIMIT 1
    ");
    $stmt->bind_param("iddd", $id_kriteria, $nilai, $nilai, $nilai);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['nama'];
    }
    return 'Tidak Diketahui';
}

// --- Fungsi insert alternatif ---
function insertAlternatif($koneksi, $data) {
    $stmt = $koneksi->prepare("
        INSERT INTO alternatif 
        (nama, sex, tgl_timbang, tgl_lahir, umur, bb, tb, z_score_tb_u, z_score_bb_u, z_score_bb_tb, status_tb_u, status_bb_u, status_bb_tb, imt, z_score_imt, status_imt)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Format: s=string, i=integer, d=double
    $stmt->bind_param(
        "ssssidddssssddds",
        $data['nama'],
        $data['sex'],
        $data['tgl_timbang'],
        $data['tgl_lahir'],
        $data['umur'],
        $data['bb'],
        $data['tb'],
        $data['z_score_tb_u'],
        $data['z_score_bb_u'],
        $data['z_score_bb_tb'],
        $data['status_tb_u'],
        $data['status_bb_u'],
        $data['status_bb_tb'],
        $data['imt'],
        $data['z_score_imt'],
        $data['status_imt']
    );

    return $stmt->execute();
}

// --- Fungsi validasi dan format tanggal dari Excel ---
function formatTanggal($hari, $bulan, $tahun) {
    if (empty($hari) || empty($bulan) || empty($tahun)) return null;

    $hari = intval($hari);
    $bulan = intval($bulan);
    $tahun = intval($tahun);
    
    if ($tahun < 100) $tahun += ($tahun < 50) ? 2000 : 1900;
    if ($bulan < 1 || $bulan > 12) return null;
    if ($hari < 1 || $hari > 31) return null;
    if (!checkdate($bulan, $hari, $tahun)) return null;

    return sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);
}

// --- Proses import Excel ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];
    $successCount = 0;
    $failCount = 0;
    $errors = [];

    if ($xlsx = SimpleXLSX::parse($file)) {
        $rows = $xlsx->rows();

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[1])) continue; // skip baris kosong

            // --- Format tanggal ---
            $tgl_timbang = formatTanggal($row[3], $row[4], $row[5]);
            $tgl_lahir   = formatTanggal($row[6], $row[7], $row[8]);

            if (!$tgl_timbang || !$tgl_lahir) {
                $failCount++;
                $errors[] = "Baris ".($i+1).": Format tanggal tidak valid";
                continue;
            }

            // --- Sex (convert 1=L, 2=P) ---
            $sex = $row[2] == '1' ? 'L' : ($row[2] == '2' ? 'P' : 'Tidak Diketahui');

            // --- Data dasar ---
            $bb = !empty($row[10]) ? floatval($row[10]) : 0;
            $tb = !empty($row[11]) ? floatval($row[11]) : 0;
            $umur = !empty($row[9]) ? intval($row[9]) : 0;

            // --- Z-Score dari Excel ---
            $z_tb_u = !empty($row[12]) ? floatval($row[12]) : 0;
            $z_bb_u = !empty($row[13]) ? floatval($row[13]) : 0;
            $z_bb_tb = !empty($row[14]) ? floatval($row[14]) : 0;

            // --- Hitung IMT ---
            $imt = $tb > 0 ? $bb / (($tb/100)**2) : 0;

            // --- HITUNG Z-Score IMT dari WHO reference ---
            $z_score_imt = getZScoreIMT($koneksi, $sex, $umur, $imt);

            // --- Status otomatis berdasarkan sub_kriteria ---
            $status_bb_u = getStatus($koneksi, 1, $z_bb_u);      // K1 (BB/U)
            $status_tb_u = getStatus($koneksi, 2, $z_tb_u);      // K2 (TB/U)
            $status_bb_tb = getStatus($koneksi, 3, $z_bb_tb);    // K3 (BB/TB)
            $status_imt = getStatus($koneksi, 4, $z_score_imt);  // K4 (IMT) - dari Z-Score IMT!

            // --- Prepare data ---
            $formData = [
                'nama' => trim($row[1]),
                'sex' => $sex,
                'tgl_timbang' => $tgl_timbang,
                'tgl_lahir' => $tgl_lahir,
                'umur' => $umur,
                'bb' => $bb,
                'tb' => $tb,
                'z_score_tb_u' => $z_tb_u,
                'z_score_bb_u' => $z_bb_u,
                'z_score_bb_tb' => $z_bb_tb,
                'status_tb_u' => $status_tb_u,
                'status_bb_u' => $status_bb_u,
                'status_bb_tb' => $status_bb_tb,
                'imt' => $imt,
                'z_score_imt' => $z_score_imt,
                'status_imt' => $status_imt
            ];

            // --- Insert ke DB ---
            if (insertAlternatif($koneksi, $formData)) {
                $successCount++;
            } else {
                $failCount++;
                $errors[] = "Baris ".($i+1)." ({$formData['nama']}): ".$koneksi->error;
            }
        }

        $_SESSION['import_success'] = $successCount;
        $_SESSION['import_fail'] = $failCount;
        if (!empty($errors)) $_SESSION['import_errors'] = $errors;

        header("Location: views/alternatif.php");
        exit;

    } else {
        $_SESSION['import_fail'] = 0;
        $_SESSION['import_errors'] = ["Gagal membaca file Excel: " . SimpleXLSX::parseError()];
        header("Location: views/alternatif.php");
        exit;
    }

} else {
    header("Location: views/alternatif.php");
    exit;
}
?>