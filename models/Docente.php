<?php
error_reporting(0);
require_once 'Database.php';
class Docente
{
    private $conn;
    private $table_name = "docentes";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerDatosDocente($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerMateriasAsignadas($id)
    {
        try {
            $query = "SELECT 
                m.id, 
                m.nombre, 
                m.codigo,
                (SELECT COUNT(*) FROM inscripciones i WHERE i.materia_id = m.id) as total_estudiantes
            FROM materias m
            WHERE m.docente_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Para cada materia, obtenemos los estudiantes con sus detalles
            foreach ($materias as &$materia) {
                $materia['estudiantes'] = $this->obtenerEstudiantesPorMateria($materia['id']);
            }

            return $materias;
        } catch (PDOException $e) {
            error_log("Error en obtenerMateriasAsignadas: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerEstudiantesPorMateria($materia_id)
    {
        $query = "SELECT e.id, e.cedula, e.nombre, e.apellido, e.email, e.telefono, mi.nota
                FROM estudiantes e 
                JOIN inscripciones mi ON e.id = mi.estudiante_id 
                WHERE mi.materia_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$materia_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignarNota($estudiante_id, $materia_id, $nota)
    {
        $query = "UPDATE inscripciones 
                 SET nota = ? 
                 WHERE estudiante_id = ? AND materia_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nota, $estudiante_id, $materia_id]);
    }

    public function actualizarDatosContacto($id, $email, $telefono)
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET email = ?, telefono = ? 
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$email, $telefono, $id]);
    }

    public function moverEstudiantesAHistorial($docente_id)
    {
        try {
            // Iniciar una transacción para asegurar la atomicidad de las operaciones
            $this->conn->beginTransaction();

            // Obtener todas las materias asignadas al docente
            $materias = $this->obtenerMateriasAsignadas($docente_id);

            // Recorrer cada materia
            foreach ($materias as $materia) {
                $materia_id = $materia['id'];
                $nombre_materia = $materia['nombre'];

                // Obtener todos los estudiantes inscritos en la materia
                $estudiantes = $this->obtenerEstudiantesPorMateria($materia_id);

                // Recorrer cada estudiante
                foreach ($estudiantes as $estudiante) {
                    $nota = $estudiante['nota'];

                    // Verificar si la nota es 0
                    if ($nota == 0) {
                        continue; // Saltar este estudiante
                    }

                    $cedula_estudiante = $estudiante['cedula'];
                    $nombre_estudiante = $estudiante['nombre'];
                    $fecha_calificacion = date('Y-m-d H:i:s'); // Fecha actual

                    // Insertar los datos en la tabla historial_academico
                    $query = "INSERT INTO historial_academico 
                          (cedula_estudiante, nombre_estudiante, nombre_materia, nombre_docente, nota, fecha_calificacion) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute([$cedula_estudiante, $nombre_estudiante, $nombre_materia, $this->obtenerNombreDocente($docente_id), $nota, $fecha_calificacion]);

                    // Eliminar al estudiante de la tabla inscripciones
                    $query = "DELETE FROM inscripciones 
                          WHERE estudiante_id = ? AND materia_id = ?";
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute([$estudiante['id'], $materia_id]);
                }
            }

            // Confirmar la transacción
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            // En caso de error, revertir la transacción
            $this->conn->rollBack();
            error_log("Error en moverEstudiantesAHistorial: " . $e->getMessage());
            throw $e;
        }
    }

    private function obtenerNombreDocente($docente_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$docente_id]);
        $docente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $docente['nombre'] . " " . $docente['apellido'];
    }
}

$database = new Database("localhost", "root", "", "sistema_notas");
$conn = $database->getConnection();

session_start();
$id = $_SESSION["id"];
$docenteManager = new Docente($conn);

$docente = $docenteManager->obtenerDatosDocente($id);
$materias_asignadas = $docenteManager->obtenerMateriasAsignadas($id);

if (isset($_GET['materia_id']) && isset($_GET['estudiante_id']) && isset($_GET['nota'])) {
    // Obtener los parámetros de la URL
    $materia_id = $_GET['materia_id'];
    $estudiante_id = $_GET['estudiante_id'];
    $nota = $_GET['nota'];

    // Intentar asignar la nota
    try {
        $resultado = $docenteManager->asignarNota($estudiante_id, $materia_id, $nota);

        // Verificar si la asignación fue exitosa
        if ($resultado) {
            // Respuesta exitosa
            echo json_encode([
                'status' => 'success',
                'message' => 'Nota asignada correctamente.'
            ]);
        } else {
            // Respuesta de error
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo asignar la nota.'
            ]);
        }
    } catch (PDOException $e) {
        // Capturar y mostrar errores de la base de datos
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
}

// Verificar si se debe mover a los estudiantes al historial académico
if (isset($_GET['mover_estudiantes'])) {
    try {
        $resultado = $docenteManager->moverEstudiantesAHistorial($_GET['id']);

        // Verificar si la operación fue exitosa
        if ($resultado) {
            // Respuesta exitosa
            echo json_encode([
                'status' => 'success',
                'message' => 'Estudiantes movidos al historial académico correctamente.'
            ]);
        } else {
            // Respuesta de error
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudieron mover los estudiantes al historial académico.'
            ]);
        }
    } catch (PDOException $e) {
        // Capturar y mostrar errores de la base de datos
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
}
