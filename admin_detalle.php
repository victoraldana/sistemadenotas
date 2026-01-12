<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

include_once('models/datos.php');

// Verificar si se proporcionó un ID
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$adminId = $_GET['id'];

// Obtener los datos del administrador
$admin = null;
foreach ($administradores as $a) {
    if ($a['id'] == $adminId) {
        $admin = $a;
        break;
    }
}

// Si no se encuentra el administrador, redirigir
if ($admin === null) {
    header("Location: admin_dashboard.php");
    exit();
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizarAdmin') {
    // Aquí iría el código para actualizar los datos del administrador en la base de datos
    // Por ejemplo:
    // $nombre = $_POST['nombre'];
    // $apellido = $_POST['apellido'];
    // $telefono = $_POST['telefono'];
    // $puesto = $_POST['puesto'];

    // Si se proporcionó una nueva clave
    // if (!empty($_POST['clave'])) {
    //     $clave = $_POST['clave'];
    //     // Actualizar la clave
    // }

    // Actualizar los datos en la base de datos

    // Redirigir de vuelta a la página de administradores
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Administrador - Sistema de Registro de Notas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!--barra de navegacion-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema de Registro de Notas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Inicio</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    Bienvenido, <?php echo $_SESSION['usuario']; ?> |
                    <a href="logout.php" class="text-white">Cerrar sesión</a>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="admin_dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Panel
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Detalles del Administrador</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="accion" value="actualizarAdmin">
                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id" class="form-label">ID</label>
                            <input type="text" class="form-control" id="id" value="<?php echo $admin['id']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo $admin['cedula']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $admin['nombre']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $admin['apellido']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $admin['telefono']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="puesto" class="form-label">Puesto</label>
                            <select class="form-select" id="puesto" name="puesto" required>
                                <option value="">Seleccione un puesto</option>
                                <option value="Director" <?php echo ($admin['puesto'] === 'Director') ? 'selected' : ''; ?>>Director</option>
                                <option value="Coordinador" <?php echo ($admin['puesto'] === 'Coordinador') ? 'selected' : ''; ?>>Coordinador</option>
                                <option value="Asistente" <?php echo ($admin['puesto'] === 'Asistente') ? 'selected' : ''; ?>>Asistente</option>
                                <option value="Secretaria" <?php echo ($admin['puesto'] === 'Secretaria') ? 'selected' : ''; ?>>Secretaria</option>
                                <option value="obrero" <?php echo ($admin['puesto'] === 'obrero') ? 'selected' : ''; ?>>Personal Obrero</option>
                                <option value="Administrativo" <?php echo ($admin['puesto'] === 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="clave" class="form-label">Nueva Clave (dejar en blanco para mantener la actual)</label>
                            <input type="password" class="form-control" id="clave" name="clave">
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="button" class="btn btn-danger me-md-2" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="fas fa-trash-alt me-2"></i>Eliminar Administrador
                        </button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Eliminar Administrador</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar a <strong><?php echo $admin['nombre'] . ' ' . $admin['apellido']; ?></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    
                    <form id="deleteUserForm" method="POST" action="delete-user.php">
                        <input type="hidden" name="action" value="deleteUser">
                        <input type="hidden" name="userType" value="admin">
                        <input type="hidden" name="userId" value="<?php echo $admin['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteUserForm').submit();">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

