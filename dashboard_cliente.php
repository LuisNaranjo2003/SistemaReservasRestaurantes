<?php
session_start();
if (!isset($_SESSION['cliente_id'])) {
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel Principal | Sistema Reservas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
 <!--Logo de mi sitio -->
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      font-family: 'Segoe UI', sans-serif;
      background: #f8f9fa;
    }
    .wrapper {
      flex: 1;
      display: flex;
      flex-direction: row;
    }
    .sidebar {
      width: 250px;
      background-color: #3f51b5;
      color: white;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar a:hover {
      background-color: rgba(255,255,255,0.1);
      border-radius: 5px;
    }
    .logout-btn {
      background-color: #f44336;
      border: none;
      padding: 8px 16px;
      color: white;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
      width: 100%;
      margin-top: 20px;
    }
    .logout-btn:hover {
      background-color: #c62828;
    }
    .main {
      flex: 1;
      padding: 30px;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .card-icon {
      font-size: 40px;
      color: #3f51b5;
    }
    footer {
      text-align: center;
      background: #3f51b5;
      padding: 10px 0;
      font-size: 14px;
      color: #fff;
      border-top: none;
    }
    .content-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="sidebar">
    <div>
      <h4 class="mb-4">Bienvenido,<br><?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?></h4>
      <!-- Sidebar links sin Mis Reservas ni Notificaciones -->
      <a href="agregar_reserva.php" class="btn btn-primary mt-3">Agregar Reserva</a>
      <a href="perfil_cliente.php"><i class="bi bi-person"></i> Mi Perfil</a>
    </div>
    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
    </form>
  </div>

  <div class="content-area">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Panel Principal</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCliente" aria-controls="navbarCliente" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarCliente">
          <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i> <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="perfil_cliente.php">Ver Perfil</a></li>
                <!-- Eliminadas opciones Editar Perfil y Ver mis reseñas recientes -->
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="logout.php" method="POST" class="m-0">
                    <button type="submit" class="dropdown-item">Cerrar sesión</button>
                  </form>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="main">
      <h2 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?> al Panel Principal</h2>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card p-4 text-center">
            <i class="bi bi-plus-circle card-icon"></i>
            <h5 class="mt-3">Agregar Reserva</h5>
            <p>Realiza una nueva reserva en tu restaurante favorito.</p>
            <a href="agregar_reserva.php" class="btn btn-primary mt-3">Agregar Reserva</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-4 text-center">
            <i class="bi bi-person card-icon"></i>
            <h5 class="mt-3">Perfil</h5>
            <p>Actualiza tus datos personales y preferencias.</p>
            <a href="perfil_cliente.php" class="btn btn-primary mt-3">Ver Perfil</a>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Sistema de Reservas - Grupo 01. Todos los derechos reservados.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
