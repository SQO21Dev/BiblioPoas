<?php
/** Layout principal autenticado con sidebar fijo y contenedor .main-content */
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title ?? 'BiblioPoás', ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <!-- Fuentes -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Nunito:wght@700;900&display=swap" rel="stylesheet">
  <!-- Font Awesome + SweetAlert2 -->
  <script src="https://kit.fontawesome.com/5152164a0e.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Chart.js (para dashboards futuros) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Estilos -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="dashboard-root">
  <?php require __DIR__ . '/../partials/sidebar.php'; ?>

  <main class="main-content">
    <?php require __DIR__ . '/../partials/toasts.php'; ?>
    <?= $content ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/assets/js/app.js" type="module"></script>
</body>
</html>
