-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS sistema_notas;
USE sistema_notas;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'docente', 'estudiante') NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de estudiantes
CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    lugar_nacimiento VARCHAR(100),
    direccion TEXT,
    telefono VARCHAR(20),
    carrera VARCHAR(100) NOT NULL,
    fecha_ingreso DATE NOT NULL,
    foto_perfil VARCHAR(255),
    usuario_id INT UNIQUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de docentes
CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    anos_servicio INT,
    horas_trabajo INT,
    telefono VARCHAR(20),
    foto_perfil VARCHAR(255),
    usuario_id INT UNIQUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de administradores
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(20),
    usuario_id INT UNIQUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Las demás tablas permanecen sin cambios
-- ...

-- Índices adicionales para la nueva estructura
CREATE INDEX idx_usuario_rol ON usuarios(rol);
CREATE INDEX idx_estudiante_usuario ON estudiantes(usuario_id);
CREATE INDEX idx_docente_usuario ON docentes(usuario_id);
CREATE INDEX idx_administrador_usuario ON administradores(usuario_id);

