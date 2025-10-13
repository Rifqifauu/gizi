<?php
$title = 'Beranda';

// Mulai "menangkap" konten halaman
ob_start();
?>
<div class="container">

<?php 
$cardData = [
  [
    'subtitle' => 'MENU',
    'title' => 'kriteria',
    'description' => 'Menu ini digunakan untuk mengelola data kriteria yang menjadi dasar penilaian status gizi balita, seperti berat badan, tinggi badan, umur, dan lainnya.',
  ],
  [
    'subtitle' => 'MENU',
    'title' => 'alternatif',
    'description' => 'Menu ini digunakan untuk mengelola data balita yang akan dievaluasi status gizinya berdasarkan kriteria yang telah ditentukan.',
  ],
  [
    'subtitle' => 'MENU',
    'title' => 'cek gizi',
    'description' => 'Menu ini digunakan untuk melakukan perhitungan dan penentuan status gizi balita secara otomatis menggunakan metode PROMETHEE.',
  ],
  [
    'subtitle' => 'FITUR',
    'title' => 'import/export',
    'description' => 'Fitur ini digunakan untuk mempermudah pengelolaan data dengan melakukan impor data balita dan ekspor hasil penilaian status gizi.',
  ]
];
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper py-4">

  <!-- Welcome Card -->
  <div class="card welcome-card shadow-sm rounded-4 border-0 mb-4">
    <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
      <div>
        <h3 class="mb-1 text-dark fw-bold">
          Selamat datang di SPK - PROMETHE
        </h3>
        <p class="text-muted mb-0">Sistem pendukung keputusan dengan metode Promethe untuk menentukan status gizi pada balita.</p>
      </div>
      <div class="d-none d-md-block">
        <div class="welcome-dot"></div>
      </div>
    </div>
  </div>

  <!-- Card Informasi -->
  <div class="row g-4">
    <?php foreach ($cardData as $card) : ?>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card rounded-4 h-100 shadow-lg overflow-hidden position-relative">
          <div class="card-body row">
              <div class="d-flex flex-column align-items-start mb-3">
                <span class=" bg-success text-white px-2 py-1 rounded-2 mb-2"><?= $card['subtitle'] ?></span>
                <h3 class="card_title mb-2 text-capitalize"><?= htmlspecialchars($card['title']) ?></h3>
              </div>
              <p class="card_description text-muted"><?= htmlspecialchars($card['description']) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<style>
  .content-wrapper {
    background-color: #f4f6f9;
  }

  .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: #fff;
  }

  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }

  .card_title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
  }

  .card_description {
    font-size: 0.95rem;
    color: #6c757d;
  }


</style>

</div>
<?php
// Ambil hasil output ke variabel $content
$content = ob_get_clean();

// Panggil layout utama
include __DIR__ . '/layout.php';
