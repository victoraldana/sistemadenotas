<?php
class Estudiante
{
    private $conn;
    private $table_name = "estudiantes";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerDatosEstudiante($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerMateriasInscritas($id)
    {
        $query = "SELECT m.nombre, m.codigo, mi.nota, d.nombre as docente 
                 FROM inscripciones mi 
                 JOIN materias m ON mi.materia_id = m.id 
                 JOIN docentes d ON m.docente_id = d.id 
                 WHERE mi.estudiante_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialAcademico($id)
    {
        $query = "SELECT * FROM historial_academico h  WHERE h.cedula_estudiante = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHorario($id)
    {
        $query = "SELECT h.dia, m.nombre as materia, h.hora_inicio, h.hora_fin, 
                        h.aula, h.seccion 
                 FROM horarios h 
                 JOIN materias m ON h.materia_id = m.id 
                 WHERE h.cedula = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarDatosContacto($id, $email, $telefono, $direccion)
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET email = ?, telefono = ?, direccion = ? 
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$email, $telefono, $direccion, $id]);
    }
}
// Clase para gestionar carreras
class Carrera
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function obtenerCarreras()
    {
        $sql = "SELECT id, nombre FROM carreras";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarCarrera($nombreCarrera)
    {
        $sql = "INSERT INTO carreras (nombre) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $nombreCarrera);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
