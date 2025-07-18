<?php
session_start();
require_once 'includes/conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit();
}

$cliente_id = $_SESSION['cliente_id'];

// Obtener datos del cliente
$sql_cliente = "SELECT nombres, apellidos, correo, telefono FROM clientes WHERE id = ?";
$stmt = $conexion->prepare($sql_cliente);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!$cliente) {
    die("Cliente no encontrado.");
}

$cliente_nombre = $cliente['nombres'];
$cliente_apellido = $cliente['apellidos'];

// Obtener reservas con info de mesa y zona
$sql_reservas = "
    SELECT r.id, r.fecha, r.hora, r.estado, r.mesa_id, res.nombre AS restaurante_nombre,
           m.zona, m.numero AS numero_mesa
    FROM reservas r
    INNER JOIN restaurantes res ON r.restaurante_id = res.id
    INNER JOIN mesas m ON r.mesa_id = m.id
    WHERE r.cliente_nombre = ? AND r.cliente_apellido = ?
    ORDER BY r.fecha DESC, r.hora DESC
";

$stmt = $conexion->prepare($sql_reservas);
$stmt->bind_param("ss", $cliente_nombre, $cliente_apellido);
$stmt->execute();
$result = $stmt->get_result();
$reservas = $result->fetch_all(MYSQLI_ASSOC);

// Agrupar reservas por fecha
$reservas_por_fecha = [];
foreach ($reservas as $reserva) {
    $fecha = $reserva['fecha'];
    if (!isset($reservas_por_fecha[$fecha])) {
        $reservas_por_fecha[$fecha] = [];
    }
    $reservas_por_fecha[$fecha][] = $reserva;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Historial de Reservas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f9f9f9;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 800px;
      margin: 30px auto;
      background: white;
      border-radius: 8px;
      padding: 25px 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      color: #007bff;
      margin-bottom: 15px;
    }
    .section-title {
      font-size: 1.3rem;
      margin-top: 25px;
      margin-bottom: 10px;
      border-bottom: 2px solid #007bff;
      padding-bottom: 3px;
      font-weight: 600;
    }
    .reserva-item {
      padding: 10px 0;
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    .reserva-item:last-child {
      border-bottom: none;
    }
    .hora {
      font-weight: 600;
      width: 70px;
      color: #555;
    }
    .restaurante {
      flex-grow: 1;
      color: #333;
    }
    .mesa, .zona {
      font-weight: 600;
      color: #444;
      min-width: 80px;
    }
    .badge {
      font-size: 0.85em;
      padding: 5px 9px;
      text-transform: capitalize;
    }
    .btn-back {
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>👤 Mi Perfil</h2>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($cliente['correo']) ?></p>
    <p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono']) ?></p>

    <h2>📅 Historial de Reservas</h2>

    <?php if (count($reservas) > 0): ?>
        <?php foreach ($reservas_por_fecha as $fecha => $reservas_dia): ?>
            <div class="section-title"><?= date("d M Y", strtotime($fecha)) ?></div>
            <?php foreach ($reservas_dia as $reserva): ?>
                <div class="reserva-item">
                  <div class="hora"><?= date("H:i", strtotime($reserva['hora'])) ?></div>
                  <div class="restaurante"><?= htmlspecialchars($reserva['restaurante_nombre']) ?></div>
                  <div class="mesa">Mesa: <?= htmlspecialchars($reserva['numero_mesa']) ?></div>
                  <div class="zona">Zona: <?= htmlspecialchars($reserva['zona']) ?></div>
                  <div>
                    <?php
                      $estado = strtolower($reserva['estado']);
                      switch ($estado) {
                        case 'pendiente':
                          $badge = 'warning'; $texto = 'Pendiente';
                          break;
                        case 'en curso':
                          $badge = 'info'; $texto = 'En Curso';
                          break;
                        case 'cancelado':
                          $badge = 'danger'; $texto = 'Cancelado';
                          break;
                        case 'finalizado':
                          $badge = 'success'; $texto = 'Finalizado';
                          break;
                        default:
                          $badge = 'dark'; $texto = ucfirst($estado);
                      }
                      echo '<span class="badge bg-' . $badge . '">' . $texto . '</span>';
                    ?>
                  </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No tienes reservas registradas todavía.</p>
    <?php endif; ?>

    <a href="dashboard_cliente.php" class="btn btn-primary btn-back">← Volver al Panel</a>
  </div>
</body>
</html>
