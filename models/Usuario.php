<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($usuario, $password) {
        $query = "SELECT id, usuario, rol, password FROM " . $this->table_name . " WHERE usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario]);

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function actualizarDatosContacto($id, $email, $telefono) {
        $query = "UPDATE " . $this->table_name . " SET email = ?, telefono = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$email, $telefono, $id]);
    }
}
?>

