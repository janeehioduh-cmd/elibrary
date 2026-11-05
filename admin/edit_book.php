<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Book.php";
require_once __DIR__ . "/../classes/Category.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid book ID.");
}

$db = new Database();
$conn = $db->connect();

$book = new Book($conn);
$category = new Category($conn);

$bookDetails = $book->getBookById($id);
$categories = $category->getAllCategories();

if (!$bookDetails) {
    die("Book not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book | Otukpo E-Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <nav class="site-nav">
        <a href="../index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="books.php">Manage Books</a>
            <a href="logout.php" class="login-btn">Logout</a>
        </div>
    </nav>

    <section class="catalogue-hero">
        <h1>Edit Book</h1>
        <p>Update information about this library book.</p>
    </section>

    <section class="section">

        <div class="details-card">

            <h2>Update Book Information</h2>

            <form action="../processes/admin_book_process.php" method="POST">

                <input type="hidden" name="action" value="edit">

                <input type="hidden" name="id"
                       value="<?php echo (int) $bookDetails['id']; ?>">

                <label for="title">Book Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    value="<?php echo htmlspecialchars($bookDetails['title']); ?>"
                    required
                >

                <label for="author">Author</label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    class="form-control"
                    value="<?php echo htmlspecialchars($bookDetails['author']); ?>"
                    required
                >

                <label for="category_id">Category</label>

                <select
                    id="category_id"
                    name="category_id"
                    class="form-control"
                >

                    <option value="">Select Category</option>

                    <?php foreach ($categories as $cat): ?>

                        <option
                            value="<?php echo (int) $cat['id']; ?>"
                            <?php echo ($bookDetails['category_id'] == $cat['id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <label for="description">Description</label>

                <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    rows="5"
                ><?php echo htmlspecialchars($bookDetails['description'] ?? ''); ?></textarea>

                <button type="submit" class="btn-gold">
                    Update Book
                </button>

            </form>

            <p style="margin-top: 25px;">
                <a href="books.php">Back to Manage Books</a>
            </p>

        </div>

    </section>

    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>
        <p>Knowledge, Learning and Discovery.</p>
        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>