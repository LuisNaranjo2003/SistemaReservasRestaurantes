<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    // Validar campos vacíos
    if (empty($usuario) || empty($contrasena)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: registro_admin.php");
        exit();
    }

    // Encriptar contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    $stmt = $conexion->prepare("INSERT INTO admin (usuario, contrasena) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $contrasena_hash);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Administrador creado correctamente.";
        header("Location: login_admin.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al registrar: " . $stmt->error;
        header("Location: registro_admin.php");
        exit();
    }

    $stmt->close();
    $conexion->close();
}
?>

<!-- HTML del formulario -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!--Logo de mi sitio -->
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card p-4 shadow">
        <h3 class="text-center mb-3">Registrar Administrador</h3>
        <?php
        if (isset($_SESSION['error'])) {
          echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
          unset($_SESSION['success']);
        }
        ?>
        <form method="POST">
          <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" name="usuario" required>
          </div>
          <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="contrasena" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>
        <a href="login_admin.php" class="d-block text-center mt-3">← Volver al login</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
