<?php
session_start();
require_once 'config/database.php';
require_once 'models/Estudiante.php';

// Verificar si el usuario está logueado y es estudiante
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: login.php");
    exit();
}

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear instancia del modelo Estudiante
$estudiante = new Estudiante($db);

// Obtener datos del estudiante
$datos_estudiante = $estudiante->obtenerDatosEstudiante($_SESSION['id']);
$materias_inscritas = $estudiante->obtenerMateriasInscritas($_SESSION['id']);
$historial_academico = $estudiante->obtenerHistorialAcademico($_SESSION['cedula']);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante - Sistema de Registro de Notas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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
                        <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#mis-materias">Mis Materias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#historial-academico">Historial Académico</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#datos-personales">Datos Personales</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    Bienvenido, <?php echo $datos_estudiante['nombre']; ?> |
                    <a href="logout.php" class="text-white">Cerrar sesión</a>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Panel de Estudiante</h2>

        <section id="datos-personales" class="mb-5">
            <h3>Datos Personales</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <img src="<?php echo $datos_estudiante['foto_perfil']; ?>" alt="Foto de perfil" class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                            <h4><?php echo $datos_estudiante['nombre'] . ' ' . $datos_estudiante['apellido']; ?></h4>
                            <p class="text-muted"><?php echo $datos_estudiante['carrera']; ?></p>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-id-card"></i> Cédula:</strong> <?php echo $datos_estudiante['cedula']; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento:</strong> <?php echo $datos_estudiante['fecha_nacimiento']; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-map-marker-alt"></i> Lugar de Nacimiento:</strong> <?php echo $datos_estudiante['lugar_nacimiento']; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-calendar-check"></i> Fecha de Ingreso:</strong> <?php echo $datos_estudiante['fecha_ingreso']; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-envelope"></i> Email:</strong> <span data-campo="email"><?php echo $datos_estudiante['email']; ?></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-phone"></i> Teléfono:</strong> <span data-campo="telefono"><?php echo $datos_estudiante['telefono']; ?></span>
                                </div>
                                <div class="col-12 mb-3">
                                    <strong><i class="fas fa-home"></i> Dirección:</strong> <span data-campo="direccion"><?php echo $datos_estudiante['direccion']; ?></span>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#editarDatosModal">
                                <i class="fas fa-edit"></i> Editar Datos de Contacto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="mis-materias" class="mb-5">
            <h3>Materias Inscritas y Notas</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre de la Materia</th>
                            <th>Código</th>
                            <th>Docente</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materias_inscritas as $materia): ?>
                            <tr>
                                <td><?php echo $materia['nombre']; ?></td>
                                <td><?php echo $materia['codigo']; ?></td>
                                <td><?php echo $materia['docente']; ?></td>
                                <td><?php echo $materia['nota']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

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


    </div>

    <!-- Modal para editar datos de contacto -->
    <div class="modal fade" id="editarDatosModal" tabindex="-1" aria-labelledby="editarDatosModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDatosModalLabel">Editar Datos de Contacto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarDatos">
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" value="<?php echo $datos_estudiante['email']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editTelefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="editTelefono" value="<?php echo $datos_estudiante['telefono']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editDireccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="editDireccion" value="<?php echo $datos_estudiante['direccion']; ?>">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDatosContacto()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // Función para guardar datos de contacto
        function guardarDatosContacto() {
            const nuevoEmail = document.getElementById('editEmail').value;
            const nuevoTelefono = document.getElementById('editTelefono').value;
            const nuevaDireccion = document.getElementById('editDireccion').value;

            // Aquí iría la lógica para enviar los datos actualizados al servidor
            // Por ahora, solo mostraremos un mensaje de éxito
            alert('Datos de contacto actualizados correctamente');

            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarDatosModal'));
            modal.hide();

            // Actualizar los datos en la página (en una aplicación real, esto se haría después de confirmar que los datos se guardaron correctamente en el servidor)
            document.querySelector('#datos-personales [data-campo="email"]').textContent = nuevoEmail;
            document.querySelector('#datos-personales [data-campo="telefono"]').textContent = nuevoTelefono;
            document.querySelector('#datos-personales [data-campo="direccion"]').textContent = nuevaDireccion;
        }
    </script>
</body>

</html>