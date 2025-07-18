<?php
session_start();
require_once 'includes/conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$cliente_id = $_SESSION['cliente_id'];

// Obtener reservas del cliente
$sql_reservas = "SELECT id, fecha, hora, estado FROM reservas WHERE cliente_nombre = (
    SELECT nombres FROM clientes WHERE id = ?
) AND cliente_apellido = (
    SELECT apellidos FROM clientes WHERE id = ?
) ORDER BY fecha DESC, hora DESC";

$stmt = $conexion->prepare($sql_reservas);
$stmt->bind_param("ii", $cliente_id, $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
$reservas = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($reservas);
