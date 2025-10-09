<?php
$title = 'Beranda';

// Mulai "menangkap" konten halaman
ob_start();
?>
<div class="container">

<h1>Ini adalah dashboard</h1>
</div>
<?php
// Ambil hasil output ke variabel $content
$content = ob_get_clean();

// Panggil layout utama
include __DIR__ . '/layout.php';

