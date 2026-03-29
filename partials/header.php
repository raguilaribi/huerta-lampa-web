<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/index.php"><?php echo APP_NAME; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/index.php"><i class="bi bi-house"></i> Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="/crops.php"><i class="bi bi-tree"></i> Cultivos</a></li>
        <li class="nav-item"><a class="nav-link" href="/calendar.php"><i class="bi bi-calendar3"></i> Calendario anual</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mb-4">
