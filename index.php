<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database configuration
    $servername = "localhost";
    $username = "u295514716_userNotas";
    $password = "Casa1212..";
    $dbname = "u295514716_NOTAS";

    try {
        // Create connection using PDO for prepared statements
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $rol = $_POST['rol']; // Store the role
        $tabla = "";
        switch ($rol) {
            case 'administrador':
                $tabla = "administradores";
                break;
            case 'docente':
                $tabla = "docentes";
                break;
            case 'estudiante':
                $tabla = "estudiantes";
                break;
            default: // Handle unknown roles
                $tabla = "";
                exit();
        }
        // Use prepared statements to prevent SQL injection
        $query = "SELECT * FROM " . $tabla . " WHERE cedula = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) { // Check if a row was found

            if ($password == $row['clave']) {
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['rol'] = $rol; // Use the stored $rol
                $_SESSION['id'] = $row['id'];
                $_SESSION['cedula'] = $row['cedula'];


                // Redirect based on role
                switch ($rol) {
                    case 'administrador':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'docente':
                        header("Location: docente_dashboard.php");
                        break;
                    case 'estudiante':
                        header("Location: estudiante_dashboard.php");
                        break;
                    default: // Handle unknown roles
                        header("Location: index.php"); // Or display an error
                        exit();
                }
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    $conn = null; // Close the connection
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Registro de Notas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-form">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-user-graduate me-2"></i>
                        Iniciar Sesión
                    </h2>
                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">
                                <i class="fas fa-user me-2"></i>Cedula
                            </label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <select id="rol" name="rol" class="form-control" required>
                                <option value="">Seleccione un rol</option>
                                <option value="estudiante">Estudiante</option>
                                <option value="docente">Docente</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>