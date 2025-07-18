<?php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "Sistema_Reservas_Restaurantes";

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer codificación UTF-8 (importante para tildes, ñ, etc.)
$conexion->set_charset("utf8mb4");
?>
