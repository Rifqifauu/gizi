<?php
include '../koneksi.php';

// === Tangkap perubahan dari GET (saat klik tab) ===
if (isset($_GET['id_arsip'])) {
    $_SESSION['id_arsip'] = $_GET['id_arsip'] ?: null;
}

// === Tangkap perubahan dropdown (jika user memilih arsip) ===
if (isset($_POST['id_arsip'])) {
    $_SESSION['id_arsip'] = $_POST['id_arsip'] ?: null;
    // Ambil tab dari POST
    $redirect_tab = $_POST['tab'] ?? 'status';
    $arsip_param = $_SESSION['id_arsip'] ? $_SESSION['id_arsip'] : '';
    header("Location: ?tab=$redirect_tab&id_arsip=$arsip_param");
    exit;
}

// Ambil arsip yang dipilih, default = null
$id_arsip_aktif = $_SESSION['id_arsip'] ?? null;

$title = 'Arsip Cek Gizi';
ob_start();
?>

<div class="container">
  <div class="card">

    <!-- Header dengan Tabs + Dropdown -->
    <div class="card-header d-flex justify-content-between flex-wrap p-4 bg-primary text-white">
      <?php
      $activeTab = $_GET['tab'] ?? 'status';
      // Parameter id_arsip untuk dibawa ke tab lain
      $arsip_param = $id_arsip_aktif ? "&id_arsip={$id_arsip_aktif}" : '';
      ?>

      <!-- Tabs Navigasi -->
      <ul class="nav nav-tabs card-header-tabs bg-primary p-2">
        <li class="nav-item">
            <a href="?tab=status<?= $arsip_param ?>" class="nav-link <?= $activeTab==='status'?'active':'' ?>">Status Arsip</a>
        </li>
        <li class="nav-item">
            <a href="?tab=konversi<?= $arsip_param ?>" class="nav-link <?= $activeTab==='konversi'?'active':'' ?>">Konversi Arsip</a>
        </li>
        <li class="nav-item">
            <a href="?tab=derajat<?= $arsip_param ?>" class="nav-link <?= $activeTab==='derajat'?'active':'' ?>">Derajat Arsip</a>
        </li>
        <li class="nav-item">
            <a href="?tab=hasil-akhir<?= $arsip_param ?>" class="nav-link <?= $activeTab==='hasil-akhir'?'active':'' ?>">Hasil Akhir Arsip</a>
        </li>
      </ul>

      <!-- Dropdown Pilih Arsip -->
      <form method="POST" class="d-flex align-items-center gap-2 bg-light rounded p-2 mt-3 mt-md-0">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
        <label for="id_arsip" class="fw-semibold text-dark mb-0">Pilih Arsip:</label>
        <select name="id_arsip" id="id_arsip" class="form-select w-auto" onchange="this.form.submit()">
          <option value="">-- Semua Arsip --</option>
          <?php
          $arsip_result = $koneksi->query("SELECT id, nama, archived_at FROM arsip ORDER BY id DESC");
          while ($arsip = $arsip_result->fetch_assoc()):
          ?>
            <option value="<?= $arsip['id'] ?>" <?= ($id_arsip_aktif == $arsip['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($arsip['nama']) ?> (<?= date('d M Y', strtotime($arsip['archived_at'])) ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </form>
    </div>

    <!-- Konten Dinamis Tiap Tab -->
    <div class="card-body">
      <?php
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