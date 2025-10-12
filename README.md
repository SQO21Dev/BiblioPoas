# 📚 BiblioPoás – Sistema de Gestión Bibliotecaria Comunitaria

Este proyecto es una aplicación web modular desarrollada en **PHP puro** bajo arquitectura **MVC personalizada**, pensada para gestionar bibliotecas comunitarias. Permite manejar usuarios, clientes, libros y préstamos (tiquetes), así como generar reportes y realizar búsquedas dinámicas.

---

## 🧰 Requisitos para ejecutar el proyecto

Antes de ejecutar este sistema, asegúrese de tener lo siguiente:

### 🔧 Software necesario
- **PHP ≥ 8.0**
- **Servidor web local** (recomendado: `php -S localhost:8000`)
- **MySQL / MariaDB**
- **Navegador moderno** (Chrome, Firefox, etc.)
- **Visual Studio Code** u otro editor (opcional)

### 📦 Librerías externas requeridas
El sistema actualmente **no requiere Composer** ni frameworks externos de PHP. Todo el código es modular y funcional de forma independiente.

---

## ⚙️ Instalación y configuración

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu_usuario/BiblioPoas.git
   cd BiblioPoas
   ```

2. **Configurar base de datos**

   - Crear una base de datos llamada `bibliopoas`
   - Importar el archivo `.sql` correspondiente (aún no incluido en este repositorio, asegúrate de tener uno con las tablas `usuarios`, `clientes`, `libros`, `tiquetes`, etc.)

3. **Editar configuración de base de datos**

   Abre el archivo `app/config/config.php` y ajusta tus credenciales:

   ```php
   $host = 'localhost';
   $db = 'bibliopoas';
   $user = 'root';
   $pass = 'root';
   ```

4. **Iniciar servidor local**
   Desde la raíz del proyecto:

   ```bash
   php -S localhost:8000 -t public
   ```

   Luego abre tu navegador y visita: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Acceso al sistema

Asegúrate de tener al menos un usuario registrado en la tabla `usuarios`. Puedes hacerlo manualmente en la base de datos o mediante el formulario de login (si habilitado).

---

## 🗂 Estructura general

```bash
BiblioPoas/
├── app/
│   ├── config/
│   ├── core/
│   ├── controllers/
│   ├── models/
│   └── modules/
│       ├── auth/
│       ├── dashboard/
│       ├── usuarios/
│       ├── clientes/
│       ├── libros/
│       └── tiquetes/
├── public/
│   └── index.php
```

---

## ✅ Funcionalidades actuales

- CRUD de Usuarios, Clientes y Libros
- Gestión de préstamos (Tiquetes)
- Vista principal de Dashboard con resumen de préstamos activos
- Validaciones y mensajes Toast modernos
- Exportación a CSV y Excel

---

## 🚧 En desarrollo

- Módulo completo de historial de tiquetes
- Gráficos e indicadores en el Dashboard
- Logs de auditoría por acción de usuario
- Permisos por rol

---

## 📄 Licencia

Este sistema fue desarrollado como parte de un **Trabajo Comunal Universitario** (TCU). El uso está orientado a fines educativos y comunitarios.
