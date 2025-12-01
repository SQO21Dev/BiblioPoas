CREATE DATABASE IF NOT EXISTS bibliopoas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bibliopoas;

-- Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) UNIQUE NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(160) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin','empleado') NOT NULL DEFAULT 'empleado',
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clientes
CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(160) NOT NULL,
  cedula VARCHAR(10),
  telefono VARCHAR(30),
  direccion VARCHAR(255),
  correo VARCHAR(160),
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categorías
CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) UNIQUE NOT NULL,
  descripcion VARCHAR(255),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);

-- Libros
CREATE TABLE IF NOT EXISTS libros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  volumen VARCHAR(120), -- “Vol. / Pte. / No. / Tomo / Ejemplar”
  isbn VARCHAR(40),
  clasificacion_dewey VARCHAR(40),
  autor VARCHAR(160),
  anio_publicacion YEAR,
  categoria_id INT,
  etiquetas VARCHAR(255),
  cantidad INT NOT NULL DEFAULT 1,
  estado ENUM('Disponible','Prestado') NOT NULL DEFAULT 'Disponible',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tiquetes (préstamos)
CREATE TABLE IF NOT EXISTS tiquetes (
  id INT AUTO_INCREMENT PRIMARY KEY,

  codigo VARCHAR(20) NOT NULL UNIQUE,

  cliente_id INT NULL,
  nombre_cliente VARCHAR(160) NOT NULL,
  telefono VARCHAR(30),
  direccion VARCHAR(255),

  libro_id INT NULL,
  titulo VARCHAR(255) NOT NULL,
  autor VARCHAR(160),
  signatura VARCHAR(80),

  categoria_edad ENUM('HJ', 'M', 'HA', 'A') DEFAULT NULL,

  estado ENUM('En Prestamo', 'Devuelto', 'Retrasado') 
         NOT NULL DEFAULT 'En Prestamo',

  fecha_prestamo DATETIME NOT NULL,
  fecha_devolucion DATETIME NOT NULL,

  usuario_registra_id INT NOT NULL,

  observaciones VARCHAR(255) DEFAULT NULL,

  nombre_biblioteca VARCHAR(200) NOT NULL 
                     DEFAULT 'Biblioteca Pública Semioficial de San Rafael de Poás',

  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                ON UPDATE CURRENT_TIMESTAMP,

  -- FOREIGN KEYS
  FOREIGN KEY (usuario_registra_id) REFERENCES usuarios(id),
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  FOREIGN KEY (libro_id) REFERENCES libros(id)
);


-- Logs / auditoría
CREATE TABLE IF NOT EXISTS logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  accion VARCHAR(60) NOT NULL,         -- create/update/delete/login/logout/export, etc.
  entidad VARCHAR(60) NOT NULL,        -- usuarios, clientes, libros, tiquetes
  entidad_id INT NULL,
  meta JSON NULL,
  ip VARCHAR(45),
  user_agent VARCHAR(255),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Usuario admin inicial (cambia la contraseña después):
INSERT IGNORE INTO usuarios (usuario, nombre, correo, password_hash, rol, estado)
VALUES ('admin', 'Administrador', 'admin@bibliopoas.cr', 
        SHA2(CONCAT('cambia-esto-por-bcrypt-', NOW()), 256), 'admin', 'activo');
