CREATE DATABASE IF NOT EXISTS biblioteca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE biblioteca_db;

-- LIBRO
CREATE TABLE libro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100),
    isbn VARCHAR(30) UNIQUE,
    editorial VARCHAR(100),
    anio YEAR,
    categoria VARCHAR(50),
    descripcion TEXT,
    estado ENUM('disponible', 'prestado', 'extraviado') DEFAULT 	'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE 
    CURRENT_TIMESTAMP
);

-- USUARIO
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    direccion VARCHAR(150),
    dni VARCHAR(20) UNIQUE,
    fecha_registro DATE DEFAULT (CURRENT_DATE),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PRESTAMO
CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_prestamo DATE DEFAULT (CURRENT_DATE),
    fecha_devolucion DATE NOT NULL,
    fecha_dev_real DATE,
    estado ENUM('prestado', 'devuelto', 'atrasado') DEFAULT 	'prestado',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_prestamos_libro FOREIGN KEY (libro_id)
        REFERENCES libro(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_prestamos_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- USUARIO_SISTEMA
CREATE TABLE usuario_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- hash de contraseña
    nombre VARCHAR(100),
    email VARCHAR(100),
    rol ENUM('admin', 'bibliotecario', 'consulta') DEFAULT 		'consulta',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);