-- Crear la base de datos
CREATE DATABASE  Sistema_Reservas_Restaurantes;
USE Sistema_Reservas_Restaurantes;

-- Tabla del administrador
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL
);

-- Tabla de restaurantes
CREATE TABLE restaurantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    hora_apertura TIME NOT NULL,
    hora_cierre TIME NOT NULL
);

-- Tabla de mesas
CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurante_id INT NOT NULL,
    zona ENUM('Interior', 'Terraza', 'VIP') NOT NULL,
    numero INT NOT NULL,
    estado ENUM('Disponible', 'Ocupada') DEFAULT 'Disponible',
    FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
);

-- Tabla de reservas
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre VARCHAR(100) NOT NULL,
    cliente_apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) NOT NULL,
    restaurante_id INT NOT NULL,
    restaurante_nombre VARCHAR(100) NOT NULL,
    mesa_id INT NOT NULL,
    zona ENUM('Interior', 'Terraza', 'VIP') NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('Pendiente', 'En Curso', 'Cancelado', 'Finalizado') DEFAULT 'Pendiente',
    FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id),
    FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

-- Tabla de notificaciones (opcional)
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mensaje TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombres VARCHAR(100),
  apellidos VARCHAR(100),
  correo VARCHAR(150) UNIQUE,
  telefono VARCHAR(20),
  contrasena VARCHAR(255)
);

