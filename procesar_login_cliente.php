<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'includes/conexion.php'; // Conexión a la BD

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    // Validar campos vacíos
    if (empty($correo) || empty($contrasena)) {
        $_SESSION['error'] = "Por favor completa todos los campos.";
        header("Location: login.php");
        exit();
    }

    // Buscar cliente por correo
    $stmt = $conexion->prepare("SELECT id, nombres, contrasena FROM clientes WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $cliente = $resultado->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contrasena, $cliente['contrasena'])) {
            // Login exitoso
            $_SESSION['cliente_id'] = $cliente['id'];
            $_SESSION['cliente_nombre'] = $cliente['nombres'];
            header("Location: dashboard_cliente.php");
            exit();
        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Correo no registrado.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: login.php");
    exit();
}
