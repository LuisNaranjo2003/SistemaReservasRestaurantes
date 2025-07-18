<?php
// Iniciar sesión para verificar si el usuario está logueado
session_start();

// Incluir la conexión a la base de datos
require_once 'includes/conexion.php';

// Verificar si se ha enviado el ID del restaurante
if (isset($_GET['restaurante_id'])) {
    $restaurante_id = (int)$_GET['restaurante_id'];

    // Preparar la consulta para obtener las mesas del restaurante
    $sql = "SELECT id, numero, zona FROM mesas WHERE restaurante_id = ? AND estado = 'Disponible'";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $restaurante_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $mesas = [];
    while ($row = $result->fetch_assoc()) {
        $mesas[] = $row;
    }

    // Retornar las mesas en formato JSON
    echo json_encode($mesas);

} else {
    // Si no se pasa el ID del restaurante, retornar un error
    echo json_encode(['error' => 'Restaurante no encontrado']);
}
?>
