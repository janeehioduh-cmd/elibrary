<?php

class Contact {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Save a contact message
    public function saveMessage($name, $email, $phone, $message) {

        $sql = "INSERT INTO contacts
                (name, email, phone, message)
                VALUES
                (:name, :email, :phone, :message)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':message' => $message
        ]);
    }

    // Get all contact messages
    public function getAllMessages() {

        $sql = "SELECT * FROM contacts ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
