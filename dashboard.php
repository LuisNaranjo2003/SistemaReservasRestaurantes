<?php 
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login_admin.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú Principal | Sistema Reservas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card-icon {
      font-size: 40px;
      color: #3f51b5;
    }

    .card .btn {
      margin-top: 15px;
    }

    .btn-primary-custom {
      background-color: #3f51b5;
      color: white;
      border: none;
      transition: background-color 0.3s ease;
    }

    .btn-primary-custom:hover {
      background-color: #2c3e9e;
      color: white;
    }

    footer {
      text-align: center;
      background: #3f51b5;
      padding: 10px 0;
      font-size: 14px;
      color: #fff;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="sidebar">
    <div>
      <h4 class="mb-4">Bienvenido,<br><?php echo htmlspecialchars($_SESSION['admin']); ?></h4>
      <a href="agregar_restaurante.php"><i class="bi bi-shop"></i> Agregar Restaurante</a>
      <a href="ver_reservas.php"><i class="bi bi-table"></i> Ver Reservas</a>
    </div>
    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
    </form>
  </div>

  <div class="main">
    <h2 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['admin']); ?> al Panel Principal de Sistemas de Reservas</h2>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card p-4 text-center">
          <div>
            <i class="bi bi-shop card-icon"></i>
            <h5 class="mt-3">Restaurantes</h5>
            <p>Gestiona y edita los restaurantes registrados.</p>
          </div>
          <a href="agregar_restaurante.php" class="btn btn-primary-custom"><i class="bi bi-plus-circle"></i> Gestionar</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4 text-center">
          <div>
            <i class="bi bi-calendar-check card-icon"></i>
            <h5 class="mt-3">Reservas</h5>
            <p>Visualiza las reservas de clientes.</p>
          </div>
          <a href="ver_reservas.php" class="btn btn-primary-custom"><i class="bi bi-eye"></i> Ver Reservas</a>
        </div>
      </div>
    </div>
  </div>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Sistema de Reservas - Grupo 01. Todos los derechos reservados.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
