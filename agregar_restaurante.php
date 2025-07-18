<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}
require_once 'includes/conexion.php';

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $hora_apertura = $_POST['hora_apertura'];
    $hora_cierre = $_POST['hora_cierre'];
    $cantidad_mesas = (int) $_POST['cantidad_mesas'];

    // zonas personalizadas
    $zona_vip = (int) $_POST['vip'];
    $zona_terraza = (int) $_POST['terraza'];
    $zona_interior = (int) $_POST['interior'];

    if (!empty($nombre) && !empty($direccion) && !empty($hora_apertura) && !empty($hora_cierre)) {
        $stmt = $conexion->prepare("INSERT INTO restaurantes (nombre, direccion, hora_apertura, hora_cierre, cantidad_mesas) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nombre, $direccion, $hora_apertura, $hora_cierre, $cantidad_mesas);

        if ($stmt->execute()) {
            $restaurante_id = $stmt->insert_id;

            // Insertar mesas por zona
            $zonas = [
                'VIP' => $zona_vip,
                'Terraza' => $zona_terraza,
                'Interior' => $zona_interior
            ];

            $stmt_mesa = $conexion->prepare("INSERT INTO mesas (restaurante_id, zona, numero, estado) VALUES (?, ?, ?, 'Disponible')");
            foreach ($zonas as $zona => $cantidad) {
                for ($i = 1; $i <= $cantidad; $i++) {
                    $stmt_mesa->bind_param("isi", $restaurante_id, $zona, $i);
                    $stmt_mesa->execute();
                }
            }
            $stmt_mesa->close();

            $mensaje = '<div class="alert alert-success">Restaurante y mesas agregados correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al agregar el restaurante.</div>';
        }
        $stmt->close();
    } else {
        $mensaje = '<div class="alert alert-warning">Por favor, completa todos los campos.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Restaurante</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 700px;
      margin-top: 60px;
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .btn-primary {
      background-color: #3f51b5;
      border: none;
    }
    .btn-primary:hover {
      background-color: #3546a1;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4 text-center">Agregar Restaurante</h2>
  <?php echo $mensaje; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre del Restaurante</label>
      <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>
    <div class="mb-3">
      <label for="direccion" class="form-label">Dirección</label>
      <input type="text" class="form-control" id="direccion" name="direccion" required>
    </div>
    <div class="mb-3">
      <label for="hora_apertura" class="form-label">Hora de Apertura</label>
      <input type="time" class="form-control" id="hora_apertura" name="hora_apertura" required>
    </div>
    <div class="mb-3">
      <label for="hora_cierre" class="form-label">Hora de Cierre</label>
      <input type="time" class="form-control" id="hora_cierre" name="hora_cierre" required>
    </div>
    <div class="mb-3">
      <label for="cantidad_mesas" class="form-label">Cantidad Total de Mesas</label>
      <input type="number" class="form-control" id="cantidad_mesas" name="cantidad_mesas" min="1" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Distribución de Mesas:</label>
      <div class="row">
        <div class="col">
          <label>VIP</label>
          <input type="number" name="vip" class="form-control" min="0" value="0" required>
        </div>
        <div class="col">
          <label>Terraza</label>
          <input type="number" name="terraza" class="form-control" min="0" value="0" required>
        </div>
        <div class="col">
          <label>Interior</label>
          <input type="number" name="interior" class="form-control" min="0" value="0" required>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Guardar Restaurante</button>
  </form>

  <a href="dashboard.php" class="btn btn-link mt-3">← Volver al Panel</a>
</div>

</body>
</html>
