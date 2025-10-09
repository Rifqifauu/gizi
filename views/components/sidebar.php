<?php
$currentPage = basename($_SERVER['PHP_SELF']);

?>


<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark sidebar" data-bs-theme="dark">
  <div class="container-fluid px-0 justify-content-start">
    <h1 class="navbar-brand text-white bg-primary h-full gap-3 d-flex p-4 align-items-center">
      <a href="dashboard.php" class="fw-bold text-decoration-none text-white" style="font-size:.9rem;">SPK - PROMETHE</a>
    </h1>

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
            <div class="text-uppercase small fw-bold text-secondary px-3 mt-3 mb-1">Dashboard</div>
            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'index.php') ? 'active' : '' ?>" href="index.php">
              <i class="bi bi-house-fill me-2"></i><span>Beranda</span>
            </a>
          </li>

          <!-- === MASTER DATA SECTION === -->
                     <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

          <li class="nav-item w-100 mt-3">
            <div class="text-uppercase small fw-bold text-secondary px-3 mb-1">Master Data</div>

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
                        <div class="text-uppercase small fw-bold text-secondary px-3 mb-1">Penilaian</div>

            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'cek-gizi.php') ? 'active' : '' ?>" href="cek-gizi.php">
              <i class="bi bi-pencil-square me-2"></i><span>Cek Gizi Balita</span>
            </a>
          </li>

          <!-- === USER SECTION === -->
          <li class="nav-item w-100 mt-3">
            <div class="text-uppercase small fw-bold text-secondary px-3 mb-1">User</div>
            <a class="nav-link d-flex align-items-center justify-content-start <?= ($currentPage === 'user.php') ? 'active' : '' ?>" href="user.php">
              <i class="bi bi-person-fill me-2"></i><span>Data User</span>
            </a>
          </li>

        </ul>
        <div onclick="logout()" class="mt-auto mb-3 px-3 btn d-flex justify-content-start gap-3 text-white bg-danger w-100">
          <i class="bi bi-door-open"></i>
          <span class="text-white">Logout</span>
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
    background-color: #111827;
    min-height: 100vh;
  }

  .nav-link {
    color: #adb5bd;
    padding: 0.6rem 1rem;
    border-radius: .5rem;
    transition: background-color 0.3s, color 0.3s;
    font-size: 0.95rem;
  }

  .nav-link:hover {
    background-color: #1f2937;
    color: #ffffff;
  }

  .nav-link.active {
    background-color: #8e9792ff;
    color: #fff;
  }

  .text-secondary {
    color: #9ca3af !important;
  }
</style>
