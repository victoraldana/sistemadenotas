<?php
//Clase para la conexión a la base de datos
class Database
{
    private $conn;

    public function __construct($servername, $username, $password, $dbname)
    {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn->close();
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

// Clase para gestionar estudiantes
class Estudiante
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function agregarEstudiante($cedula, $nombre, $apellido, $fechaNacimiento, $lugarNacimiento, $direccion, $telefono, $email, $clave, $carrera, $fechaIngreso, $fotoPerfil)
    {
        $sql = "INSERT INTO estudiantes (cedula, nombre, apellido, fecha_nacimiento, lugar_nacimiento, direccion, telefono, email, clave, carrera, fecha_ingreso, foto_perfil) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("ssssssssssss", $cedula, $nombre, $apellido, $fechaNacimiento, $lugarNacimiento, $direccion, $telefono, $email, $clave, $carrera, $fechaIngreso, $fotoPerfil);

        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }

        $stmt->close();
    }

    public function obtenerEstudiantes()
    {
        $sql = "SELECT * FROM estudiantes";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function obtenerEstudiantePorCedula($cedula)
    {
        $sql = "SELECT id, nombre, apellido FROM estudiantes WHERE cedula = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc(); // Devuelve solo un estudiante (si existe)
    }
}

// Clase para gestionar docentes
class Docente
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function agregarDocente($cedula, $nombre, $apellido, $fechaNacimiento, $lugarNacimiento, $direccion, $telefono, $email, $clave, $especialidad, $fechaIngreso, $fotoPerfil)
    {
        $sql = "INSERT INTO docentes (cedula, nombre, apellido, fecha_nacimiento, lugar_nacimiento, direccion, telefono, email, clave, especialidad, fecha_ingreso, foto_perfil) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("ssssssssssss", $cedula, $nombre, $apellido, $fechaNacimiento, $lugarNacimiento, $direccion, $telefono, $email, $clave, $especialidad, $fechaIngreso, $fotoPerfil);

        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }

        $stmt->close();
    }

    public function obtenerDocentes()
    {
        $sql = "SELECT * FROM docentes";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Clase para gestionar materias
class Materia
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function agregarMateria($nombre, $codigo, $docenteId, $carreraId)
    { // Agregar $carreraId como parámetro
        $sql = "INSERT INTO materias (nombre, codigo, docente_id, carrera_id) VALUES (?, ?, ?, ?)"; // Incluir carrera_id
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $codigo, $docenteId, $carreraId); // bind_param con un entero más
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function obtenerMaterias()
    {
        $sql = "SELECT m.id, m.nombre, m.codigo, d.nombre AS nombre_docente, c.nombre AS nombre_carrera,
                       GROUP_CONCAT(CONCAT(e.cedula, ' ', e.nombre,' ', e.apellido) SEPARATOR ', ') AS estudiantes_inscritos
                FROM materias m
                INNER JOIN docentes d ON m.docente_id = d.id
                INNER JOIN carreras c ON m.carrera_id = c.id
                LEFT JOIN inscripciones i ON m.id = i.materia_id
                LEFT JOIN estudiantes e ON i.estudiante_id = e.id
                GROUP BY m.id";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Clase para gestionar inscripciones
class Inscripcion
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function inscribirEstudiante($estudianteId, $materiaId)
    {
        $fecha = date('Y-m-d');
        $sql = "INSERT INTO inscripciones (estudiante_id, materia_id, fecha_inscripcion) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $estudianteId, $materiaId, $fecha);
        $stmt->execute();
        $stmt->close();
    }
}

class Administrador
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function agregarAdministrador($cedula, $nombre, $apellido, $telefono, $clave, $puesto)
    {
        $sql = "INSERT INTO administradores (cedula, nombre, apellido, telefono, clave, puesto) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("ssssss", $cedula, $nombre, $apellido, $telefono, $clave, $puesto);

        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }

        $stmt->close();
        return true;
    }

    public function obtenerAdministradores()
    {
        $sql = "SELECT * FROM administradores";
        $result = $this->conn->query($sql);

        if (!$result) {
            die("Error al obtener administradores: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerAdministradorPorId($id)
    {
        $sql = "SELECT * FROM administradores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function actualizarAdministrador($id, $cedula, $nombre, $apellido, $telefono, $puesto, $clave = null)
    {
        // Si se proporciona una nueva clave, actualizarla también
        if ($clave) {
            $sql = "UPDATE administradores SET cedula = ?, nombre = ?, apellido = ?, telefono = ?, puesto = ?, clave = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $this->conn->error);
            }

            $stmt->bind_param("ssssssi", $cedula, $nombre, $apellido, $telefono, $puesto, $clave, $id);
        } else {
            // Si no se proporciona clave, mantener la existente
            $sql = "UPDATE administradores SET cedula = ?, nombre = ?, apellido = ?, telefono = ?, puesto = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $this->conn->error);
            }

            $stmt->bind_param("sssssi", $cedula, $nombre, $apellido, $telefono, $puesto, $id);
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function eliminarAdministrador($id)
    {
        $sql = "DELETE FROM administradores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
