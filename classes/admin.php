<?php

class Admin {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Admin login
    public function login($email, $password) {

        $sql = "SELECT * FROM admins WHERE email = :email";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return false;
    }

    // Get admin details
    public function getAdminById($id) {

        $sql = "SELECT id, name, email FROM admins WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
