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

try {

    if ($action === 'add') {

        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($title === '' || $author === '') {
            throw new Exception("Book title and author are required.");
        }

        $success = $book->addBook(
            $title,
            $author,
            $category_id ?: null,
            $description,
            null
        );

        $message = $success
            ? "Book added successfully."
            : "Unable to add book.";

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

        $success = $book->updateBook(
            $id,
            $title,
            $author,
            $category_id ?: null,
            $description
        );

        $message = $success
            ? "Book updated successfully."
            : "Unable to update book.";

    } elseif ($action === 'delete') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            throw new Exception("Invalid book ID.");
        }

        $success = $book->deleteBook($id);

        $message = $success
            ? "Book deleted successfully."
            : "Unable to delete book.";

    } else {

        throw new Exception("Invalid action.");

    }

} catch (Exception $e) {

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