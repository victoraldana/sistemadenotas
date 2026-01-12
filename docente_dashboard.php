<?php
session_start();

// Verificar si el usuario está logueado y es docente
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'docente') {
    header("Location: login.php");
    exit();
}

include_once('models/Docente.php');


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Docente - Sistema de Registro de Notas</title>
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
                        <a class="nav-link" href="#materias-asignadas">Mis Materias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#perfil-docente">Mi Perfil</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    Bienvenido, <?php echo $docente['nombre']; ?> |
                    <a href="logout.php" class="text-white">Cerrar sesión</a>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Panel de Docente</h2>

        <section id="perfil-docente" class="mb-5">
            <h3>Mi Perfil</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <img src="<?php echo $docente['foto_perfil'] ? $docente['foto_perfil'] : 'img/user.jpg'; ?>" alt="Foto de perfil de <?php echo $docente['nombre']; ?>" class="img-fluid rounded-circle" id="profile-picture">
                        </div>
                        <div class="col-md-9">
                            <p><strong><i class="fas fa-user"></i> Nombres:</strong> <?php echo $docente['nombre']; ?></p>
                            <p><strong><i class="fas fa-user"></i> Apellidos:</strong> <?php echo $docente['apellido']; ?></p>
                            <p><strong><i class="fas fa-id-card"></i> Cédula:</strong> <?php echo $docente['cedula']; ?></p>
                            <p><strong><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento:</strong> <?php echo $docente['fecha_nacimiento']; ?></p>
                            <p><strong><i class="fas fa-map-marker-alt"></i> Lugar de Nacimiento:</strong> <?php echo $docente['lugar_nacimiento']; ?></p>

                            <p><strong><i class="fas fa-book"></i> Especialidad:</strong> <?php echo $docente['especialidad']; ?></p>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <form id="formActualizarContacto">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label"><i class="fas fa-phone"></i> Teléfono:</label>
                                    <input type="tel" class="form-control" id="telefono" value="<?php echo $docente['telefono']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email:</label>
                                    <input type="email" class="form-control" id="email" value="<?php echo $docente['email']; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Actualizar Contacto</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="materias-asignadas" class="mb-5">
            <h3>Materias Asignadas</h3>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-warning" onclick="moverEstudiantesAHistorial()">
                    <i class="fas fa-exchange-alt"></i> Mover Estudiantes a Historial
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre de la Materia</th>
                            <th>Código</th>
                            <th>Estudiantes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materias_asignadas as $index => $materia): ?>
                            <tr>
                                <td><?php echo $materia['nombre']; ?></td>
                                <td><?php echo $materia['codigo']; ?></td>
                                <td><?php echo $materia['total_estudiantes']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="verListaEstudiantes(<?php echo $index; ?>)">
                                        <i class="fas fa-list"></i> Ver Lista
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="lista-estudiantes" class="mb-5" style="display: none;">
            <h3 id="titulo-lista-estudiantes"></h3>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success" onclick="descargarListaEstudiantes()">
                    <i class="fas fa-download"></i> Descargar Lista
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Nota</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-lista-estudiantes">
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Modal para asignar nota -->
    <div class="modal fade" id="asignarNotaModal" tabindex="-1" aria-labelledby="asignarNotaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="asignarNotaModalLabel">Asignar Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAsignarNota">
                        <div class="mb-3">
                            <label for="estudiante-nombre" class="form-label">Estudiante:</label>
                            <input type="text" class="form-control" id="estudiante-nombre" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="nota" class="form-label">Nota:</label>
                            <input type="number" class="form-control" id="nota" min="0" max="100" required>
                        </div>
                        <input type="hidden" id="materia-index">
                        <input type="hidden" id="estudiante-index">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarNota()">Guardar Nota</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script>
        function moverEstudiantesAHistorial() {
            const confirmacion = confirm('¿Estás seguro de mover a todos los estudiantes al historial académico? Esta acción no se puede deshacer.');
            if (!confirmacion) {
                return; // Si el usuario cancela, no hacer nada
            }

            const idDocente = <?php echo json_encode($id); ?>; // Obtener el ID del docente desde PHP

            fetch(`models/Docente.php?mover_estudiantes=true&id=${idDocente}`)
                .then(response => response.json()) // Convertir la respuesta a JSON
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message); // Mostrar mensaje de éxito
                        location.reload(); // Recargar la página para actualizar los datos
                    } else {
                        alert('Error: ' + data.message); // Mostrar mensaje de error
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Hubo un error al conectar con el servidor.');
                });
        }

        const materiasAsignadas = <?php echo json_encode($materias_asignadas); ?>;
        const nombreDocente = <?php echo json_encode($docente['nombre'] . ' ' . $docente['apellido']); ?>;
        const periodoActual = "periodo";
        let materiaActualIndex = -1;

        function verListaEstudiantes(index) {
            materiaActualIndex = index;
            const materia = materiasAsignadas[index];
            document.getElementById('titulo-lista-estudiantes').textContent = `Lista de Estudiantes - ${materia.nombre} (${materia.codigo})`;

            const tbody = document.getElementById('tbody-lista-estudiantes');
            tbody.innerHTML = '';

            materia['estudiantes'].forEach((estudiante, estudianteIndex) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${estudiante.cedula}</td>
                    <td>${estudiante.nombre}</td>
                    <td>${estudiante.apellido}</td>
                    <td>${estudiante.email}</td>
                    <td>${estudiante.telefono}</td>
                    <td>${estudiante.nota}</td>
                    
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="abrirModalNota(${index}, ${estudianteIndex})">
                            <i class="fas fa-edit"></i> Asignar Nota
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('lista-estudiantes').style.display = 'block';
        }

        function abrirModalNota(materiaIndex, estudianteIndex) {
            const materia = materiasAsignadas[materiaIndex];
            const estudiante = materia.estudiantes[estudianteIndex];

            document.getElementById('estudiante-nombre').value = `${estudiante.nombre} ${estudiante.apellido}`;
            document.getElementById('nota').value = estudiante.nota !== null ? estudiante.nota : '';
            document.getElementById('materia-index').value = materiaIndex;
            document.getElementById('estudiante-index').value = estudianteIndex;

            const modal = new bootstrap.Modal(document.getElementById('asignarNotaModal'));
            modal.show();
        }

        function guardarNota() {
            const materiaIndex = document.getElementById('materia-index').value;
            const estudianteIndex = document.getElementById('estudiante-index').value;
            const nota = document.getElementById('nota').value;

            // Validar la nota
            if (nota === '' || isNaN(nota) || nota < 0 || nota > 20) {
                alert('Por favor, ingrese una nota válida entre 0 y 20.');
                return;
            }

            // Obtener los IDs de la materia y el estudiante
            const materiaId = materiasAsignadas[materiaIndex].id;
            const estudianteId = materiasAsignadas[materiaIndex].estudiantes[estudianteIndex].id;

            // Hacer la llamada a la API
            fetch(`models/Docente.php?materia_id=${materiaId}&estudiante_id=${estudianteId}&nota=${nota}`)
                .then(response => response.json()) // Convertir la respuesta a JSON
                .then(data => {
                    if (data.status === 'success') {
                        // Actualizar la nota en el frontend si la API fue exitosa
                        materiasAsignadas[materiaIndex].estudiantes[estudianteIndex].nota = parseFloat(nota);
                        alert('Nota asignada correctamente.');

                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('asignarNotaModal'));
                        modal.hide();

                        // Actualizar la lista de estudiantes
                        verListaEstudiantes(materiaActualIndex);
                    } else {
                        // Mostrar un mensaje de error si la API falló
                        alert('Error al asignar la nota: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Hubo un error al conectar con el servidor.');
                });
        }

        document.getElementById('formActualizarContacto').addEventListener('submit', function(e) {
            e.preventDefault();
            const nuevoTelefono = document.getElementById('telefono').value;
            const nuevoEmail = document.getElementById('email').value;

            // Aquí iría la lógica para actualizar los datos en el servidor
            // Por ahora, solo mostraremos un mensaje de éxito
            alert('Datos de contacto actualizados correctamente');
        });

        function descargarListaEstudiantes() {
            if (materiaActualIndex === -1) {
                alert('Por favor, seleccione una materia primero.');
                return;
            }

            const materia = materiasAsignadas[materiaActualIndex];
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // Configurar el encabezado del documento
            doc.setFontSize(18);
            doc.text('Lista de Estudiantes', 14, 15);

            doc.setFontSize(12);
            doc.text(`Docente: ${nombreDocente}`, 14, 25);
            doc.text(`Materia: ${materia.nombre} (${materia.codigo})`, 14, 31);
            doc.text(`Periodo: ${periodoActual}`, 14, 37);
            doc.text(`Fecha de generación: ${new Date().toLocaleDateString()}`, 14, 43);

            // Crear la tabla de estudiantes
            const headers = [
                ['Cédula', 'Nombres', 'Apellidos', 'Correo Electrónico', 'Teléfono', 'Nota']
            ];
            const data = materia.estudiantes.map(estudiante => [
                estudiante.cedula,
                estudiante.nombre,
                estudiante.apellido,
                estudiante.email,
                estudiante.telefono,
                estudiante.nota !== null ? estudiante.nota : '-'
            ]);

            doc.autoTable({
                head: headers,
                body: data,
                startY: 50,
                styles: {
                    fontSize: 8
                },
                columnStyles: {
                    0: {
                        cellWidth: 25
                    },
                    1: {
                        cellWidth: 30
                    },
                    2: {
                        cellWidth: 30
                    },
                    3: {
                        cellWidth: 50
                    },
                    4: {
                        cellWidth: 30
                    },
                    5: {
                        cellWidth: 20
                    }
                }
            });

            // Guardar el PDF
            doc.save(`Lista_Estudiantes_${materia.codigo}_${periodoActual}.pdf`);
        }
    </script>
</body>

</html>