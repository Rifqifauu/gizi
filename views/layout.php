<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$name = $_SESSION['name'];
$role = $_SESSION['role'];
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SPK - PROMETHE</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/script/app.css">


</head>

<body class="antialiased">
  <div class="wrapper">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="page-wrapper">
      <?php include __DIR__ . '/components/navbar.php'; ?>
      <div class="page-body">
        <main class="container-fluid py-3">
          <?= $content ?? '' ?>
        </main>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
</body>

</html>
