<?php
include '../koneksi.php';
session_start();
if(isset($_SESSION['user_id'])){
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
        // Cek password
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

<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SPK - PROMETHE</title>
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Masuk ke sistem</h2>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="" method="post" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Your Username" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <div class="input-group input-group-flat">
                            <input type="password" name="password" class="form-control" placeholder="Your password" required>
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="togglePassword" style="cursor:pointer;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
<script>
// Show/Hide password
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.querySelector('input[name="password"]');

togglePassword.addEventListener('click', () => {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    togglePassword.classList.toggle('bi-eye-slash');
});
</script>
</body>
</html>
