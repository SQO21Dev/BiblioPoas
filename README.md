# 📚 BiblioPoás  
Sistema de gestión bibliotecaria — Préstamos, libros, clientes y control administrativo.

**BiblioPoás** es una aplicación web desarrollada en **PHP 8 (MVC ligero)**, **MySQL**, **Bootstrap 5** y **JavaScript**, diseñada específicamente para la **Biblioteca Pública Semioficial de San Rafael de Poás**.

El sistema permite administrar **préstamos (tiquetes)**, **libros**, **clientes**, visualizar métricas en tiempo real mediante un **Dashboard**, y mantener un control administrativo claro y ordenado.

La aplicación puede ejecutarse como una **app tipo escritorio** en **Windows y Linux**, usando el servidor embebido de PHP.

---

## 🚀 Características principales

### ✔ Gestión de préstamos (Tiquetes)
- Crear, editar, cerrar y eliminar tiquetes.
- Control automático del estado del libro:
  - **Disponible**
  - **Prestado**
  - **Retrasado**
  - **Devuelto**
- Validación para evitar préstamos duplicados del mismo libro.
- Fechas de préstamo y devolución con validación.
- Categorización por edad (OP, AP, O, A, HJ, MJ, etc.).
- Observaciones y datos de contacto del cliente.
- Actualización rápida desde el Dashboard (modal).

---

### ✔ Gestión de libros
- Registro completo de libros:
  - Título
  - Autor
  - Volumen
  - ISBN
  - Clasificación Dewey
  - Categoría
  - Cantidad
- Control de estado automático (Disponible / Prestado).
- Listado optimizado y ordenado.

---

### ✔ Gestión de clientes
- Registro de clientes con:
  - Nombre
  - Teléfono
  - Dirección
- Autocompletado en formularios de tiquetes.

---

### ✔ Dashboard interactivo
- KPIs automáticos:
  - Total de libros
  - Tiquetes activos
  - Clientes
  - Tiquetes vencidos
- Gráficos dinámicos (Chart.js):
  - Distribución por categoría de edad
  - Tiquetes por estado
- Filtros por rango de fechas.
- Actualización automática de datos al abrir la vista.
- Tabla de tiquetes críticos (activos y vencidos).

---

### ✔ Exportaciones
- Exportación de tiquetes a:
  - **CSV**
  - **Excel (XLSX básico)**
- Respeta filtros de fecha aplicados.

---

### ✔ Seguridad y control
- Protección CSRF en formularios.
- Validación estricta de datos.
- Manejo correcto de estados ENUM.
- Código organizado bajo arquitectura MVC ligera.

---

## 🛠 Tecnologías utilizadas

- **PHP 8.1+**
- **MySQL / MariaDB**
- **PDO (PDO_MYSQL)**
- **HTML5 / CSS3**
- **Bootstrap 5**
- **JavaScript (Vanilla)**
- **SweetAlert2**
- **Chart.js**
- **MVC ligero (custom)**
- Compatible con **Windows** y **Linux (Zorin OS, Mint, Ubuntu)**

---

## 🔧 Requisitos

- PHP 8.1 o superior
- Extensión PHP: `pdo_mysql`
- MySQL 5.7+ o MariaDB 10+
- Navegador moderno (Chrome, Edge, Firefox)
- (Opcional) XAMPP o similar para MySQL

---

## ▶ Ejecución rápida (modo desarrollo)

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t public
