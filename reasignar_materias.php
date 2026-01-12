<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

include_once('con_db.php');

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se proporcionó un ID de docente
if (!isset($_GET['docente_id'])) {
    $_SESSION['error'] = "No se proporcionó un ID de docente";
    header("Location: admin_dashboard.php");
    exit();
}

$docente_id = $_GET['docente_id'];

// Verificar si el docente existe
$sql = "SELECT nombre, apellido FROM docentes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "El docente no existe";
    header("Location: admin_dashboard.php");
    exit();
}

$docente = $result->fetch_assoc();
$nombre_docente = $docente['nombre'] . ' ' . $docente['apellido'];

// Obtener las materias asignadas al docente
$sql = "SELECT id, nombre, codigo FROM materias WHERE docente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$result = $stmt->get_result();
$materias = $result->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de todos los docentes excepto el que se va a eliminar
$sql = "SELECT id, nombre, apellido FROM docentes WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$result = $stmt->get_result();
$docentes = $result->fetch_all(MYSQLI_ASSOC);

// Procesar el formulario de reasignación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reasignar'])) {
    $errores = false;
    
    // Verificar que todas las materias tengan un nuevo docente asignado
    foreach ($materias as $materia) {
        $materia_id = $materia['id'];
        $nuevo_docente_id = $_POST['docente_' . $materia_id] ?? null;
        
        if ($nuevo_docente_id === null || $nuevo_docente_id === '') {
            $errores = true;
            break;
        }
        
        // Actualizar la materia con el nuevo docente
        $sql = "UPDATE materias SET docente_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $nuevo_docente_id, $materia_id);
        
        if (!$stmt->execute()) {
            $errores = true;
            break;
        }
    }
    
    if (!$errores) {
        // Todas las materias han sido reasignadas, proceder con la eliminación del docente
        if (isset($_SESSION['docente_a_eliminar']) && isset($_SESSION['admin_cedula']) && isset($_SESSION['admin_clave'])) {
            $userId = $_SESSION['docente_a_eliminar'];
            $username = $_SESSION['admin_cedula'];
            $password = $_SESSION['admin_clave'];
            
            // Eliminar el docente
            $sql = "DELETE FROM docentes WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            
            if ($stmt->execute()) {
                // Registro de la acción
                $adminId = $_SESSION['id'] ?? 0;
                $accion = "Eliminación de docente con ID: " . $userId;
                $fecha = date("Y-m-d H:i:s");
                
                // Verificar si existe la tabla logs
                $checkTable = $conn->query("SHOW TABLES LIKE 'logs'");
                if ($checkTable->num_rows > 0) {
                    $sql = "INSERT INTO logs (admin_id, accion, fecha) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iss", $adminId, $accion, $fecha);
                    $stmt->execute();
                }
                
                // Limpiar las variables de sesión
                unset($_SESSION['docente_a_eliminar']);
                unset($_SESSION['admin_cedula']);
                unset($_SESSION['admin_clave']);
                
                // Redirigir al dashboard con mensaje de éxito
                $_SESSION['mensaje'] = "Docente eliminado correctamente después de reasignar sus materias";
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Error al eliminar el docente: " . $stmt->error;
            }
        } else {
            $_SESSION['error'] = "Información de sesión incompleta para eliminar el docente";
        }
    } else {
        $_SESSION['error'] = "Error al reasignar las materias";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reasignar Materias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema de Gestión Académica</a>
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
                <a href="docente.php?cedula=<?php echo $docente_id; ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Reasignar Materias antes de Eliminar Docente</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <p>Antes de eliminar al docente <strong><?php echo $nombre_docente; ?></strong>, debe reasignar todas sus materias a otros docentes.</p>

                <?php if (count($materias) > 0): ?>
                    <form method="POST">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Código</th>
                                    <th>Nuevo Docente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materias as $materia): ?>
                                    <tr>
                                        <td><?php echo $materia['nombre']; ?></td>
                                        <td><?php echo $materia['codigo']; ?></td>
                                        <td>
                                            <select class="form-select" name="docente_<?php echo $materia['id']; ?>" required>
                                                <option value="">Seleccione un docente</option>
                                                <?php foreach ($docentes as $docente): ?>
                                                    <option value="<?php echo $docente['id']; ?>">
                                                        <?php echo $docente['nombre'] . ' ' . $docente['apellido']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <a href="docente.php?cedula=<?php echo $docente_id; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" name="reasignar" class="btn btn-danger">
                                <i class="fas fa-save me-2"></i>Reasignar y Eliminar Docente
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        El docente no tiene materias asignadas. Puede proceder con la eliminación.
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <a href="docente.php?cedula=<?php echo $docente_id; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                        <form method="POST" action="delete-user.php">
                            <input type="hidden" name="action" value="deleteUser">
                            <input type="hidden" name="userType" value="teacher">
                            <input type="hidden" name="userId" value="<?php echo $docente_id; ?>">
                            <input type="hidden" name="username" value="<?php echo $_SESSION['admin_cedula'] ?? ''; ?>">
                            <input type="hidden" name="password" value="<?php echo $_SESSION['admin_clave'] ?? ''; ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i>Eliminar Docente
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

