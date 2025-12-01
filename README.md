# 📚 BiblioPoás  
Sistema de gestión bibliotecaria — Préstamos, libros, clientes y control administrativo.

BiblioPoás es una aplicación web desarrollada en **PHP 8**, **MVC ligero**, **Bootstrap 5**, **MySQL** y **SweetAlert2**, diseñada específicamente para la Biblioteca Pública Semioficial de San Rafael de Poás.  
El sistema permite administrar préstamos (tiquetes), libros, clientes y registro de actividad mediante un módulo completo de auditoría.

---

## 🚀 Características principales

### ✔ Gestión de préstamos (Tiquetes)
- Crear, editar y cerrar préstamos.
- Validación automática del estado del libro (Disponible / Prestado).
- Actualización rápida desde el Dashboard mediante modal.
- Control de fechas de préstamo y devolución.
- Categorización por edad según formatos de la biblioteca.
- Observaciones y datos del cliente integrados.

### ✔ Gestión de libros
- Registro completo: título, autor, signatura, códigos, editorial, etc.
- Control de estado (Disponible / Prestado).
- Búsqueda y filtros.

### ✔ Gestión de clientes
- Datos personales, teléfono y dirección.
- Autocompletado para creación rápida de tiquetes.

### ✔ Dashboard avanzado
- KPIs automáticos.
- Lista de tiquetes activos y vencidos.
- Exportación CSV y XLSX.
- Modal rápido para editar la fecha de vencimiento o cerrar el tiquete.

### ✔ Auditoría (Logs)
- Registro automático de acciones:
  - Crear / editar / eliminar libros
  - Crear / editar / eliminar clientes
  - Crear / cerrar / actualizar tiquetes
  - Login y logout
- Incluye usuario, rol, fecha y descripción del evento.

---

## 🛠 Tecnologías utilizadas

- **PHP 8.1+** (servidor embebido o Apache)
- **MySQL / MariaDB**
- **HTML5 + CSS3**
- **Bootstrap 5**
- **JavaScript (vanilla)**
- **SweetAlert2**
- **MVC ligero escrito a mano**
- **Zorin OS / Linux Mint / Windows compatible**

---

## 🔧 Requisitos

- PHP 8.1+
- Extensión `pdo_mysql`
- MySQL 5.7+ o MariaDB 10+
- Composer (opcional)
- Apache o PHP built-in server

---

## ▶ Cómo ejecutarlo (modo rápido)

```bash
php -S localhost:8000 -t public
