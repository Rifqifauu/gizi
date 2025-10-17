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
        <a href="?tab=matriks" class="nav-link <?= $activeTab==='matriks'?'active':'' ?>">Matriks</a>
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
      <div class="tab-content">
    <div class="tab-pane fade <?= $activeTab==='status'?'show active':'' ?>" id="status">
        <?php include __DIR__ . '/components/cek-gizi/status-alternatif.php'; ?>
    </div>
    <div class="tab-pane fade <?= $activeTab==='konversi'?'show active':'' ?>" id="konversi">
        <?php include __DIR__ . '/components/cek-gizi/konversi-skala.php'; ?>
    </div>
    <div class="tab-pane fade <?= $activeTab==='matriks'?'show active':'' ?>" id="matriks">
        <?php include __DIR__ . '/components/cek-gizi/matriks-perbandingan.php'; ?>
    </div>
    <div class="tab-pane fade <?= $activeTab==='derajat'?'show active':'' ?>" id="derajat">
        <?php include __DIR__ . '/components/cek-gizi/derajat-preferensi.php'; ?>
    </div>
    <div class="tab-pane fade <?= $activeTab==='hasil-akhir'?'show active':'' ?>" id="derajat">
        <?php include __DIR__ . '/components/cek-gizi/hasil-akhir.php'; ?>
    </div>
</div>

    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
