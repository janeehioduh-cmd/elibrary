<?php

class Category {

    private $conn;
    private $table = "categories";

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all categories
    public function getAllCategories() {

        $sql = "SELECT * FROM categories ORDER BY name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one category by ID
    public function getCategoryById($id) {

        $sql = "SELECT * FROM categories WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add category
    public function addCategory($name) {

        $sql = "INSERT INTO categories (name) VALUES (:name)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name
        ]);
    }

    // Update category
    public function updateCategory($id, $name) {

        $sql = "UPDATE categories
                SET name = :name
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':id' => $id
        ]);
    }

    // Delete category
    public function deleteCategory($id) {

        $sql = "DELETE FROM categories WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return true;
    }

}

?>
