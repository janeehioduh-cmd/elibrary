<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Book.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/books.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$book = new Book($conn);

$action = $_POST['action'] ?? '';

$success = false;
$message = "";

$uploadedImages = [];
$uploadedCover = null;
$imagesToDelete = [];


// Upload multiple gallery images
function uploadBookImages($files) {

    $uploaded = [];

    if (
        !$files ||
        !isset($files['name']) ||
        !is_array($files['name'])
    ) {
        return $uploaded;
    }

    $selectedFiles = [];

    foreach ($files['name'] as $key => $name) {

        $error = $files['error'][$key] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new Exception("An image upload failed. Please try again.");
        }

        $selectedFiles[] = $key;
    }

    if (count($selectedFiles) > 10) {
        throw new Exception("You can upload a maximum of 10 images at a time.");
    }

    if (empty($selectedFiles)) {
        return $uploaded;
    }

    $uploadDirectory = __DIR__ . "/../uploads/books/";

    if (
        !is_dir($uploadDirectory) &&
        !mkdir($uploadDirectory, 0755, true)
    ) {
        throw new Exception("Unable to create the book image folder.");
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    try {

        foreach ($selectedFiles as $key) {

            $temporaryFile = $files['tmp_name'][$key];
            $fileSize = $files['size'][$key];

            if ($fileSize > 5 * 1024 * 1024) {
                throw new Exception("Each image must not exceed 5 MB.");
            }

            if (!is_uploaded_file($temporaryFile)) {
                throw new Exception("Invalid image upload.");
            }

            if (getimagesize($temporaryFile) === false) {
                throw new Exception("One of the uploaded files is not a valid image.");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $temporaryFile);
            finfo_close($finfo);

            if (!isset($allowedTypes[$mimeType])) {
                throw new Exception("Only JPG, PNG and WEBP images are allowed.");
            }

            $extension = $allowedTypes[$mimeType];

            $fileName = bin2hex(random_bytes(16)) . "." . $extension;

            $destination = $uploadDirectory . $fileName;

            if (!move_uploaded_file($temporaryFile, $destination)) {
                throw new Exception("Unable to save an uploaded image.");
            }

            $uploaded[] = "uploads/books/" . $fileName;
        }

    } catch (Throwable $e) {

        deleteBookImageFiles($uploaded);

        throw $e;
    }

    return $uploaded;
}


// Upload one replacement cover image
function uploadBookCover($file) {

    if (!$file || !isset($file['error'])) {
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("The cover image upload failed.");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("The cover image must not exceed 5 MB.");
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new Exception("Invalid cover image upload.");
    }

    if (getimagesize($file['tmp_name']) === false) {
        throw new Exception("The cover file is not a valid image.");
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowedTypes[$mimeType])) {
        throw new Exception("Only JPG, PNG and WEBP cover images are allowed.");
    }

    $uploadDirectory = __DIR__ . "/../uploads/books/";

    if (
        !is_dir($uploadDirectory) &&
        !mkdir($uploadDirectory, 0755, true)
    ) {
        throw new Exception("Unable to create the book image folder.");
    }

    $fileName = bin2hex(random_bytes(16)) . "." . $allowedTypes[$mimeType];

    $destination = $uploadDirectory . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Unable to save the replacement cover image.");
    }

    return "uploads/books/" . $fileName;
}


// Save gallery image paths in the database
function saveBookImages($conn, $bookId, $images) {

    $sql = "INSERT INTO book_images (book_id, image_path)
            VALUES (:book_id, :image_path)";

    $stmt = $conn->prepare($sql);

    foreach ($images as $imagePath) {

        $stmt->execute([
            ':book_id' => $bookId,
            ':image_path' => $imagePath
        ]);
    }
}


// Safely delete files belonging to the book uploads folder
function deleteBookImageFiles($images) {

    $uploadDirectory = realpath(__DIR__ . "/../uploads/books");

    if ($uploadDirectory === false) {
        return;
    }

    foreach (array_unique($images) as $imagePath) {

        $fullPath = realpath(
            __DIR__ . "/../" . ltrim($imagePath, "/\\")
        );

        if (
            $fullPath !== false &&
            strpos(
                $fullPath,
                $uploadDirectory . DIRECTORY_SEPARATOR
            ) === 0 &&
            is_file($fullPath)
        ) {
            unlink($fullPath);
        }
    }
}


try {

    // ADD BOOK
    if ($action === 'add') {

        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($title === '' || $author === '') {
            throw new Exception("Book title and author are required.");
        }

        $uploadedImages = uploadBookImages(
            $_FILES['book_images'] ?? null
        );

        $coverImage = $uploadedImages[0] ?? null;

        $conn->beginTransaction();

        $success = $book->addBook(
            $title,
            $author,
            $category_id ?: null,
            $description,
            $coverImage
        );

        if (!$success) {
            throw new Exception("Unable to add book.");
        }

        $bookId = (int)$conn->lastInsertId();

        if ($bookId <= 0) {
            throw new Exception("Unable to retrieve the new book ID.");
        }

        saveBookImages($conn, $bookId, $uploadedImages);

        $conn->commit();

        $message = "Book and images added successfully.";


    // EDIT BOOK
    } elseif ($action === 'edit') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if (!$id) {
            throw new Exception("Invalid book ID.");
        }

        if ($title === '' || $author === '') {
            throw new Exception("Book title and author are required.");
        }

        $existingBook = $book->getBookById($id);

        if (!$existingBook) {
            throw new Exception("Book not found.");
        }

        // Upload any additional gallery images
        $uploadedImages = uploadBookImages(
            $_FILES['book_images'] ?? null
        );

        // Upload a replacement cover if one was selected
        $uploadedCover = uploadBookCover(
            $_FILES['cover_image'] ?? null
        );

        $conn->beginTransaction();

        $success = $book->updateBook(
            $id,
            $title,
            $author,
            $category_id ?: null,
            $description
        );

        if (!$success) {
            throw new Exception("Unable to update book.");
        }

        // Save new gallery images
        saveBookImages($conn, $id, $uploadedImages);

        // Replace the main cover when a new one was uploaded
        if ($uploadedCover !== null) {

            $updateCover = $conn->prepare("
                UPDATE books
                SET cover_image = :cover_image
                WHERE id = :id
            ");

            $updateCover->execute([
                ':cover_image' => $uploadedCover,
                ':id' => $id
            ]);

            $oldCover = $existingBook['cover_image'] ?? null;

        } else {

            // If there is no existing cover, use the first new gallery image
            if (!empty($uploadedImages)) {

                $coverStmt = $conn->prepare("
                    UPDATE books
                    SET cover_image = :cover_image
                    WHERE id = :id
                    AND (cover_image IS NULL OR cover_image = '')
                ");

                $coverStmt->execute([
                    ':cover_image' => $uploadedImages[0],
                    ':id' => $id
                ]);
            }

            $oldCover = null;
        }

        $conn->commit();

        // Delete the old cover only if no gallery record still uses it
        if (!empty($oldCover)) {

            $checkOldCover = $conn->prepare("
                SELECT COUNT(*)
                FROM book_images
                WHERE book_id = :book_id
                AND image_path = :image_path
            ");

            $checkOldCover->execute([
                ':book_id' => $id,
                ':image_path' => $oldCover
            ]);

            if ((int)$checkOldCover->fetchColumn() === 0) {
                deleteBookImageFiles([$oldCover]);
            }
        }

        $message = "Book updated successfully.";


    // DELETE BOOK
    } elseif ($action === 'delete') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            throw new Exception("Invalid book ID.");
        }

        $imageStmt = $conn->prepare("
            SELECT image_path
            FROM book_images
            WHERE book_id = :id
        ");

        $imageStmt->execute([':id' => $id]);

        $galleryImages = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

        $bookDetails = $book->getBookById($id);

        if (!$bookDetails) {
            throw new Exception("Book not found.");
        }

        if (!empty($bookDetails['cover_image'])) {
            $imagesToDelete[] = $bookDetails['cover_image'];
        }

        $imagesToDelete = array_merge(
            $imagesToDelete,
            $galleryImages
        );

        $conn->beginTransaction();

        $deleteImages = $conn->prepare("
            DELETE FROM book_images
            WHERE book_id = :id
        ");

        $deleteImages->execute([':id' => $id]);

        $success = $book->deleteBook($id);

        if (!$success) {
            throw new Exception("Unable to delete book.");
        }

        $conn->commit();

        deleteBookImageFiles($imagesToDelete);

        $message = "Book deleted successfully.";

    } else {

        throw new Exception("Invalid action.");

    }

} catch (Throwable $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Remove newly uploaded files if the operation fails
    deleteBookImageFiles($uploadedImages);

    if ($uploadedCover !== null) {
        deleteBookImageFiles([$uploadedCover]);
    }

    $success = false;
    $message = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Update | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <main class="section">

        <div class="details-card process-card">

            <?php if ($success): ?>

                <div class="process-icon">✓</div>

                <h2>Action Successful!</h2>

            <?php else: ?>

                <div class="process-error-icon">!</div>

                <h2>Action Unsuccessful</h2>

            <?php endif; ?>

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

            <a href="../admin/books.php" class="btn-gold">
                Back to Manage Books
            </a>

            <br><br>

            <a href="../admin/dashboard.php" class="back-link">
                Admin Dashboard
            </a>

        </div>

    </main>

</body>

</html>