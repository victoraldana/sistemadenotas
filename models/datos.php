<?php

include_once('Administrador.php');

// Crear instancia de la conexión a la base de datos
$db = new Database('localhost', 'u295514716_userNotas', 'Casa1212..', 'u295514716_NOTAS');
$conn = $db->getConnection();

// Crear instancias de las clases de gestión
$estudianteManager = new Estudiante($conn);
$docenteManager = new Docente($conn);
$materiaManager = new Materia($conn);
$inscripcionManager = new Inscripcion($conn);
$carrerasManager = new Carrera($conn);
$administradorManager = new Administrador($conn); // Nueva instancia para administradores

// Variables para el formulario de inscripción
$estudianteSeleccionado = null; // Almacena el estudiante seleccionado

// Procesar formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregarEstudiante':
                $estudianteManager->agregarEstudiante(
                    $_POST['cedula'],
                    $_POST['nombre'],
                    $_POST['apellido'],
                    $_POST['fechaNacimiento'],
                    $_POST['lugarNacimiento'],
                    $_POST['direccion'],
                    $_POST['telefono'],
                    $_POST['email'],
                    $_POST['clave'],
                    $_POST['carreraId'],
                    $_POST['fechaIngreso'],
                    "img/user.jpg"
                );
                break;

            case 'agregarDocente':
                $docenteManager->agregarDocente(
                    $_POST['cedula'],
                    $_POST['nombre'],
                    $_POST['apellido'],
                    $_POST['fechaNacimiento'],
                    $_POST['lugarNacimiento'],
                    $_POST['direccion'],
                    $_POST['telefono'],
                    $_POST['email'],
                    $_POST['clave'],
                    $_POST['especialidad'],
                    $_POST['fechaIngreso'],
                    "img/user.jpg"
                );
                break;

            case 'agregarMateria':
                $materiaManager->agregarMateria($_POST['nombre'], $_POST['codigo'], $_POST['docenteId'], $_POST['carreraId']);
                break;

            case 'buscarEstudiante':
                if (isset($_POST['cedula'])) {
                    $cedula = $_POST['cedula'];
                    $estudianteSeleccionado = $estudianteManager->obtenerEstudiantePorCedula($cedula);
                }
                break;

            case 'inscribirEstudiante':
                if (isset($_POST['estudianteId']) && isset($_POST['materiaId'])) {
                    $inscripcionManager->inscribirEstudiante($_POST['estudianteId'], $_POST['materiaId']);
                }
                break;

            case 'agregarCarrera':
                $nombreCarrera = $_POST['nombreCarrera'];
                if ($carrerasManager->agregarCarrera($nombreCarrera)) {
                    echo "<div class='alert alert-success'>Carrera agregada correctamente.</div>";
                    $carreras = $carrerasManager->obtenerCarreras();
                } else {
                    echo "<div class='alert alert-danger'>Error al agregar la carrera.</div>";
                }
                break;

            case 'agregarAdministrador':
                // Procesar el formulario para agregar un nuevo administrador
                $administradorManager->agregarAdministrador(
                    $_POST['cedula'],
                    $_POST['nombre'],
                    $_POST['apellido'],
                    $_POST['telefono'],
                    $_POST['clave'],
                    $_POST['puesto']
                );
                echo "<div class='alert alert-success'>Administrador agregado correctamente.</div>";
                break;

            case 'actualizarAdmin':
                // Procesar el formulario para actualizar un administrador existente
                $id = $_POST['id'];
                $cedula = $_POST['cedula'];
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $telefono = $_POST['telefono'];
                $puesto = $_POST['puesto'];
                $clave = !empty($_POST['clave']) ? $_POST['clave'] : null;

                if ($administradorManager->actualizarAdministrador($id, $cedula, $nombre, $apellido, $telefono, $puesto, $clave)) {
                    echo "<div class='alert alert-success'>Administrador actualizado correctamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al actualizar el administrador.</div>";
                }
                break;
        }
    }
}

// Obtener datos para mostrar en las tablas
$estudiantes = $estudianteManager->obtenerEstudiantes();
$docentes = $docenteManager->obtenerDocentes();
$materias = $materiaManager->obtenerMaterias();
$carreras = $carrerasManager->obtenerCarreras();
$administradores = $administradorManager->obtenerAdministradores(); // Obtener administradores

// Cerrar la conexión
$db->closeConnection();
