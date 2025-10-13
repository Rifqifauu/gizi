<?php
if (!function_exists('getNilaiSkala')) {
    function getNilaiSkala($koneksi, $namaSub) {
        if (!$namaSub) return 0;
        $namaSub = $koneksi->real_escape_string($namaSub);
        $result = $koneksi->query("SELECT nilai_skala FROM sub_kriteria WHERE nama = '$namaSub' LIMIT 1");
        if ($result && $row = $result->fetch_assoc()) {
            return (float)$row['nilai_skala'];
        }
        return 0;
    }
}
