<?php
$currentPage = basename($_SERVER['PHP_SELF']);

?>
<html>
<aside class="navbar navbar-vertical navbar-expand-lg sidebar">
  <div class="container-fluid px-0 justify-content-start">
    <div class="bg-sidebar-header">
      <h1 class="navbar-brand text-white h-full gap-3 p-4 align-items-center">
        <a href="dashboard.php" class="fw-bold text-decoration-none text-success" style="font-size:.9rem;">SPK - PROMETHE</a>
      </h1>
    </div>
    <div class="offcanvas offcanvas-start px-lg-3" id="sidebar-menu">
      <div class="offcanvas-header">
        <div class="d-flex gap-3 align-items-center">
          <div class="logo-text flex-grow-1">
            <h3 class="m-0"></h3>
            <div class="fs-4 fw-bold"></div>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body p-3 p-lg-0 flex-column flex-grow-1 overflow-auto">
        <ul class="navbar-nav align-items-start mt-lg-3 w-100">

          <!-- === DASHBOARD SECTION === -->
          <li class="nav-item w-100">
            <div class="text-uppercase small fw-bold text-white px-3 mt-3 mb-1">Dashboard</div>
            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'index.php') ? 'active' : '' ?>" href="index.php">
              <i class="bi bi-house-fill me-2"></i><span>Beranda</span>
            </a>
          </li>

          <!-- === MASTER DATA SECTION === -->
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

          <li class="nav-item w-100 mt-3">
            <div class="text-uppercase small fw-bold text-white px-3 mb-1">Master Data</div>

            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'kriteria.php') ? 'active' : '' ?>" href="kriteria.php">
              <i class="bi bi-list-check me-2"></i><span>Data Kriteria</span>
            </a>

            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'alternatif.php') ? 'active' : '' ?>" href="alternatif.php">
              <i class="bi bi-people-fill me-2"></i><span>Data Balita</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- === Cek Gizi SECTION === -->
          <li class="nav-item w-100 mt-3">
            <div class="text-uppercase small fw-bold text-white px-3 mb-1">Penilaian</div>

            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'cek-gizi.php') ? 'active' : '' ?>" href="cek-gizi.php">
              <i class="bi bi-pencil-square me-2"></i><span class="">Perhitungan dan Analisis</span>
            </a>
          </li>
          <!-- === Arsip Gizi SECTION === -->
          <li class="nav-item w-100 mt-3">
            <div class="text-uppercase small fw-bold text-white px-3 mb-1">Arsip</div>

            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'arsip.php') ? 'active' : '' ?>" href="arsip.php">
              <i class="bi bi-pencil-square me-2"></i><span class="">Arsip Perhitungan</span>
            </a>
          </li>

          <!-- === USER SECTION === -->
          <li class="nav-item w-100 mt-3">
            <div class="text-uppercase small fw-bold text-white px-3 mb-1">User</div>
            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'user.php') ? 'active' : '' ?>" href="user.php">
              <i class="bi bi-person-fill me-2"></i><span>Data User</span>
            </a>
          </li>

        </ul>
        <div onclick="logout()" class="mt-auto mb-3 px-3 btn d-flex justify-content-start gap-3 text-white btn-logout w-100">
          <i class="bi bi-door-open"></i>
          <span class="text-white">Logout</span>
        </div>
      </div>
    </div>
  </div>
</aside>

<script>
  function logout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
      window.location.href = '/logout.php';
    }
  }
</script>

<style>
  .sidebar {
    background: linear-gradient(135deg, #1eae6bff, #067c4dff);
    min-height: 100vh;
  }

  .bg-sidebar-header {
    background-color: #ffffffff;
  }

  .nav-link {
    color: #ffffff;
    padding: 0.6rem 1rem;
    border-radius: 0.5rem;
    transition: background-color 0.3s, color 0.3s;
    font-size: 0.95rem;
  }

  .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.15);
    color: #ffffff;
  }

  .nav-link.active {
    background-color: #ffffff;
    color: #259753 !important;
    font-weight: bold;
  }

  .btn-logout {
    background-color: #dc3545;
    border: none;
    transition: background-color 0.3s ease;
  }

  .btn-logout:hover {
    background-color: #c82333;
    color: #ffffff !important;
  }
</style>