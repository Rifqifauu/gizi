<?php
$title = 'Arsip Cek Gizi';
ob_start();
?>
<div class="container">
  <div class="card">
    <div class="card-header p-4">
      <?php
      $activeTab = $_GET['tab'] ?? 'status';
      ?>
      <ul class="nav nav-tabs card-header-tabs bg-primary p-2" role="tablist">
          <li class="nav-item">
              <a href="?tab=status" class="nav-link <?= $activeTab==='status'?'active':'' ?>">Status Arsip</a>
          </li>
          <li class="nav-item">
              <a href="?tab=konversi" class="nav-link <?= $activeTab==='konversi'?'active':'' ?>">Konversi Arsip</a>
          </li>
          <li class="nav-item">
              <a href="?tab=derajat" class="nav-link <?= $activeTab==='derajat'?'active':'' ?>">Derajat Arsip</a>
          </li>
          <li class="nav-item">
              <a href="?tab=hasil-akhir" class="nav-link <?= $activeTab==='hasil-akhir'?'active':'' ?>">Hasil Akhir Arsip</a>
          </li>
      </ul>
    </div>

    <div class="card-body">
      <?php
      // Include file versi arsip
      switch ($activeTab) {
          case 'status':
              include __DIR__ . '/components/arsip/status-alternatif-arsip.php';
              break;
          case 'konversi':
              include __DIR__ . '/components/arsip/konversi-skala-arsip.php';
              break;
          case 'derajat':
              include __DIR__ . '/components/arsip/derajat-preferensi-arsip.php';
              break;
          case 'hasil-akhir':
              include __DIR__ . '/components/arsip/hasil-akhir-arsip.php';
              break;
          default:
              include __DIR__ . '/components/arsip/status-alternatif-arsip.php';
      }
      ?>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
