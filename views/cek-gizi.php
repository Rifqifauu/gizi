<?php
$title = 'Cek Gizi';
ob_start();
?>
<div class="container">
  <div class="card">
    <div class="card-header p-4">
      <?php
      $activeTab = $_GET['tab'] ?? 'status';
      ?>
      <ul class="nav nav-tabs card-header-tabs bg-success p-2" role="tablist">
          <li class="nav-item">
              <a href="?tab=status" class="nav-link <?= $activeTab==='status'?'active':'' ?>">Status</a>
          </li>
          <li class="nav-item">
              <a href="?tab=konversi" class="nav-link <?= $activeTab==='konversi'?'active':'' ?>">Konversi</a>
          </li>
          <li class="nav-item">
              <a href="?tab=derajat" class="nav-link <?= $activeTab==='derajat'?'active':'' ?>">Derajat Preferensi</a>
          </li>
          <li class="nav-item">
              <a href="?tab=hasil-akhir" class="nav-link <?= $activeTab==='hasil-akhir'?'active':'' ?>">Hasil Akhir</a>
          </li>
      </ul>
    </div>

    <div class="card-body">
      <?php
      // Include HANYA file yang aktif
      switch ($activeTab) {
          case 'status':
              include __DIR__ . '/components/cek-gizi/status-alternatif.php';
              break;
          case 'konversi':
              include __DIR__ . '/components/cek-gizi/konversi-skala.php';
              break;
          case 'derajat':
              include __DIR__ . '/components/cek-gizi/derajat-preferensi.php';
              break;
          case 'hasil-akhir':
              include __DIR__ . '/components/cek-gizi/hasil-akhir.php';
              break;
          default:
              include __DIR__ . '/components/cek-gizi/status-alternatif.php';
      }
      ?>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>