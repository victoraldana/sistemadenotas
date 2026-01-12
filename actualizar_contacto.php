<?php
session_start();
require_once 'config/database.php';
require_once 'models/Estudiante.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'estudiante') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

$database = new Database();
$db = $database->getConnection();
$estudiante = new Estudiante($db);

$resultado = $estudiante->actualizarDatosContacto(
    $_SESSION['id'],
    $data['email'],
    $data['telefono'],
    $data['direccion']
);

header('Content-Type: application/json');
echo json_encode([
    'success' => $resultado,
    'message' => $resultado ? 'Datos actualizados correctamente' : 'Error al actualizar los datos'
]);
?>

