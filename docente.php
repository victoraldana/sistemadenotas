<?php
include_once("con_db.php");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener cédula desde la URL
$IdCedula = isset($_GET['cedula']) ? $_GET['cedula'] : null;

// Inicializar variables
$id = $cedula = $clave = $nombre = $apellido = $fecha_nacimiento = $especialidad = $anos_servicio = $horas_trabajo = $telefono = $foto_perfil = $usuario_id = $lugar_nacimiento = $direccion = $email = $fecha_ingreso = "";

// Si se pasa una cédula, buscar el docente
if ($IdCedula) {
    $sql = "SELECT * FROM docentes WHERE cedula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $IdCedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $docente = $result->fetch_assoc();
        $id = $docente['id'];
        $cedula = $docente['cedula'];
        $clave = $docente['clave'];
        $nombre = $docente['nombre'];
        $apellido = $docente['apellido'];
        $fecha_nacimiento = $docente['fecha_nacimiento'];
        $especialidad = $docente['especialidad'];
        $anos_servicio = $docente['anos_servicio'];
        $horas_trabajo = $docente['horas_trabajo'];
        $telefono = $docente['telefono'];
        $foto_perfil = $docente['foto_perfil'];
        $usuario_id = $docente['usuario_id'];
        $lugar_nacimiento = $docente['lugar_nacimiento'];
        $direccion = $docente['direccion'];
        $email = $docente['email'];
        $fecha_ingreso = $docente['fecha_ingreso'];
    } else {
        echo "<div class='alert alert-danger'>Docente no encontrado. Filas devueltas: " . $result->num_rows . "</div>";
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-warning'>No se proporcionó una cédula.</div>";
}

// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['accion'])) {
    // Función para sanitizar la entrada
    function sanitize_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Sanitizar y validar entradas
    $cedula = sanitize_input($_POST['cedula']);
    $clave = $_POST['clave'];
    $nombre = sanitize_input($_POST['nombre']);
    $apellido = sanitize_input($_POST['apellido']);
    $fecha_nacimiento = sanitize_input($_POST['fecha_nacimiento']);
    $especialidad = sanitize_input($_POST['especialidad']);
    $anos_servicio = filter_var($_POST['anos_servicio'], FILTER_VALIDATE_INT);
    $horas_trabajo = filter_var($_POST['horas_trabajo'], FILTER_VALIDATE_INT);
    $telefono = sanitize_input($_POST['telefono']);
    $lugar_nacimiento = sanitize_input($_POST['lugar_nacimiento']);
    $direccion = sanitize_input($_POST['direccion']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $fecha_ingreso = sanitize_input($_POST['fecha_ingreso']);

    // Validar que todos los campos requeridos estén presentes
    if (
        empty($cedula) || empty($nombre) || empty($apellido) || empty($fecha_nacimiento) ||
        empty($especialidad) || $anos_servicio === false || $horas_trabajo === false ||
        empty($telefono) || empty($lugar_nacimiento) || empty($direccion) ||
        $email === false || empty($fecha_ingreso)
    ) {
        echo "<div class='alert alert-danger'>Por favor, complete todos los campos correctamente.</div>";
    } else {
        $sql = "UPDATE docentes SET clave = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, 
        especialidad = ?, anos_servicio = ?, horas_trabajo = ?, telefono = ?, 
        lugar_nacimiento = ?, direccion = ?, email = ?, fecha_ingreso = ? WHERE cedula = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(
                "sssssiiisssss",
                $clave,
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $especialidad,
                $anos_servicio,
                $horas_trabajo,
                $telefono,
                $lugar_nacimiento,
                $direccion,
                $email,
                $fecha_ingreso,
                $cedula
            );

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Docente actualizado correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al actualizar el docente: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Error en la preparación de la consulta: " . $conn->error . "</div>";
        }
    }
}
// Procesar cambio de profesor en materia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'cambiarProfesor') {
    $materia_id = $_POST['materia_id'];
    $nuevo_docente_id = $_POST['nuevo_docente_id'];

    $sql = "UPDATE materias SET docente_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nuevo_docente_id, $materia_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Profesor cambiado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al cambiar el profesor.</div>";
    }

    $stmt->close();
}

// Obtener materias asignadas al docente
$materiasAsignadas = [];
if ($id) {
    $sql = "SELECT id, nombre, codigo FROM materias WHERE docente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $materiasAsignadas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Verificar si el docente tiene materias asignadas
$tieneMaterias = count($materiasAsignadas) > 0;

// Obtener lista de todos los docentes para el cambio de profesor
$todosDocentes = [];
$sql = "SELECT id, nombre, apellido FROM docentes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $todosDocentes[] = $row;
    }
}

// Mostrar mensaje de error si existe
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

// Mostrar mensaje de éxito si existe
if (isset($_SESSION['mensaje'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['mensaje'] . "</div>";
    unset($_SESSION['mensaje']);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Docente</title>
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
            <a class="navbar-brand" href="#">Sistema de Gestión de Docentes</a>
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
                        <h2 class="text-center">Gestionar Docente</h2>
                        <hr>

                        <?php if ($cedula !== null && $cedula !== '' && $nombre): ?>
                            <div class="text-center mb-4">
                                <img src="<?php echo $foto_perfil; ?>" alt="Foto de Perfil" class="foto-perfil">
                            </div>

                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="cedula" class="form-label">Cédula</label>
                                        <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo $cedula; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="clave" class="form-label">Clave</label>
                                        <input type="text" class="form-control" id="clave" name="clave" value="<?php echo $clave; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="especialidad" class="form-label">Especialidad</label>
                                        <input type="text" class="form-control" id="especialidad" name="especialidad" value="<?php echo $especialidad; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="anos_servicio" class="form-label">Años de Servicio</label>
                                        <input type="number" class="form-control" id="anos_servicio" name="anos_servicio" value="<?php echo $anos_servicio; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="horas_trabajo" class="form-label">Horas de Trabajo</label>
                                        <input type="number" class="form-control" id="horas_trabajo" name="horas_trabajo" value="<?php echo $horas_trabajo; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lugar_nacimiento" class="form-label">Lugar de Nacimiento</label>
                                        <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" value="<?php echo $lugar_nacimiento; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                        <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo $fecha_ingreso; ?>" required>
                                    </div>

                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    
                                    <?php if (!$tieneMaterias): ?>
                                        <!-- Mostrar el botón de eliminar solo si el docente no tiene materias asignadas -->
                                        <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                            <i class="fas fa-trash-alt me-2"></i>Eliminar Docente
                                        </button>
                                    <?php else: ?>
                                        <!-- Mostrar un botón deshabilitado con un tooltip si el docente tiene materias -->
                                        <button type="button" class="btn btn-danger mt-3" disabled data-bs-toggle="tooltip" data-bs-placement="top" 
                                                title="No se puede eliminar el docente porque tiene materias asignadas. Reasigne las materias primero.">
                                            <i class="fas fa-trash-alt me-2"></i>Eliminar Docente
                                        </button>
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            No se puede eliminar el docente porque tiene materias asignadas. Debe reasignar todas las materias a otros docentes primero.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </form>

                            <h3 class="mt-4">Materias Asignadas</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Código</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($materiasAsignadas) > 0): ?>
                                            <?php foreach ($materiasAsignadas as $materia): ?>
                                                <tr>
                                                    <td><?php echo $materia['nombre']; ?></td>
                                                    <td><?php echo $materia['codigo']; ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cambiarProfesorModal<?php echo $materia['id']; ?>">
                                                            Cambiar Profesor
                                                        </button>

                                                        <!-- Modal para cambiar profesor -->
                                                        <div class="modal fade" id="cambiarProfesorModal<?php echo $materia['id']; ?>" tabindex="-1" aria-labelledby="cambiarProfesorModalLabel<?php echo $materia['id']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="cambiarProfesorModalLabel<?php echo $materia['id']; ?>">Cambiar Profesor para <?php echo $materia['nombre']; ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form method="POST">
                                                                            <input type="hidden" name="accion" value="cambiarProfesor">
                                                                            <input type="hidden" name="materia_id" value="<?php echo $materia['id']; ?>">
                                                                            <div class="mb-3">
                                                                                <label for="nuevo_docente_id" class="form-label">Seleccionar Nuevo Profesor</label>
                                                                                <select class="form-select" id="nuevo_docente_id" name="nuevo_docente_id" required>
                                                                                    <?php foreach ($todosDocentes as $docente): ?>
                                                                                        <option value="<?php echo $docente['id']; ?>"><?php echo $docente['nombre'] . ' ' . $docente['apellido']; ?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">El docente no tiene materias asignadas</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                No se encontró un docente con la cédula proporcionada o la cédula no es válida.
                                <br>Asegúrese de que la cédula sea correcta y que exista en la base de datos.
                            </div>
                            <form action="" method="GET" class="mt-3">
                                <div class="mb-3">
                                    <label for="cedula" class="form-label">Ingrese la cédula del docente:</label>
                                    <input type="text" class="form-control" id="cedula" name="cedula" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Buscar Docente</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación (solo se mostrará si el docente no tiene materias) -->
    <?php if (!$tieneMaterias): ?>
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Eliminar Docente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar a <strong><?php echo $nombre . ' ' . $apellido; ?></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    
                    <form id="deleteUserForm" method="POST" action="delete-user.php">
                        <input type="hidden" name="action" value="deleteUser">
                        <input type="hidden" name="userType" value="teacher">
                        <input type="hidden" name="userId" value="<?php echo $id; ?>">
                        <input type="hidden" name="cedula" value="<?php echo $cedula; ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Cédula del Administrador</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña del Administrador</label>
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
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar los tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>

</html>



