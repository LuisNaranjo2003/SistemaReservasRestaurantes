<?php
session_start();
require_once 'includes/conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    echo "<p>No estás autenticado.</p>";
    exit();
}

$cliente_id = $_SESSION['cliente_id'];

// Obtener reservas del cliente
$sql_reservas = "SELECT id, fecha, hora, estado FROM reservas WHERE cliente_nombre = (
                    SELECT CONCAT(nombres, ' ', apellidos) FROM clientes WHERE id = ?
                 )
                 ORDER BY fecha DESC, hora DESC";

$stmt = $conexion->prepare($sql_reservas);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
$reservas = $result->fetch_all(MYSQLI_ASSOC);

if (count($reservas) > 0):
?>
 <!--Logo de mi sitio -->
  <link rel="icon" href="assets/img/logo.ico" type="image/x-icon">
<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead class="table-primary">
      <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reservas as $reserva): ?>
        <tr>
          <td><?= htmlspecialchars($reserva['id']) ?></td>
          <td><?= htmlspecialchars($reserva['fecha']) ?></td>
          <td><?= htmlspecialchars($reserva['hora']) ?></td>
          <td>
            <?php
              $estado = strtolower($reserva['estado']);
              switch ($estado) {
                case 'pendiente':
                  $badge = 'warning';
                  $texto = 'Pendiente';
                  break;
                case 'en curso':
                  $badge = 'info';
                  $texto = 'En Curso';
                  break;
                case 'cancelado':
                  $badge = 'danger';
                  $texto = 'Cancelado';
                  break;
                case 'finalizado':
                  $badge = 'success';
                  $texto = 'Finalizado';
                  break;
                default:
                  $badge = 'secondary';
                  $texto = htmlspecialchars($reserva['estado']);
              }
              echo '<span class="badge bg-' . $badge . '">' . $texto . '</span>';
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
  <p class="text-muted">No tienes reservas registradas todavía.</p>
<?php endif; ?>
