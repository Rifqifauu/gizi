<?php
session_start();

if (isset($_SESSION['import_success']) || isset($_SESSION['import_fail'])) {
    $success = $_SESSION['import_success'] ?? 0;
    $fail = $_SESSION['import_fail'] ?? 0;
    $errors = $_SESSION['import_errors'] ?? [];
    
    if ($success > 0) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
        echo "<strong>✓ Berhasil!</strong> {$success} data berhasil diimport.";
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
    }
    
    if ($fail > 0) {
        echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>";
        echo "<strong>⚠ Peringatan!</strong> {$fail} data gagal diimport.<br>";
        
        if (!empty($errors)) {
            echo "<hr class='my-2'><small><strong>Detail Error:</strong><br>";
            echo "<ul class='mb-0 ps-3'>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul></small>";
        }
        
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
    }
    
    unset($_SESSION['import_success'], $_SESSION['import_fail'], $_SESSION['import_errors']);
}

include __DIR__ . '/script/SimpleXLSX.php';
include __DIR__ . '/koneksi.php'; 

function insertAlternatif($koneksi, $data) {
    $stmt = $koneksi->prepare("
        INSERT INTO alternatif 
        (nama, sex, tgl_timbang, tgl_lahir, umur, bb, tb, z_score_tb_u, z_score_bb_u, z_score_bb_tb, status_tb_u, status_bb_u, status_bb_tb, imt)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssiddssssssd",
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
        $data['imt']
    );

    return $stmt->execute();
}

function formatTanggal($hari, $bulan, $tahun) {
    // Validasi input kosong
    if (empty($hari) || empty($bulan) || empty($tahun)) {
        return null;
    }
    
    // Konversi ke integer untuk memastikan
    $hari = intval($hari);
    $bulan = intval($bulan);
    $tahun = intval($tahun);
    
    // Fix tahun 2 digit ke 4 digit
    if ($tahun < 100) {
        // Jika tahun < 50, anggap 20xx (2000-2049)
        // Jika tahun >= 50, anggap 19xx (1950-1999)
        $tahun += ($tahun < 50) ? 2000 : 1900;
    }
    
    // Validasi range bulan
    if ($bulan < 1 || $bulan > 12) {
        return null;
    }
    
    // Validasi range hari
    if ($hari < 1 || $hari > 31) {
        return null;
    }
    
    // Validasi tanggal valid menggunakan checkdate
    if (!checkdate($bulan, $hari, $tahun)) {
        return null;
    }
    
    // Format ke YYYY-MM-DD untuk MySQL
    return sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];
    $successCount = 0;
    $failCount = 0;
    $errors = [];

    if ($xlsx = SimpleXLSX::parse($file)) {
        $rows = $xlsx->rows();

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip baris kosong
            if (empty($row[1])) continue;

            // Format tanggal dengan validasi
            $tgl_timbang = formatTanggal($row[3], $row[4], $row[5]);
            $tgl_lahir   = formatTanggal($row[6], $row[7], $row[8]);

            // Validasi tanggal hasil
            if (!$tgl_timbang || !$tgl_lahir) {
                $failCount++;
                $errors[] = "Baris " . ($i + 1) . ": Format tanggal tidak valid (Timbang: {$row[3]}/{$row[4]}/{$row[5]}, Lahir: {$row[6]}/{$row[7]}/{$row[8]})";
                continue;
            }

            // Sex converter dengan validasi
            $sex = $row[2] == '1' ? 'L' : ($row[2] == '2' ? 'P' : 'Tidak Diketahui');

            // Data array
            $formData = [
                'nama' => trim($row[1]),
                'sex' => $sex,
                'tgl_timbang' => $tgl_timbang,
                'tgl_lahir' => $tgl_lahir,
                'umur' => !empty($row[9]) ? intval($row[9]) : 0,
                'bb' => !empty($row[10]) ? floatval($row[10]) : 0,
                'tb' => !empty($row[11]) ? floatval($row[11]) : 0,
                'z_score_tb_u' => !empty($row[12]) ? floatval($row[12]) : 0,
                'z_score_bb_u' => !empty($row[13]) ? floatval($row[13]) : 0,
                'z_score_bb_tb' => !empty($row[14]) ? floatval($row[14]) : 0,
                'status_tb_u' => !empty($row[15]) ? trim($row[15]) : '',
                'status_bb_u' => !empty($row[16]) ? trim($row[16]) : '',
                'status_bb_tb' => !empty($row[17]) ? trim($row[17]) : '',
                'imt' => !empty($row[18]) ? floatval($row[18]) : 0,
            ];

            try {
                if (insertAlternatif($koneksi, $formData)) {
                    $successCount++;
                } else {
                    $failCount++;
                    $errors[] = "Baris " . ($i + 1) . " ({$formData['nama']}): Gagal insert - " . $koneksi->error;
                }
            } catch (Exception $e) {
                $failCount++;
                $errors[] = "Baris " . ($i + 1) . " ({$formData['nama']}): " . $e->getMessage();
            }
        }
        
        // Set session untuk notifikasi
        $_SESSION['import_success'] = $successCount;
        $_SESSION['import_fail'] = $failCount;
        if (!empty($errors)) {
            $_SESSION['import_errors'] = $errors;
        }
        
        header("Location: views/alternatif.php");
        exit;
    } else {
        $_SESSION['import_error'] = "Gagal membaca file Excel: " . SimpleXLSX::parseError();
        header("Location: views/alternatif.php");
        exit;
    }

} else {
    header("Location: views/alternatif.php");
    exit;
}
?>