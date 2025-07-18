<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once 'includes/conexion.php';

// --- MANEJO DE POST PARA ESTADO Y ELIMINAR --- //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (isset($_POST['reserva_id'], $_POST['estado'])) {
        $reserva_id = intval($_POST['reserva_id']);
        $nuevo_estado = $_POST['estado'];

        $stmt = $conexion->prepare("UPDATE reservas SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_estado, $reserva_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        exit();
    }

    if (isset($_POST['eliminar_reserva_id'])) {
        $reserva_id = intval($_POST['eliminar_reserva_id']);

        $stmt = $conexion->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->bind_param("i", $reserva_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Reserva eliminada correctamente']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        exit();
    }

    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit();
}

// --- FILTROS GET --- //
$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_mesa = $_GET['mesa'] ?? '';
$filtro_restaurante = $_GET['restaurante'] ?? '';

$where = [];
$params = [];
$tipos = "";

if ($filtro_fecha !== '') {
    $where[] = "r.fecha = ?";
    $params[] = $filtro_fecha;
    $tipos .= "s";
}
if ($filtro_mesa !== '') {
    $where[] = "m.numero = ?";
    $params[] = $filtro_mesa;
    $tipos .= "s";
}
if ($filtro_restaurante !== '') {
    $where[] = "res.nombre = ?";
    $params[] = $filtro_restaurante;
    $tipos .= "s";
}
$condicion = $where ? "WHERE " . implode(" AND ", $where) : "";

// --- CONSULTA PRINCIPAL --- //
$sql = "SELECT r.id, r.cliente_nombre, r.cliente_apellido, r.cedula,
               res.nombre AS restaurante, m.numero AS mesa_numero,
               r.zona, r.fecha, r.hora, r.estado
        FROM reservas r
        JOIN restaurantes res ON r.restaurante_id = res.id
        JOIN mesas m ON r.mesa_id = m.id
        $condicion
        ORDER BY r.id ASC";

$stmt = $conexion->prepare($sql);
if ($params) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// --- VALORES PARA FILTROS --- //
$mesas = $conexion->query("SELECT DISTINCT numero FROM mesas ORDER BY numero ASC")->fetch_all(MYSQLI_ASSOC);
$restaurantes = $conexion->query("SELECT DISTINCT nombre FROM restaurantes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ver Reservas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
  <style>
    body { background-color: #f8f9fa; }
    .container { margin-top: 40px; }
    .table-container {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .table th { background-color: #3f51b5; color: white; }
    .btn-volver { margin-top: 20px; }
    select.form-select-sm { min-width: 140px; }
    #mensaje-exito { display: none; }
  </style>
</head>
<body>

<div class="container">
  <div class="table-container">
    <h2 class="text-center mb-4">Reservas Registradas</h2>

    <!-- FILTROS -->
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($filtro_fecha) ?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="mesa" class="form-label">Mesa</label>
        <select name="mesa" id="mesa" class="form-select">
          <option value="">Todas</option>
          <?php foreach ($mesas as $m): ?>
            <option value="<?= $m['numero'] ?>" <?= $filtro_mesa == $m['numero'] ? 'selected' : '' ?>><?= $m['numero'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label for="restaurante" class="form-label">Restaurante</label>
        <select name="restaurante" id="restaurante" class="form-select">
          <option value="">Todos</option>
          <?php foreach ($restaurantes as $r): ?>
            <option value="<?= $r['nombre'] ?>" <?= $filtro_restaurante == $r['nombre'] ? 'selected' : '' ?>><?= $r['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-50">Filtrar</button>
        <a href="ver_reservas.php" class="btn btn-outline-secondary w-50">Eliminar Filtros</a>
      </div>
    </form>

    <div id="mensaje-exito" class="alert alert-success text-center" role="alert"></div>

    <?php if ($resultado->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Cédula</th>
              <th>Restaurante</th>
              <th>Mesa</th>
              <th>Zona</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($reserva = $resultado->fetch_assoc()): ?>
              <tr data-id="<?= $reserva['id'] ?>">
                <td><?= $reserva['id'] ?></td>
                <td><?= htmlspecialchars($reserva['cliente_nombre']) ?></td>
                <td><?= htmlspecialchars($reserva['cliente_apellido']) ?></td>
                <td><?= htmlspecialchars($reserva['cedula']) ?></td>
                <td><?= htmlspecialchars($reserva['restaurante']) ?></td>
                <td><?= htmlspecialchars($reserva['mesa_numero']) ?></td>
                <td><?= htmlspecialchars($reserva['zona']) ?></td>
                <td><?= $reserva['fecha'] ?></td>
                <td><?= $reserva['hora'] ?></td>
                <td>
                  <select class="form-select form-select-sm estado-select">
                    <option value="Pendiente" <?= $reserva['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="En revision" <?= $reserva['estado'] === 'En revision' ? 'selected' : '' ?>>En revisión</option>
                    <option value="En Curso" <?= $reserva['estado'] === 'En Curso' ? 'selected' : '' ?>>En Curso</option>
                    <option value="Cancelado" <?= $reserva['estado'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="Finalizado" <?= $reserva['estado'] === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                  </select>
                </td>
                <td>
                  <button class="btn btn-success btn-sm aplicar-cambio" title="Aplicar"><i class="bi bi-check-lg"></i></button>
                  <button class="btn btn-warning btn-sm" title="Modificar"><i class="bi bi-pencil-square"></i></button>
                  <button class="btn btn-danger btn-sm btn-borrar" title="Borrar"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center">No hay reservas registradas.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary btn-volver">← Volver al Panel</a>
  </div>
</div>

<script>
  // Aplicar cambio de estado
  document.querySelectorAll(".aplicar-cambio").forEach(btn => {
    btn.addEventListener("click", function () {
      const row = this.closest("tr");
      const reservaId = row.dataset.id;
      const estado = row.querySelector(".estado-select").value;

      fetch("ver_reservas.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `reserva_id=${reservaId}&estado=${encodeURIComponent(estado)}`
      })
        .then(res => res.json())
        .then(data => {
          const msg = document.getElementById("mensaje-exito");
          if (data.success) {
            msg.textContent = "Reserva actualizada a " + estado;
            msg.style.display = "block";
            setTimeout(() => msg.style.display = "none", 3000);
          } else {
            alert("Error al actualizar: " + (data.error || ""));
          }
        })
        .catch(error => {
          console.error("Error fetch:", error);
          alert("Error de conexión");
        });
    });
  });

  // Borrar reserva
  document.querySelectorAll(".btn-borrar").forEach(btn => {
    btn.addEventListener("click", function () {
      if (!confirm("¿Estás seguro que deseas eliminar esta reserva?")) return;

      const row = this.closest("tr");
      const reservaId = row.dataset.id;

      fetch("ver_reservas.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `eliminar_reserva_id=${reservaId}`
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            row.remove();
            const msg = document.getElementById("mensaje-exito");
            msg.textContent = data.mensaje;
            msg.style.display = "block";
            setTimeout(() => msg.style.display = "none", 3000);
          } else {
            alert("Error al eliminar: " + (data.error || ""));
          }
        })
        .catch(error => {
          console.error("Error fetch:", error);
          alert("Error de conexión");
        });
    });
  });
</script>

</body>
</html>
