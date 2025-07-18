<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "Sistema_Reservas_Restaurantes");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Validar entrada
$usuario = trim($_POST['usuario']);
$contrasena = trim($_POST['contrasena']);

if (empty($usuario) || empty($contrasena)) {
    $_SESSION['error'] = "Por favor, completa todos los campos.";
    header("Location: login_admin.php");
    exit();
}

// Consultar admin en base de datos
$query = "SELECT * FROM admin WHERE usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $admin = $resultado->fetch_assoc();

    // Verificar contraseña con hash
    if (password_verify($contrasena, $admin['contrasena'])) {
        $_SESSION['admin'] = $admin['usuario'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Contraseña incorrecta.";
        header("Location: login_admin.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Usuario no encontrado.";
    header("Location: login_admin.php");
    exit();
}

$stmt->close();
$conexion->close();
?>
