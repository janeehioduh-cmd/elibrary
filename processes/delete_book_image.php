<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/books.php");
    exit();
}

$image_id = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT);
$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);

if (!$image_id || !$book_id) {
    die("Invalid image or book ID.");
}

$db = new Database();
$conn = $db->connect();

try {

    // Find the selected image
    $query = $conn->prepare("
        SELECT image_path
        FROM book_images
        WHERE id = :image_id
        AND book_id = :book_id
    ");

    $query->execute([
        ':image_id' => $image_id,
        ':book_id' => $book_id
    ]);

    $image = $query->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        throw new Exception("Image not found.");
    }

    $imagePath = $image['image_path'];

    $conn->beginTransaction();

    // Check whether this image is the book's main cover
    $coverQuery = $conn->prepare("
        SELECT cover_image
        FROM books
        WHERE id = :book_id
    ");

    $coverQuery->execute([':book_id' => $book_id]);

    $book = $coverQuery->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        throw new Exception("Book not found.");
    }

    // If the deleted image is the cover, choose another gallery image
    if ($book['cover_image'] === $imagePath) {

        $replacementQuery = $conn->prepare("
            SELECT image_path
            FROM book_images
            WHERE book_id = :book_id
            AND id != :image_id
            ORDER BY id ASC
            LIMIT 1
        ");

        $replacementQuery->execute([
            ':book_id' => $book_id,
            ':image_id' => $image_id
        ]);

        $replacement = $replacementQuery->fetch(PDO::FETCH_ASSOC);

        $newCover = $replacement ? $replacement['image_path'] : null;

        $updateCover = $conn->prepare("
            UPDATE books
            SET cover_image = :cover_image
            WHERE id = :book_id
        ");

        $updateCover->execute([
            ':cover_image' => $newCover,
            ':book_id' => $book_id
        ]);
    }

    // Delete the selected image from the database
    $deleteQuery = $conn->prepare("
        DELETE FROM book_images
        WHERE id = :image_id
        AND book_id = :book_id
    ");

    $deleteQuery->execute([
        ':image_id' => $image_id,
        ':book_id' => $book_id
    ]);

    $conn->commit();

    // Remove the physical image file safely
    $uploadFolder = realpath(__DIR__ . "/../uploads/books");

    if ($uploadFolder !== false) {

        $filePath = realpath(__DIR__ . "/../" . $imagePath);

        if (
            $filePath !== false &&
            strpos($filePath, $uploadFolder . DIRECTORY_SEPARATOR) === 0 &&
            is_file($filePath)
        ) {
            unlink($filePath);
        }
    }

    $_SESSION['image_message'] = "Image deleted successfully.";

} catch (Throwable $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    $_SESSION['image_message'] = "Unable to delete image: " . $e->getMessage();
}

header("Location: ../admin/manage_book_images.php?id=" . $book_id);
exit();