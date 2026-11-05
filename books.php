<?php
session_start();

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/Book.php";

$db = new Database();
$conn = $db->connect();

$book = new Book($conn);
$books = $book->getAllBooks();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Books | Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- NAVIGATION -->
    <nav class="site-nav">

        <a href="index.php" class="brand">
            OTUKPO E-LIBRARY
        </a>

        <div class="nav-links">

            <a href="index.php">Home</a>
            <a href="books.php">Books</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>

            <?php if (isset($_SESSION['member_id'])): ?>

                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="login-btn">Logout</a>

            <?php else: ?>

                <a href="register.php">Register</a>
                <a href="login.php" class="login-btn">Login</a>

            <?php endif; ?>

        </div>

    </nav>


    <!-- PAGE HEADER -->
    <section class="catalogue-hero">

        <h1>Our Book Catalogue</h1>

        <p>
            Explore our collection and discover your next favourite book.
        </p>

    </section>


    <!-- BOOK COLLECTION -->
    <section class="section books-section">

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

                        <?php
                        echo htmlspecialchars(
                            $row['category_name'] ?? 'Uncategorized'
                        );
                        ?>
                    </p>

                    <p>
                        <?php
                        echo htmlspecialchars($row['description'] ?? '');
                        ?>
                    </p>

                    <p>
                        <strong>Status:</strong>

                        <?php if ($row['is_available']): ?>

                            <span class="available">Available</span>

                        <?php else: ?>

                            <span class="unavailable">Unavailable</span>

                        <?php endif; ?>

                    </p>

                    <a
                        href="book.php?id=<?php echo (int) $row['id']; ?>"
                        class="book-link"
                    >
                        View Book Details
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    </section>


    <!-- FOOTER -->
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