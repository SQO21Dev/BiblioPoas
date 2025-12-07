-- Usuario admin por defecto para pruebas:
-- usuario: admin
-- contraseña: admin123  (recomendable cambiarla luego)

INSERT INTO usuarios (usuario, nombre, correo, password_hash, rol, estado)
VALUES (
  'admin',
  'Administrador',
  'admin@biblio-poas.local',
  '$2y$10$9hSg0s5c5XQj7GZ7l6OQmu2xw7WzEw3kUuD2r7x6mI6i6b2qD2c9e',
  'admin',
  'activo'
);
