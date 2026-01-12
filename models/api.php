<?php
// Conexión a la base de datos (ajusta los datos de conexión)
$conn = new mysqli('localhost', 'u295514716_userNotas', 'Casa1212..', 'u295514716_NOTAS');


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case 'buscarEstudiante':
            if (isset($_GET['cedula'])) {
                $cedula = $_GET['cedula'];
                $sql = "SELECT id, nombre, apellido FROM estudiantes WHERE cedula = '$cedula'";
                $result = $conn->query($sql);

                $estudiantes = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $estudiantes[] = $row;
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($estudiantes);
            }
            break;
    }
}

$conn->close();
