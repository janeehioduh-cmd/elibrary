<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/Book.php";
require_once __DIR__ . "/classes/Category.php";

$db = new Database();
$conn = $db->connect();

$book = new Book($conn);
$category = new Category($conn);

$books = $book->getAllBooks();
$categories = $category->getAllCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- Navigation -->
    <nav class="site-nav">
        <a href="index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="books.php">Books</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="register.php">Register</a>
            <a href="login.php" class="login-btn">Login</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <p class="hero-label">WELCOME TO OTUKPO E-LIBRARY</p>

            <h1>Discover the World Through Books</h1>

            <p>
                Explore knowledge, discover new ideas and borrow
                books from the comfort of your home.
            </p>

            <a href="books.php" class="btn-gold">
                Explore Our Books
            </a>
        </div>
    </section>

    <!-- Categories -->
    <section class="section">
        <h2>Explore Book Categories</h2>

        <p class="section-description">
            Find books across different fields of knowledge.
        </p>

        <div class="category-list">
            <?php foreach ($categories as $row): ?>

                <span class="category-chip">
                    <?php echo htmlspecialchars($row['name']); ?>
                </span>

            <?php endforeach; ?>
        </div>
    </section>

    <!-- Available Books -->
    <section class="section books-section">

        <h2>Available Books</h2>

        <p class="section-description">
            Discover books selected to support your learning journey.
        </p>

        <div class="book-grid">

            <?php foreach ($books as $row): ?>

                <div class="book-card">

                    <div class="book-icon">📚</div>

                    <h3>
                        <?php echo htmlspecialchars($row['title']); ?>
                    </h3>

                    <p>
                        <strong>Author:</strong>
                        <?php echo htmlspecialchars($row['author']); ?>
                    </p>

                    <p>
                        <strong>Category:</strong>
                        <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>
                    </p>

                    <p>
                        <?php echo htmlspecialchars($row['description'] ?? ''); ?>
                    </p>

                    <a href="book.php?id=<?php echo (int) $row['id']; ?>"
                       class="book-link">
                        View Book
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>

        <p>Knowledge, Learning and Discovery.</p>

        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>