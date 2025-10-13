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
            header('Location: dashboard.php');
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
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SPK - PROMETHE</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <style>
    body {
      background: linear-gradient(135deg, #1eae74ff, #067c47ff);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: "Segoe UI", sans-serif;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      padding: 2.5rem;
      border-radius: 20px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
      color: #fff;
    }

    .login-container h1 {
      font-weight: 700;
      font-size: 1.8rem;
      text-align: center;
      margin-bottom: 0.25rem;
    }

    .login-container h2 {
      font-weight: 400;
      font-size: 1rem;
      text-align: center;
      margin-bottom: 1.5rem;
      opacity: 0.85;
    }

    .form-label {
      color: #fff;
      font-weight: 500;
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.15);
      border: none;
      color: #fff;
      transition: background 0.3s ease;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.25);
      outline: none;
      box-shadow: 0 0 0 2px #076b48;
      color: #fff;
    }

    .input-group-text {
      background-color: rgba(255, 255, 255, 0.15) !important;
      border: none !important;
      color: #333 !important;
      padding: 0.5rem 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: none !important;
    }

    .alert {
      background-color: rgba(220, 53, 69, 0.9);
      color: #fff;
      border: none;
      text-align: center;
      font-weight: 500;
      margin-bottom: 1rem;
    }

    #togglePassword {
      cursor: pointer;
      color: #eeeeeeff;
      font-size: 1.2rem;
    }
.btn:hover {
    color : black;
}
    
  </style>
</head>
<body>
  <div class="login-container">
    <h1>SPK Gizi Balita</h1>
    <h2>Masuk ke Sistem</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="post" autocomplete="off" novalidate>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input 
          type="text" 
          name="username" 
          class="form-control" 
          placeholder="Your Username" 
          required 
        />
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input 
            type="password" 
            name="password" 
            class="form-control"
            placeholder="Your password" 
            required
          />
          <span class="input-group-text">
            <i class="bi bi-eye" id="togglePassword"></i>
          </span>
        </div>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn w-100">Sign in</button>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.querySelector('input[name="password"]');

    togglePassword.addEventListener('click', () => {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      togglePassword.classList.toggle('bi-eye-slash', isPassword);
      togglePassword.classList.toggle('bi-eye', !isPassword);
    });
  </script>
</body>
</html>