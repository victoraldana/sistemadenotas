# 📚 Sistema de Registro de Notas

Sistema web para la gestión académica de instituciones educativas. Permite administrar estudiantes, docentes, materias, carreras y calificaciones de manera eficiente.

## ✨ Características

### 🔐 Autenticación Multi-rol
- **Administrador**: Gestión completa del sistema
- **Docente**: Gestión de materias y calificaciones
- **Estudiante**: Consulta de notas y perfil

### 👥 Gestión de Usuarios
- Registro de estudiantes con información personal completa
- Registro de docentes con especialidades y experiencia
- Registro de administradores con diferentes puestos

### 📖 Gestión Académica
- Creación y administración de carreras
- Creación y asignación de materias
- Inscripción de estudiantes en materias
- Registro y consulta de calificaciones

### 🔍 Funcionalidades Adicionales
- Búsqueda dinámica en tablas
- Dashboards personalizados por rol
- Interfaz responsiva con Bootstrap 5

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5.3
- **Iconos**: Font Awesome 6

## 📁 Estructura del Proyecto

```
sistema-de-notas/
├── index.php                 # Página de login
├── admin_dashboard.php       # Panel de administrador
├── docente_dashboard.php     # Panel de docente
├── estudiante_dashboard.php  # Panel de estudiante
├── usuario.php               # Detalles del usuario
├── docente.php               # Detalles del docente
├── admin_detalle.php         # Detalles del administrador
├── reasignar_materias.php    # Reasignación de materias
├── actualizar_contacto.php   # Actualización de contacto
├── delete-user.php           # Eliminación de usuarios
├── logout.php                # Cierre de sesión
├── con_db.php                # Conexión a base de datos
├── create_database.sql       # Script de creación de BD
├── config/
│   └── database.php          # Configuración de BD
├── models/
│   ├── Administrador.php     # Modelo de administrador
│   ├── Docente.php           # Modelo de docente
│   ├── Estudiante.php        # Modelo de estudiante
│   ├── Usuario.php           # Modelo de usuario
│   ├── Database.php          # Clase de conexión
│   ├── datos.php             # Funciones de datos
│   └── api.php               # API para peticiones AJAX
└── img/
    └── user.jpg              # Imagen por defecto
```

## ⚙️ Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx, XAMPP, etc.)

### Pasos

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/victoraldana/sistemadenotas.git
   ```

2. **Importar la base de datos**
   ```bash
   mysql -u usuario -p nombre_bd < create_database.sql
   ```

3. **Configurar la conexión a la base de datos**
   
   Editar el archivo `config/database.php` con los datos de tu servidor:
   ```php
   $servername = "localhost";
   $username = "tu_usuario";
   $password = "tu_contraseña";
   $dbname = "nombre_base_datos";
   ```

4. **Acceder al sistema**
   
   Abrir en el navegador: `http://localhost/sistema-de-notas/`

## 👤 Roles y Permisos

| Funcionalidad | Administrador | Docente | Estudiante |
|--------------|:-------------:|:-------:|:----------:|
| Gestionar estudiantes | ✅ | ❌ | ❌ |
| Gestionar docentes | ✅ | ❌ | ❌ |
| Gestionar materias | ✅ | ❌ | ❌ |
| Gestionar carreras | ✅ | ❌ | ❌ |
| Inscribir estudiantes | ✅ | ❌ | ❌ |
| Registrar notas | ✅ | ✅ | ❌ |
| Ver notas | ✅ | ✅ | ✅ |
| Ver perfil | ✅ | ✅ | ✅ |

## 🗄️ Modelo de Base de Datos

El sistema utiliza las siguientes tablas principales:

- **usuarios**: Credenciales de acceso
- **estudiantes**: Información de estudiantes
- **docentes**: Información de docentes
- **administradores**: Información de administradores
- **materias**: Catálogo de materias
- **carreras**: Catálogo de carreras
- **inscripciones**: Relación estudiante-materia
- **notas**: Calificaciones de estudiantes

## 🔒 Seguridad

- Uso de PDO con prepared statements para prevenir SQL Injection
- Control de sesiones PHP
- Validación de roles para acceso a funcionalidades
- Protección de rutas por rol de usuario

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

## 👨‍💻 Autor

Desarrollado por **Victor Aldana**

---

⭐ Si este proyecto te fue útil, ¡no olvides darle una estrella!
