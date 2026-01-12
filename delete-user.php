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

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteUser') {
    // Obtener y sanitizar datos
    $userType = $_POST['userType'];
    $userId = $_POST['userId'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cedula = isset($_POST['cedula']) ? $_POST['cedula'] : ''; // Obtener la cédula para redirección
    
    // Verificar credenciales del administrador
    $sql = "SELECT * FROM administradores WHERE cedula = ? AND clave = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        // Credenciales válidas, proceder con la eliminación
        $tableName = "";
        
        switch ($userType) {
            case 'admin':
                $tableName = "administradores";
                break;
            case 'student':
                $tableName = "estudiantes";
                // Eliminar primero las inscripciones relacionadas
                $sql = "DELETE FROM inscripciones WHERE estudiante_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                
                // Eliminar calificaciones relacionadas si existe la tabla
                $checkTable = $conn->query("SHOW TABLES LIKE 'calificaciones'");
                if ($checkTable->num_rows > 0) {
                    $sql = "DELETE FROM calificaciones WHERE estudiante_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                }
                break;
            case 'teacher':
                $tableName = "docentes";
                
                // Verificar si el docente tiene materias asignadas
                $sql = "SELECT COUNT(*) as total FROM materias WHERE docente_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['total'] > 0) {
                    // El docente tiene materias asignadas, no se puede eliminar
                    $_SESSION['error'] = "No se puede eliminar el docente porque tiene materias asignadas. Reasigne las materias primero.";
                    header("Location: docente.php?cedula=" . $cedula);
                    exit();
                }
                break;
            default:
                $_SESSION['error'] = 'Tipo de usuario no válido';
                header("Location: admin_dashboard.php");
                exit();
        }
        
        // Eliminar usuario
        $sql = "DELETE FROM $tableName WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            // Registro de la acción
            $adminId = $_SESSION['id'] ?? 0;
            $accion = "Eliminación de " . $userType . " con ID: " . $userId;
            $fecha = date("Y-m-d H:i:s");
            
            // Verificar si existe la tabla logs
            $checkTable = $conn->query("SHOW TABLES LIKE 'logs'");
            if ($checkTable->num_rows > 0) {
                $sql = "INSERT INTO logs (admin_id, accion, fecha) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $adminId, $accion, $fecha);
                $stmt->execute();
            }
            
            // Redirigir al dashboard con mensaje de éxito
            $_SESSION['mensaje'] = "Usuario eliminado correctamente";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Redirigir con mensaje de error
            $_SESSION['error'] = "Error al eliminar el usuario: " . $stmt->error;
            
            if ($userType === 'teacher') {
                header("Location: docente.php?cedula=" . $cedula);
            } else {
                header("Location: admin_dashboard.php");
            }
            exit();
        }
    } else {
        // Credenciales inválidas, redirigir con error
        $_SESSION['error'] = "Credenciales inválidas. No se pudo eliminar el usuario.";
        
        // Redirigir de vuelta a la página anterior
        if ($userType === 'admin') {
            header("Location: admin_detalle.php?id=" . $userId);
        } elseif ($userType === 'student') {
            header("Location: usuario.php?id=" . $userId);
        } elseif ($userType === 'teacher') {
            header("Location: docente.php?cedula=" . $cedula);
        } else {
            header("Location: admin_dashboard.php");
        }
        exit();
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// Si no es una solicitud POST válida
$_SESSION['error'] = "Solicitud no válida";
header("Location: admin_dashboard.php");
exit();
?>



