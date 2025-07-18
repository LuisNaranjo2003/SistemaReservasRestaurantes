<?php
session_start();
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit();
}

$cliente_id = $_SESSION['cliente_id'];

// Conexión a la base de datos
require_once 'includes/conexion.php';

// Inicializar variables
$mensaje = '';

// Obtener los datos del cliente
$sql = "SELECT nombres, apellidos FROM clientes WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cliente = $result->fetch_assoc();
    $cliente_nombre = $cliente['nombres'];
    $cliente_apellido = $cliente['apellidos'];
} else {
    $mensaje = '<div class="alert alert-danger">Cliente no encontrado.</div>';
}

// Obtener lista de restaurantes con hora apertura y cierre
$restaurantes = [];
$resultado = $conexion->query("SELECT id, nombre, hora_apertura, hora_cierre FROM restaurantes");
while ($row = $resultado->fetch_assoc()) {
    $restaurantes[] = $row;
}

// Guardar reserva
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']);
    $restaurante_id = (int)$_POST['restaurante_id'];
    $mesa_id = (int)$_POST['mesa_id'];
    $zona = trim($_POST['zona']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    if ($nombre && $apellido && $cedula && $restaurante_id && $mesa_id && $zona && $fecha && $hora) {
        $fecha_actual = date('Y-m-d');

        if ($fecha < $fecha_actual) {
            $mensaje = '<div class="alert alert-danger">No puedes reservar en una fecha pasada.</div>';
        } else {
            // Obtener horarios apertura y cierre del restaurante
            $sql_hora = "SELECT hora_apertura, hora_cierre FROM restaurantes WHERE id = ?";
            $stmt_hora = $conexion->prepare($sql_hora);
            $stmt_hora->bind_param("i", $restaurante_id);
            $stmt_hora->execute();
            $resultado_hora = $stmt_hora->get_result();

            if ($resultado_hora->num_rows > 0) {
                $restaurante = $resultado_hora->fetch_assoc();
                $hora_apertura = $restaurante['hora_apertura'];
                $hora_cierre = $restaurante['hora_cierre'];

                $time_reserva = strtotime($hora);
                $time_apertura = strtotime($hora_apertura);
                $time_cierre = strtotime($hora_cierre);

                // Validar horario considerando horario cruzado
                if ($time_cierre < $time_apertura) {
                    // Ejemplo: apertura 23:30, cierre 14:30
                    if (!($time_reserva >= $time_apertura || $time_reserva < $time_cierre)) {
                        $mensaje = '<div class="alert alert-danger">La hora seleccionada está fuera del horario de atención. El restaurante abre a las ' . $hora_apertura . ' y cierra a las ' . $hora_cierre . '.</div>';
                    }
                } else {
                    if ($time_reserva < $time_apertura || $time_reserva >= $time_cierre) {
                        $mensaje = '<div class="alert alert-danger">La hora seleccionada está fuera del horario de atención. El restaurante abre a las ' . $hora_apertura . ' y cierra a las ' . $hora_cierre . '.</div>';
                    }
                }

                // Validar si la mesa está ocupada en la misma fecha y hora
                if (!$mensaje) {
                    $sql_ocupada = "SELECT COUNT(*) as total FROM reservas WHERE mesa_id = ? AND fecha = ? AND hora = ?";
                    $stmt_ocupada = $conexion->prepare($sql_ocupada);
                    $stmt_ocupada->bind_param("iss", $mesa_id, $fecha, $hora);
                    $stmt_ocupada->execute();
                    $res_ocupada = $stmt_ocupada->get_result();
                    $fila = $res_ocupada->fetch_assoc();

                    if ($fila['total'] > 0) {
                        $mensaje = '<div class="alert alert-danger">La mesa seleccionada ya está reservada para esa fecha y hora. Por favor, elige otra mesa o un horario diferente.</div>';
                    }
                    $stmt_ocupada->close();
                }

                // Insertar reserva si no hay error
                if (!$mensaje) {
                    $stmt = $conexion->prepare("INSERT INTO reservas (cliente_nombre, cliente_apellido, cedula, restaurante_id, mesa_id, zona, fecha, hora) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssissss", $nombre, $apellido, $cedula, $restaurante_id, $mesa_id, $zona, $fecha, $hora);

                    if ($stmt->execute()) {
                        $mensaje = '<div class="alert alert-success">Reserva guardada correctamente.</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al guardar la reserva.</div>';
                    }
                    $stmt->close();
                }

            } else {
                $mensaje = '<div class="alert alert-danger">No se encontró el restaurante para validar su horario.</div>';
            }
            $stmt_hora->close();
        }
    } else {
        $mensaje = '<div class="alert alert-warning">Completa todos los campos.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agregar Reserva</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon" />
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 700px;
      margin-top: 40px;
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .zona-img {
      width: 100px;
      cursor: pointer;
      border: 2px solid transparent;
      border-radius: 10px;
      margin-right: 10px;
    }
    .zona-img.selected {
      border-color: #3f51b5;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4 text-center">Agregar Reserva</h2>

  <?= $mensaje ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($cliente_nombre) ?>" readonly />
    </div>
    <div class="mb-3">
      <label class="form-label">Apellido</label>
      <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($cliente_apellido) ?>" readonly />
    </div>
    <div class="mb-3">
      <label class="form-label">Cédula</label>
      <input type="text" class="form-control" name="cedula" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Restaurante</label>
      <select class="form-select" name="restaurante_id" id="restaurante_id" required>
        <option value="">Selecciona un restaurante</option>
        <?php foreach ($restaurantes as $r): ?>
          <option value="<?= $r['id'] ?>" data-hora-apertura="<?= $r['hora_apertura'] ?>" data-hora-cierre="<?= $r['hora_cierre'] ?>">
            <?= htmlspecialchars($r['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3" id="mesas_container" style="display:none;">
      <label class="form-label">Mesa</label>
      <select class="form-select" name="mesa_id" id="mesa_id" required>
        <option value="">Selecciona una mesa</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Zona</label><br />
      <input type="hidden" name="zona" id="zona" />
      <img src="assets/img/interior.png" alt="Interior" class="zona-img" data-zona="Interior" />
      <img src="assets/img/terraza.png" alt="Terraza" class="zona-img" data-zona="Terraza" />
      <img src="assets/img/vip.png" alt="VIP" class="zona-img" data-zona="VIP" />
    </div>
    <div class="mb-3">
      <label class="form-label">Fecha</label>
      <input type="date" class="form-control" name="fecha" id="fecha" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Hora</label>
      <input type="time" class="form-control" name="hora" id="hora" required />
    </div>
    <button type="submit" class="btn btn-primary w-100">Guardar Reserva</button>
  </form>

  <a href="dashboard_cliente.php" class="btn btn-link mt-3">← Volver al Panel</a>
</div>

<script>
  // Establecer la fecha mínima como hoy
  const fechaInput = document.getElementById('fecha');
  const hoy = new Date().toISOString().split('T')[0];
  fechaInput.setAttribute('min', hoy);

  // Selección de zona
  const zonaInput = document.getElementById('zona');
  const zonaImgs = document.querySelectorAll('.zona-img');
  zonaImgs.forEach(img => {
    img.addEventListener('click', () => {
      zonaImgs.forEach(i => i.classList.remove('selected'));
      img.classList.add('selected');
      zonaInput.value = img.dataset.zona;
    });
  });

  // Cargar mesas y actualizar hora máxima y mínima según restaurante
  const restauranteSelect = document.getElementById('restaurante_id');
  const horaInput = document.getElementById('hora');

  restauranteSelect.addEventListener('change', function () {
    const selectedOption = restauranteSelect.options[restauranteSelect.selectedIndex];
    const horaApertura = selectedOption.getAttribute('data-hora-apertura');
    const horaCierre = selectedOption.getAttribute('data-hora-cierre');

    // Establecer mínimo y máximo permitido en hora
    if (horaApertura) {
      horaInput.setAttribute('min', horaApertura);
    } else {
      horaInput.removeAttribute('min');
    }
    if (horaCierre) {
      horaInput.setAttribute('max', horaCierre);
    } else {
      horaInput.removeAttribute('max');
    }

    // Mostrar mesas disponibles
    const restauranteId = this.value;
    const mesaSelect = document.getElementById('mesa_id');
    const mesaContainer = document.getElementById('mesas_container');

    mesaSelect.innerHTML = '<option value="">Cargando...</option>';
    mesaContainer.style.display = 'block';

    fetch('obtener_mesas.php?restaurante_id=' + restauranteId)
      .then(res => res.json())
      .then(data => {
        mesaSelect.innerHTML = '<option value="">Selecciona una mesa</option>';
        data.forEach(mesa => {
          mesaSelect.innerHTML += `<option value="${mesa.id}">Mesa #${mesa.numero} - ${mesa.zona}</option>`;
        });
      });
  });
</script>

</body>
</html>
