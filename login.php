<?php
session_start();

// Verifica si ya inició sesión como cliente
if (isset($_SESSION['cliente_id'])) {
  header("Location: dashboard_cliente.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión | Cliente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!--Logo de mi sitio -->
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
  <style>
    body {
      height: 100vh;
      background: linear-gradient(135deg, rgb(130, 102, 255), rgb(94, 110, 255));
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-box {
      background: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
    }

    .login-box h2 {
      margin-bottom: 30px;
      color: rgb(97, 58, 255);
      text-align: center;
      font-weight: bold;
    }

    .form-control {
      border-radius: 10px;
    }

    .btn-login, .btn-register {
      font-weight: bold;
      width: 48%;
      border-radius: 50px;
      transition: 0.3s;
    }

    .btn-login {
      background-color: rgb(58, 58, 255);
      color: white;
    }

    .btn-login:hover {
      background-color: rgb(66, 38, 226);
    }

    .btn-register {
      background-color: #6c757d;
      color: white;
    }

    .btn-register:hover {
      background-color: #5a6268;
    }

    .btn-back, .btn-admin {
      margin-top: 15px;
      display: block;
      text-align: center;
      text-decoration: none;
      font-size: 14px;
    }

    .btn-back {
      color: #999;
    }

    .btn-back:hover {
      color: #333;
    }

    .btn-admin {
      color: #0d6efd;
      font-weight: bold;
    }

    .btn-admin:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h2>Iniciar sesión</h2>
    <form action="procesar_login_cliente.php" method="POST">
      <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" required>
      </div>
      <div class="mb-3">
        <label for="contrasena" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
      </div>
      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-login">Ingresar</button>
        <a href="registro.php" class="btn btn-register text-center">Registrar</a>
      </div>
    </form>

    <?php
    if (isset($_SESSION['error_cliente'])) {
      echo '<div class="alert alert-danger mt-3" role="alert">' . $_SESSION['error_cliente'] . '</div>';
      unset($_SESSION['error_cliente']);
    }
    ?>

    <a href="login_admin.php" class="btn-admin">¿Eres administrador? Inicia sesión aquí</a>
    <a href="index.php" class="btn-back">← Volver al inicio</a>
  </div>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
