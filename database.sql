CREATE DATABASE IF NOT EXISTS todocamisetas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE todocamisetas_db;

-- 1. Tabla de Clientes B2B
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comercial VARCHAR(100) NOT NULL,
    rut VARCHAR(12) NOT NULL UNIQUE,
    direccion VARCHAR(255) NOT NULL,
    categoria ENUM('Regular', 'Preferencial') DEFAULT 'Regular',
    contacto_nombre VARCHAR(100) NOT NULL,
    contacto_correo VARCHAR(100) NOT NULL,
    porcentaje_oferta DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Camisetas (Stock)
CREATE TABLE camisetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    titulo VARCHAR(150) NOT NULL,
    club VARCHAR(100) NOT NULL,
    pais VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    color VARCHAR(50) NOT NULL,
    precio INT NOT NULL,
    precio_oferta INT NULL,
    detalles TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabla de Tallas (Catálogo base)
CREATE TABLE tallas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(10) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 4. Tabla Intermedia: Muchos a Muchos (Camisetas <-> Tallas) con CASCADE
CREATE TABLE camiseta_tallas (
    camiseta_id INT NOT NULL,
    talla_id INT NOT NULL,
    PRIMARY KEY (camiseta_id, talla_id),
    FOREIGN KEY (camiseta_id) REFERENCES camisetas(id) ON DELETE CASCADE,
    FOREIGN KEY (talla_id) REFERENCES tallas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertar datos iniciales de prueba para los clientes exigidos por el caso
INSERT INTO clientes (nombre_comercial, rut, direccion, categoria, contacto_nombre, contacto_correo, porcentaje_oferta) VALUES
('90minutos', '11.111.111-1', 'Providencia, Santiago', 'Preferencial', 'Juan Pérez', 'juan@90minutos.cl', 0.00),
('tdeportes', '22.222.222-2', 'Las Condes, Santiago', 'Regular', 'Ana López', 'ana@tdeportes.cl', 10.00);

-- Insertar tallas básicas de prueba
INSERT INTO tallas (nombre) VALUES ('S'), ('M'), ('L'), ('XL');