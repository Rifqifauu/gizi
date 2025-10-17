<?php
include '../koneksi.php';
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM user WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Password salah';
        }
    } else {
        $error = 'Username tidak ditemukan';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | SPK Gizi Balita</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #1eae74ff, #067c47ff);
      min-height: 100vh;
      display: flex;
      align-items: center;
      font-family: "Segoe UI", sans-serif;
      color: #fff;
    }

    .container {
      max-width: 1100px;
    }

    .left-section {
      padding-right: 3rem;
    }

    .left-section h1 {
      font-weight: 700;
      font-size: 2.2rem;
      margin-bottom: 1rem;
    }

    .left-section p {
      font-size: 1rem;
      line-height: 1.7;
      opacity: 0.9;
    }

    .card {
      width: 100%;
      border: none;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .card-body {
      padding: 4rem 2rem;

    }

    .form-control {
      border-radius: 50px;
      padding: 0.75rem 1rem;
    }

    .input-group-text {
      border-radius: 50px;
      background-color: #f8f9fa;
    }

    .btn-primary {
      background-color: #067c47;
      border: none;
      border-radius: 50px;
      padding: 0.75rem;
      font-weight: 500;
    }

    .btn-primary:hover {
      background-color: #056b3e;
    }

    .form-label {
      font-weight: 500;
    }

    #togglePassword {
      cursor: pointer;
    }

    @media (max-width: 768px) {
      body {
        padding: 2rem 1rem;
      }
      .left-section {
        text-align: center;
        padding-right: 0;
        margin-bottom: 2rem;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="row align-items-center">
      <!-- Kiri -->
      <div class="col-md-6 left-section">
        <h1>SPK Gizi Balita Metode Promethee</h1>
        <p>
          Sistem Pendukung Keputusan (SPK) Gizi Balita dengan metode Promethee membantu tenaga kesehatan dan kader posyandu 
          dalam menentukan status gizi balita secara lebih cepat dan objektif. 
          Dengan sistem ini, proses penilaian gizi dapat dilakukan berdasarkan berbagai kriteria seperti berat badan, tinggi badan, usia, dan data kesehatan lainnya.
        </p>
        <p>
          Aplikasi ini dirancang untuk meningkatkan ketepatan analisis serta membantu pengambilan keputusan yang lebih akurat 
          dalam upaya pencegahan dan penanganan gizi buruk pada anak-anak.
        </p>
      </div>

      <!-- Kanan -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h3 class="text-center mb-1 text-dark p-4">Masuk Ke Sistem</h3>

            <?php if ($error): ?>
              <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
              <div class="mb-3">
                <input 
                  type="text" 
                  name="username" 
                  class="form-control" 
                  placeholder="Masukkan username"
                  required
                >
              </div>

              <div class="mb-3">
                <div class="input-group">
                  <input 
                    type="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Masukkan password"
                    required
                  >
                  <span class="input-group-text" id="togglePassword">
                    <i class="bi bi-eye"></i>
                  </span>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.querySelector('input[name="password"]');
    const icon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
    });
  </script>

</body>
</html>
