<?php
$koneksi = new mysqli('127.0.0.1', 'root', 'root', 'gizi', 8889);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
