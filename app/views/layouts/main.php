<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($config['app_name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="mb-4">TechSolutions Admin Dashboard (PHP MVC)</h1>
  <?php require __DIR__ . '/../partials/nav.php'; ?>
  <main class="card shadow-sm mt-3">
    <div class="card-body">
      <?= $content ?>
    </div>
  </main>
</div>
</body>
</html>
