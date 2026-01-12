<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}


include_once('models/datos.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Sistema de Registro de Notas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


</head>

<body>
    <!--barra e navegacion-->
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
                </ul>
                <span class="navbar-text">
                    Bienvenido, <?php echo $_SESSION['usuario']; ?> |
                    <a href="logout.php" class="text-white">Cerrar sesión</a>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Panel de Administrador</h2>

        <div class="row mb-4">
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#agregarCarreraModal">
                    <i class="fas fa-graduation-cap me-2"></i>Agregar Carrera
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#agregarEstudianteModal">
                    <i class="fas fa-user-graduate me-2"></i>Agregar Estudiante
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#agregarDocenteModal">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Agregar Docente
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#agregarMateriaModal">
                    <i class="fas fa-book me-2"></i>Agregar Materia
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#inscribirEstudianteModal">
                    <i class="fas fa-user-plus me-2"></i>Inscribir Estudiante
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#agregarAdministradorModal">
                    <i class="fas fa-user-cog me-2"></i>Agregar Administrador
                </button>
            </div>
        </div>

        <!-- Buscador de Estudiantes -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchEstudiantes" placeholder="Buscar Estudiante...">
        </div>

        <!-- Tabla de Estudiantes -->
        <h3>Estudiantes</h3>
        <table class="table table-striped" id="tablaEstudiantes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Carrera</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?php echo $estudiante['cedula']; ?></td>
                        <td><?php echo $estudiante['nombre']; ?></td>
                        <td><?php echo $estudiante['apellido']; ?></td>
                        <td><?php echo $estudiante['email']; ?></td>
                        <td><?php echo $estudiante['fecha_nacimiento']; ?></td>
                        <td><?php echo $estudiante['carrera']; ?></td>
                        <td><?php echo $estudiante['telefono']; ?></td>
                        <td><a class="btn btn-warning" href="usuario.php?cedula=<?php echo $estudiante['cedula']; ?>&id=<?php echo $estudiante['id']; ?>">VER</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Buscador de Docentes -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchDocentes" placeholder="Buscar Docente...">
        </div>

        <!-- Tabla de Docentes -->
        <h3>Docentes</h3>
        <table class="table table-striped" id="tablaDocentes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Especialidad</th>
                    <th>Telefono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docentes as $docente): ?>
                    <tr>
                        <td><?php echo $docente['cedula']; ?></td>
                        <td><?php echo $docente['nombre']; ?></td>
                        <td><?php echo $docente['apellido']; ?></td>
                        <td><?php echo $docente['email']; ?></td>
                        <td><?php echo $docente['especialidad']; ?></td>
                        <td><?php echo $docente['telefono']; ?></td>
                        <td><a class="btn btn-warning" href="docente.php?cedula=<?php echo $docente['cedula']; ?>">VER</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Buscador de Materias -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchMaterias" placeholder="Buscar Materia...">
        </div>

        <!-- Tabla de Materias -->
        <h3>Materias</h3>
        <table class="table table-striped" id="tablaMaterias">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Docente</th>
                    <th>Carrera</th>
                    <th>Acciones</th> <!-- Nueva columna para el botón -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $materia): ?>
                    <tr>
                        <td><?php echo $materia['id']; ?></td>
                        <td><?php echo $materia['nombre']; ?></td>
                        <td><?php echo $materia['codigo']; ?></td>
                        <td><?php echo $materia['nombre_docente']; ?></td>
                        <td><?php echo $materia['nombre_carrera']; ?></td>
                        <td>
                            <!-- Botón para mostrar/ocultar estudiantes inscritos -->
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                                data-bs-target="#estudiantes-<?php echo $materia['id']; ?>"
                                aria-expanded="false" aria-controls="estudiantes-<?php echo $materia['id']; ?>">
                                Ver Estudiantes
                            </button>
                        </td>
                    </tr>
                    <!-- Fila de collapse para mostrar estudiantes inscritos -->
                    <tr class="collapse" id="estudiantes-<?php echo $materia['id']; ?>">
                        <td colspan="6">
                            <strong>Estudiantes inscritos:</strong>
                            <?php if (!empty($materia['estudiantes_inscritos'])): ?>
                                <ul>
                                    <?php
                                    // Convertir la cadena de estudiantes en un array
                                    $estudiantes = explode(', ', $materia['estudiantes_inscritos']);
                                    foreach ($estudiantes as $estudiante): ?>
                                        <li><?php echo $estudiante; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No hay estudiantes inscritos.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Buscador de Administradores -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchAdmins" placeholder="Buscar Administrador...">
        </div>

        <!-- Tabla de Administradores -->
        <h3>Administradores</h3>
        <table class="table table-striped" id="tablaAdmins">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Teléfono</th>
                    <th>Puesto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($administradores as $admin): ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td><?php echo $admin['cedula']; ?></td>
                        <td><?php echo $admin['nombre']; ?></td>
                        <td><?php echo $admin['apellido']; ?></td>
                        <td><?php echo $admin['telefono']; ?></td>
                        <td><?php echo $admin['puesto']; ?></td>
                        <td><a class="btn btn-warning" href="admin_detalle.php?id=<?php echo $admin['id']; ?>">VER</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal para Agregar Estudiante -->
        <div class="modal fade" id="agregarEstudianteModal" tabindex="-1" aria-labelledby="agregarEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="agregarEstudianteModalLabel">Agregar Estudiante</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="accion" value="agregarEstudiante">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="cedulaEstudiante" class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="cedulaEstudiante" name="cedula" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nombreEstudiante" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreEstudiante" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidoEstudiante" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellidoEstudiante" name="apellido" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fechaNacimientoEstudiante" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fechaNacimientoEstudiante" name="fechaNacimiento" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lugarNacimientoEstudiante" class="form-label">Lugar de Nacimiento</label>
                                    <input type="text" class="form-control" id="lugarNacimientoEstudiante" name="lugarNacimiento" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="direccionEstudiante" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionEstudiante" name="direccion" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefonoEstudiante" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefonoEstudiante" name="telefono" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="emailEstudiante" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailEstudiante" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="claveEstudiante" class="form-label">Clave</label>
                                    <input type="password" class="form-control" id="claveEstudiante" name="clave" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="carreraMateria" class="form-label">Carrera</label>
                                    <select class="form-select" id="carreraMateria" name="carreraId" required>
                                        <?php foreach ($carreras as $carrera): ?>
                                            <option value="<?php echo $carrera['nombre']; ?>"><?php echo $carrera['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="fechaIngresoEstudiante" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="fechaIngresoEstudiante" name="fechaIngreso" required>
                                </div>

                                <div class="col-12">
                                    <label for="fotoPerfilEstudiante" class="form-label">Foto de Perfil</label>

                                    <div id="vistaPreviaFotoPerfil" style="margin-top: 10px;">
                                        <img src="img/user.jpg" alt="Vista previa de la foto de perfil" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Agregar Docente -->
        <div class="modal fade" id="agregarDocenteModal" tabindex="-1" aria-labelledby="agregarDocenteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="agregarDocenteModalLabel">Agregar Docente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="agregarDocente">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="cedulaDocente" class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="cedulaDocente" name="cedula" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nombreDocente" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreDocente" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidoDocente" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellidoDocente" name="apellido" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fechaNacimientoDocente" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fechaNacimientoDocente" name="fechaNacimiento" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lugarNacimientoDocente" class="form-label">Lugar de Nacimiento</label>
                                    <input type="text" class="form-control" id="lugarNacimientoDocente" name="lugarNacimiento" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="direccionDocente" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionDocente" name="direccion" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefonoDocente" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefonoDocente" name="telefono" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="emailDocente" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailDocente" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="claveDocente" class="form-label">Clave</label>
                                    <input type="password" class="form-control" id="claveDocente" name="clave" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="especialidadDocente" class="form-label">Especialidad</label>
                                    <input type="text" class="form-control" id="especialidadDocente" name="especialidad" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="tituloDocente" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="tituloDocente" name="titulo" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="experienciaDocente" class="form-label">Experiencia</label>
                                    <input type="text" class="form-control" id="experienciaDocente" name="experiencia" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fechaIngresoDocente" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="fechaIngresoDocente" name="fechaIngreso" required>
                                </div>

                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal para Agregar Materia -->
        <div class="modal fade" id="agregarMateriaModal" tabindex="-1" aria-labelledby="agregarMateriaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarMateriaModalLabel">Agregar Materia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="agregarMateria">
                            <div class="mb-3">
                                <label for="nombreMateria" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombreMateria" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="codigoMateria" class="form-label">Código</label>
                                <input type="text" class="form-control" id="codigoMateria" name="codigo" required>
                            </div>
                            <div class="mb-3">
                                <label for="docenteMateria" class="form-label">Docente</label>
                                <select class="form-select" id="docenteMateria" name="docenteId" required>
                                    <?php foreach ($docentes as $docente): ?>
                                        <option value="<?php echo $docente['id']; ?>"><?php echo $docente['nombre'] . ' ' . $docente['apellido']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="carreraMateria" class="form-label">Carrera</label>
                                <select class="form-select" id="carreraMateria" name="carreraId" required>
                                    <?php foreach ($carreras as $carrera): ?>
                                        <option value="<?php echo $carrera['id']; ?>"><?php echo $carrera['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para INSCRIPCION DEL ESTUDIANTE -->
        <div class="modal fade" id="inscribirEstudianteModal" tabindex="-1" aria-labelledby="inscribirEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inscribirEstudianteModalLabel">Inscribir Estudiante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="inscribirEstudiante">

                            <div class="mb-3">
                                <label for="cedulaInscripcion" class="form-label">Cédula</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cedulaInscripcion" name="cedula" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="buscarEstudiante()">Buscar</button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="estudianteInscripcion" class="form-label">Estudiante</label>
                                <select class="form-select" id="estudianteInscripcion" name="estudianteId" required>
                                    <option value="">Seleccione un estudiante</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="materiaInscripcion" class="form-label">Materia</label>
                                <select class="form-select" id="materiaInscripcion" name="materiaId" required>
                                    <?php foreach ($materias as $materia): ?>
                                        <option value="<?php echo $materia['id']; ?>"><?php echo $materia['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Inscribir</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para Agregar Carreas -->
        <div class="modal fade" id="agregarCarreraModal" tabindex="-1" aria-labelledby="agregarCarreraModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarCarreraModalLabel">Agregar Carrera</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="agregarCarrera">
                            <div class="mb-3">
                                <label for="nombreCarrera" class="form-label">Nombre de la Carrera</label>
                                <input type="text" class="form-control" id="nombreCarrera" name="nombreCarrera" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Agregar Administrador -->
        <div class="modal fade" id="agregarAdministradorModal" tabindex="-1" aria-labelledby="agregarAdministradorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="agregarAdministradorModalLabel">Agregar Administrador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="agregarAdministrador">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="cedulaAdmin" class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="cedulaAdmin" name="cedula" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nombreAdmin" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreAdmin" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidoAdmin" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellidoAdmin" name="apellido" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefonoAdmin" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefonoAdmin" name="telefono" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="claveAdmin" class="form-label">Clave</label>
                                    <input type="password" class="form-control" id="claveAdmin" name="clave" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="puestoAdmin" class="form-label">Puesto</label>
                                    <select class="form-select" id="puestoAdmin" name="puesto" required>
                                        <option value="">Seleccione un puesto</option>
                                        <option value="Director">Director</option>
                                        <option value="Coordinador">Coordinador</option>
                                        <option value="Asistente">Asistente</option>
                                        <option value="Secretaria">Secretaria</option>
                                        <option value="Obrero">Personal Obrero</option>
                                        <option value="Administrativo">Administrativo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    function buscarEstudiante() {
        const cedula = document.getElementById('cedulaInscripcion').value;
        const selectEstudiante = document.getElementById('estudianteInscripcion');

        fetch(`models/api.php?accion=buscarEstudiante&cedula=${cedula}`)
            .then(response => response.json())
            .then(data => {
                selectEstudiante.innerHTML = '<option value="">Seleccione un estudiante</option>'; // Limpia y agrega la opción por defecto

                if (data.length > 0) {
                    data.forEach(estudiante => {
                        const option = document.createElement('option');
                        option.value = estudiante.id;
                        option.text = `${estudiante.nombre} ${estudiante.apellido}`;
                        selectEstudiante.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.text = 'No se encontraron estudiantes con esa cédula';
                    selectEstudiante.innerHTML = "";
                    selectEstudiante.innerHTML = option;
                }
            })
            .catch(error => {
                console.error('Error al buscar estudiante:', error);
                alert('Error al buscar estudiante. Inténtalo de nuevo más tarde.');
            });
    }

    function mostrarVistaPrevia(input) {
        if (input.files && input.files[0]) {
            var lector = new FileReader();

            lector.onload = function(e) {
                var vistaPrevia = document.getElementById('vistaPreviaFotoPerfil');
                vistaPrevia.innerHTML = '<img src="' + e.target.result + '" alt="Vista previa de la foto de perfil" style="max-width: 200px; max-height: 200px;">';
            }

            lector.readAsDataURL(input.files[0]);
        }
    }

    // Función para buscar en las tablas
    function buscarEnTabla(inputId, tablaId) {
        const searchText = document.getElementById(inputId).value.toLowerCase();
        const tabla = document.getElementById(tablaId);
        const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < filas.length; i++) {
            let mostrarFila = false;
            const celdas = filas[i].getElementsByTagName('td');

            for (let j = 0; j < celdas.length; j++) {
                const textoCelda = celdas[j].textContent || celdas[j].innerText;
                if (textoCelda.toLowerCase().indexOf(searchText) > -1) {
                    mostrarFila = true;
                    break;
                }
            }

            filas[i].style.display = mostrarFila ? '' : 'none';
        }
    }

    // Permitir búsqueda al presionar Enter en los campos de búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        const searchInputs = [{
                input: 'searchEstudiantes',
                tabla: 'tablaEstudiantes'
            },
            {
                input: 'searchDocentes',
                tabla: 'tablaDocentes'
            },
            {
                input: 'searchMaterias',
                tabla: 'tablaMaterias'
            },
            {
                input: 'searchAdmins',
                tabla: 'tablaAdmins'
            }
        ];

        searchInputs.forEach(item => {
            const input = document.getElementById(item.input);
            if (input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        buscarEnTabla(item.input, item.tabla);
                        e.preventDefault();
                    }
                });
            }
        });
    });
</script>

</html>