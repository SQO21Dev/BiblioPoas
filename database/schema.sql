-- Crear base si no existe
CREATE DATABASE IF NOT EXISTS biblio_poas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblio_poas;

SET NAMES utf8mb4;

-- USUARIOS
-- USUARIOS
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  nombre  VARCHAR(120) NOT NULL,
  correo  VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin','empleado') NOT NULL DEFAULT 'empleado',
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  contrasena_temporal VARCHAR(255) DEFAULT NULL,
  creado_en    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- CLIENTES
DROP TABLE IF EXISTS clientes;
CREATE TABLE clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  cedula VARCHAR(50) DEFAULT NULL,
  telefono VARCHAR(50) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_clientes_nombre (nombre),
  KEY idx_clientes_cedula (cedula)
) ENGINE=InnoDB;

-- CATEGORIAS (tabla independiente)
DROP TABLE IF EXISTS categorias;
CREATE TABLE categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL UNIQUE,
  descripcion VARCHAR(255) DEFAULT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- LIBROS
DROP TABLE IF EXISTS libros;
CREATE TABLE libros (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- ID interno DB (no visible)
  titulo VARCHAR(255) NOT NULL,
  volumen VARCHAR(50) DEFAULT NULL,    -- Vol./Pte./No./Tomo/Ejemplar
  isbn VARCHAR(50) DEFAULT NULL,
  clasificacion_dewey VARCHAR(50) DEFAULT NULL,
  autor VARCHAR(255) DEFAULT NULL,
  anio_publicacion INT DEFAULT NULL,
  categoria_id INT UNSIGNED DEFAULT NULL,     -- FK -> categorias
  etiquetas VARCHAR(255) DEFAULT NULL,        -- tags separadas por coma
  cantidad INT UNSIGNED NOT NULL DEFAULT 1,
  estado ENUM('Disponible','Prestado') NOT NULL DEFAULT 'Disponible',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_libros_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  KEY idx_libros_titulo (titulo),
  KEY idx_libros_isbn (isbn),
  KEY idx_libros_dewey (clasificacion_dewey),
  KEY idx_libros_autor (autor)
) ENGINE=InnoDB;

-- TIQUETES (Préstamos)
DROP TABLE IF EXISTS tiquetes;
CREATE TABLE tiquetes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,           -- interno
  codigo VARCHAR(20) NOT NULL UNIQUE,                   -- ej: BBPO-0001
  cliente_id INT UNSIGNED DEFAULT NULL,                 -- FK -> clientes
  libro_id INT UNSIGNED NOT NULL,                       -- FK -> libros
  nombre_cliente VARCHAR(150) DEFAULT NULL,             -- si no se desea guardar cliente
  titulo VARCHAR(255) DEFAULT NULL,                     -- redundante por trazabilidad
  autor VARCHAR(255) DEFAULT NULL,
  signatura VARCHAR(100) DEFAULT NULL,
  fecha_prestamo DATETIME NOT NULL,
  fecha_devolucion DATETIME DEFAULT NULL,
  categoria_edad ENUM(
    'OP','AP',        -- 0 a 5 años
    'O','A',          -- 6 a 12 años
    'HJ','MJ',        -- 13 a 17 años
    'HJU','MJU',      -- 18 a 35 años
    'HA','MA',        -- 36 a 64 años
    'HAM','NAM'       -- 65+ años
  ) NOT NULL,
  estado ENUM('En Prestamo','Atrasado','Devuelto') NOT NULL DEFAULT 'En Prestamo',
  observaciones VARCHAR(255) DEFAULT NULL,
  usuario_registra_id INT UNSIGNED NOT NULL,            -- FK -> usuarios
  telefono VARCHAR(50) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL,
  nombre_biblioteca VARCHAR(150) NOT NULL DEFAULT 'Biblioteca Pública Semioficial de San Rafael de Poás',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_tiquetes_cliente FOREIGN KEY (cliente_id)
    REFERENCES clientes(id) ON UPDATE CASCADE ON DELETE SET NULL,

  CONSTRAINT fk_tiquetes_libro FOREIGN KEY (libro_id)
    REFERENCES libros(id) ON UPDATE CASCADE ON DELETE RESTRICT,

  CONSTRAINT fk_tiquetes_usuario FOREIGN KEY (usuario_registra_id)
    REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- LOGS / AUDITORÍA
DROP TABLE IF EXISTS logs;
CREATE TABLE logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fecha_evento DATETIME NOT NULL,
  usuario_actor VARCHAR(120) NOT NULL,
  rol VARCHAR(50) NOT NULL,
  accion VARCHAR(50) NOT NULL,   -- crear, editar, eliminar, login, logout, reset_password, cambio_estado
  entidad VARCHAR(50) NOT NULL,  -- libro, usuario, tiquete, cliente
  descripcion VARCHAR(255) DEFAULT NULL,
  resultado VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB;
