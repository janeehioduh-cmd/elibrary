<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Category.php";

$db = new Database();
$conn = $db->connect();

$category = new Category($conn);
$categories = $category->getAllCategories();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Book | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <nav class="site-nav">

        <a href="../index.php" class="brand">
            OTUKPO E-LIBRARY
        </a>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="books.php">Manage Books</a>
        </div>

    </nav>

    <section class="catalogue-hero">

        <h1>Add New Book</h1>

        <p>Add a new book and its images to the library collection.</p>

    </section>

    <section class="section">

        <div class="details-card">

            <h2>Book Information</h2>

            <form
                action="../processes/admin_book_process.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <input type="hidden" name="action" value="add">

                <!-- BOOK TITLE -->
                <label for="title">Book Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    placeholder="Enter book title"
                    required
                >

                <!-- AUTHOR -->
                <label for="author">Author</label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    class="form-control"
                    placeholder="Enter author's name"
                    required
                >

                <!-- CATEGORY -->
                <label for="category_id">Book Category</label>

                <select
                    id="category_id"
                    name="category_id"
                    class="form-control"
                >

                    <option value="">Select a category</option>

                    <?php foreach ($categories as $row): ?>

                        <option value="<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <!-- DESCRIPTION -->
                <label for="description">Book Description</label>

                <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    rows="5"
                    placeholder="Enter book description"
                ></textarea>

                <!-- MULTIPLE BOOK IMAGES -->
                <label for="book_images">
                    Upload Book Images
                </label>

                <p>
                    You can select multiple images for this book.
                </p>

                <input
                    type="file"
                    id="book_images"
                    name="book_images[]"
                    class="form-control"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                >

                <p style="font-size: 13px; margin-top: 8px;">
                    Accepted formats: JPG, PNG and WEBP.
                </p>

                <button type="submit" class="btn-gold">
                    Add Book
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

        <p>
            &copy; <?php echo date('Y'); ?>
            Otukpo E-Library. All rights reserved.
        </p>

    </footer>

</body>

</html>