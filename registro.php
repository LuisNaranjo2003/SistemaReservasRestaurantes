<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = trim($_POST['contrasena']);

    // Validación básica
    if (empty($nombres) || empty($apellidos) || empty($correo) || empty($telefono) || empty($contrasena)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: registro.php");
        exit();
    }

    // Validar que el teléfono tenga exactamente 10 dígitos
    if (!preg_match('/^\d{10}$/', $telefono)) {
        $_SESSION['error'] = "El teléfono debe contener exactamente 10 dígitos.";
        header("Location: registro.php");
        exit();
    }

    // Validar que el teléfono no exista ya en la base de datos
    $verificar_telefono = $conexion->prepare("SELECT id FROM clientes WHERE telefono = ?");
    $verificar_telefono->bind_param("s", $telefono);
    $verificar_telefono->execute();
    $verificar_telefono->store_result();

    if ($verificar_telefono->num_rows > 0) {
        $_SESSION['error'] = "Este número de teléfono ya está registrado.";
        $verificar_telefono->close();
        header("Location: registro.php");
        exit();
    }
    $verificar_telefono->close();

    // Encriptar la contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar cliente en la base de datos
    $stmt = $conexion->prepare("INSERT INTO clientes (nombres, apellidos, correo, telefono, contrasena) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombres, $apellidos, $correo, $telefono, $contrasena_hash);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Cliente registrado correctamente.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al registrar: " . $stmt->error;
        header("Location: registro.php");
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
  <title>Registro de Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow">
        <h3 class="text-center mb-3">Registro de Cliente</h3>
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
        <form method="POST" id="registroForm">
          <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control" name="nombres" required>
          </div>
          <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control" name="apellidos" required>
          </div>
          <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="correo" required>
          </div>
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" name="telefono" maxlength="10" pattern="\d{10}" required
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          </div>
          <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="contrasena" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </form>
        <a href="login.php" class="d-block text-center mt-3">← Volver al login</a>
      </div>
    </div>
  </div>
</div>

<!-- Validación en el cliente -->
<script>
document.getElementById("registroForm").addEventListener("submit", function(e) {
  const telefono = document.querySelector('input[name="telefono"]').value.trim();
  if (!/^\d{10}$/.test(telefono)) {
    e.preventDefault();
    alert("Solo coloque 10 dígitos en el teléfono.");
  }
});
</script>

</body>
</html>
