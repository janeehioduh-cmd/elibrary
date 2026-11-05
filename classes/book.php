<?php

class Book {

    private $conn;
    private $table = "books";

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Display all books
    public function getAllBooks() {

        $sql = "SELECT books.*, categories.name AS category_name
                FROM books
                LEFT JOIN categories
                ON books.category_id = categories.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find one book by ID
    public function getBookById($id) {

        $sql = "SELECT * FROM books WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new book
    public function addBook($title, $author, $category_id, $description, $cover_image) {

        $sql = "INSERT INTO books
                (title, author, category_id, description, cover_image)
                VALUES
                (:title, :author, :category_id, :description, :cover_image)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':category_id' => $category_id,
            ':description' => $description,
            ':cover_image' => $cover_image
        ]);
    }

    // Update a book
    public function updateBook($id, $title, $author, $category_id, $description) {

        $sql = "UPDATE books SET
                title = :title,
                author = :author,
                category_id = :category_id,
                description = :description
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':category_id' => $category_id,
            ':description' => $description,
            ':id' => $id
        ]);
    }

    // Delete a book
    public function deleteBook($id) {

        $sql = "DELETE FROM books WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

}

?>