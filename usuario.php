<?php
// Verificar conexión
session_start();
require_once 'con_db.php';
require_once 'config/database.php';
require_once 'models/Estudiante.php';


// Verificar si el usuario está logueado y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Obtener cédula desde la URL
$cedula = isset($_GET['cedula']) ? $_GET['cedula'] : null;

// Inicializar variables
$id = $nombre = $apellido = $fecha_nacimiento = $lugar_nacimiento = $direccion = $telefono = $email = $clave = $carrera = $fecha_ingreso = $foto_perfil = "";



// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear instancia del modelo Estudiante
$estudiante = new Estudiante($db);

// Obtener datos del estudiante
$datos_estudiante = $estudiante->obtenerDatosEstudiante($_GET['id']);
$materias_inscritas = $estudiante->obtenerMateriasInscritas($_GET['id']);
$historial_academico = $estudiante->obtenerHistorialAcademico($_GET['cedula']);

// Obtener la cédula del estudiante para usarla en la URL
$cedula_estudiante = $datos_estudiante['cedula'];



$id = $datos_estudiante['id'];
$nombre = $datos_estudiante['nombre'];
$apellido = $datos_estudiante['apellido'];
$fecha_nacimiento = $datos_estudiante['fecha_nacimiento'];
$lugar_nacimiento = $datos_estudiante['lugar_nacimiento'];
$direccion = $datos_estudiante['direccion'];
$telefono = $datos_estudiante['telefono'];
$email = $datos_estudiante['email'];
$clave = $datos_estudiante['clave'];
$carrera = $datos_estudiante['carrera'];
$fecha_ingreso = $datos_estudiante['fecha_ingreso'];
$foto_perfil = $datos_estudiante['foto_perfil'];



// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['accion'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $lugar_nacimiento = $_POST['lugar_nacimiento'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $clave = $_POST['clave'];
    $carrera = $_POST['carrera'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $foto_perfil = $_POST['foto_perfil'];

    $sql = "UPDATE estudiantes SET nombre = ?, apellido = ?, fecha_nacimiento = ?, lugar_nacimiento = ?, direccion = ?, telefono = ?, email = ?, clave = ?, carrera = ?, fecha_ingreso = ?, foto_perfil = ? WHERE cedula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $nombre, $apellido, $fecha_nacimiento, $lugar_nacimiento, $direccion, $telefono, $email, $clave, $carrera, $fecha_ingreso, $foto_perfil, $cedula);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Usuario actualizado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar el usuario.</div>";
    }
    $stmt->close();
}

// Procesar la anulación de inscripción
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'anularInscripcion') {
    $inscripcion_id = $_POST['inscripcion_id'];

    $sql = "DELETE FROM inscripciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $inscripcion_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inscripción anulada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al anular la inscripción.</div>";
    }

    $stmt->close();

    // Recargar la página para actualizar la lista de materias inscritas
    header("Location: " . $_SERVER['PHP_SELF'] . "?cedula=" . $cedula . "&id=" . $id);
    exit();
}

$materiasInscritas = [];
if ($id) {
    $sql = "SELECT m.nombre, m.codigo, i.fecha_inscripcion, i.id as inscripcion_id
FROM inscripciones i
INNER JOIN materias m ON i.materia_id = m.id
WHERE i.estudiante_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $materiasInscritas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <style>
        .foto-perfil {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema de Registro de Notas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin_dashboard.php">VOLVER</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">Modificar Usuario</h2>
                        <hr>

                        <?php if ($cedula && $nombre): ?>
                            <div class="text-center mb-4">
                                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de Perfil" class="foto-perfil">
                            </div>

                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fecha_nacimiento); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lugar_nacimiento" class="form-label">Lugar de Nacimiento</label>
                                        <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" value="<?php echo htmlspecialchars($lugar_nacimiento); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="clave" class="form-label">Clave</label>
                                        <input type="password" class="form-control" id="clave" name="clave" value="<?php echo htmlspecialchars($clave); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="carrera" class="form-label">Carrera</label>
                                        <input type="text" class="form-control" id="carrera" name="carrera" value="<?php echo htmlspecialchars($carrera); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                        <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo htmlspecialchars($fecha_ingreso); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="foto_perfil" class="form-label">Foto de Perfil (URL)</label>
                                        <input type="text" class="form-control" id="foto_perfil" name="foto_perfil" value="<?php echo htmlspecialchars($foto_perfil); ?>">
                                    </div>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                        <i class="fas fa-trash-alt me-2"></i>Eliminar Estudiante
                                    </button>
                                </div>
                            </form>

                            <h3 class="mt-4">Materias Inscritas</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Código</th>
                                            <th>Fecha de Inscripción</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($materiasInscritas as $materia): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($materia['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($materia['codigo']); ?></td>
                                                <td><?php echo htmlspecialchars($materia['fecha_inscripcion']); ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="accion" value="anularInscripcion">
                                                        <input type="hidden" name="inscripcion_id" value="<?php echo $materia['inscripcion_id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea anular esta inscripción?');">Anular</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <section id="historial-academico" class="mb-5">
                                <h3>Historial Académico</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre de la Materia</th>

                                                <th>Docente</th>
                                                <th>Nota</th>
                                                <th>Periodo</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($historial_academico as $materia): ?>
                                                <tr>
                                                    <td><?php echo $materia['nombre_materia']; ?></td>

                                                    <td><?php echo $materia['nombre_docente']; ?></td>
                                                    <td><?php echo $materia['nota']; ?></td>
                                                    <td><?php echo $materia['periodo']; ?></td>
                                                    <td><?php echo $materia['fecha_calificacion']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </section>
                        <?php else: ?>
                            <div class="alert alert-warning">No se encontró un usuario con la cédula proporcionada.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Eliminar Estudiante</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar a <strong><?php echo $nombre . ' ' . $apellido; ?></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    
                    <form id="deleteUserForm" method="POST" action="delete-user.php">
                        <input type="hidden" name="action" value="deleteUser">
                        <input type="hidden" name="userType" value="student">
                        <input type="hidden" name="userId" value="<?php echo $id; ?>">
                        
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
