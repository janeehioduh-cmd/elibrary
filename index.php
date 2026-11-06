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

// Get approved testimonials
$testimonialQuery = $conn->prepare("
    SELECT testimonials.message, members.name
    FROM testimonials
    INNER JOIN members
        ON testimonials.member_id = members.id
    WHERE testimonials.status = 'approved'
    ORDER BY testimonials.id DESC
");

$testimonialQuery->execute();

$testimonials = $testimonialQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .testimonials-section {
            padding: 50px 20px;
            background: #f5e9d5;
            text-align: center;
        }

        .testimonials-section h2 {
            color: #3B2418;
            margin-bottom: 12px;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 22px;
            max-width: 1100px;
            margin: 30px auto 0;
        }

        .testimonial-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            text-align: left;
        }

        .testimonial-stars {
            color: #D4AF37;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .testimonial-message {
            color: #444;
            line-height: 1.7;
            margin-bottom: 18px;
            overflow-wrap: anywhere;
        }

        .testimonial-author {
            color: #3B2418;
            font-weight: bold;
        }

        .testimonial-empty {
            margin-top: 25px;
            color: #555;
        }
    </style>
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

        <div class="hero-overlay">

            <div class="hero-content">

                <p class="hero-label">WELCOME TO</p>

                <h1>OTUKPO E-LIBRARY</h1>

                <div class="gold-line"></div>

                <p class="hero-description">
                    Explore a world of knowledge, discover inspiring books
                    and expand your learning journey from anywhere.
                </p>

                <div class="hero-actions">

                    <a href="books.php" class="btn-gold">
                        Explore Our Books
                    </a>

                    <form method="GET" action="books.php" class="hero-search-form">

                        <input
                            type="text"
                            name="search"
                            placeholder="Search by title or author..."
                        >

                        <button type="submit">Search</button>

                    </form>

                </div>

            </div>

        </div>

    </section>

    <!-- Homepage Search -->
    <section class="home-search">

        <h2>Find Your Next Book</h2>

        <p>Search our library by book title or author.</p>

        <form method="GET" action="books.php" class="search-form">

            <input
                type="text"
                name="search"
                placeholder="Enter book title or author..."
            >

            <button type="submit">Search Books</button>

        </form>

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

    <!-- MEMBER TESTIMONIALS -->
    <section class="testimonials-section">

        <h2>What Our Members Say</h2>

        <p>
            Hear from members who use Otukpo E-Library.
        </p>

        <?php if (!empty($testimonials)): ?>

            <div class="testimonial-grid">

                <?php foreach ($testimonials as $row): ?>

                    <div class="testimonial-card">

                        <div class="testimonial-stars">
                            ★★★★★
                        </div>

                        <p class="testimonial-message">
                            "<?php echo nl2br(htmlspecialchars($row['message'])); ?>"
                        </p>

                        <p class="testimonial-author">
                            — <?php echo htmlspecialchars($row['name']); ?>
                        </p>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p class="testimonial-empty">
                Our members' experiences will appear here soon.
            </p>

        <?php endif; ?>

    </section>

    <!-- Footer -->
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