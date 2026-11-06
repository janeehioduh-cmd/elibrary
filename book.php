<?php
session_start();

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/Book.php";
require_once __DIR__ . "/classes/Borrowing.php";

$db = new Database();
$conn = $db->connect();

$book = new Book($conn);
$borrowing = new Borrowing($conn);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid book ID.");
}

$bookDetails = $book->getBookById($id);

if (!$bookDetails) {
    die("Book not found.");
}

// Get all images belonging to this book
$imageQuery = $conn->prepare("
    SELECT image_path
    FROM book_images
    WHERE book_id = :book_id
    ORDER BY id ASC
");

$imageQuery->execute([
    ':book_id' => $id
]);

$bookImages = $imageQuery->fetchAll(PDO::FETCH_ASSOC);

// Check whether the logged-in member already has a pending request
$hasPendingRequest = false;

if (isset($_SESSION['member_id'])) {
    $hasPendingRequest = $borrowing->hasPendingRequest(
        $id,
        $_SESSION['member_id']
    );
}

// Select the correct main cover image
$mainImage = !empty($bookDetails['cover_image'])
    ? $bookDetails['cover_image']
    : (!empty($bookImages) ? $bookImages[0]['image_path'] : '');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Details | Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .book-gallery {
            margin: 25px 0;
            text-align: center;
        }

        .main-book-image {
            width: 100%;
            max-width: 450px;
            height: 350px;
            object-fit: contain;
            border-radius: 12px;
            background: #f5e9d5;
            padding: 10px;
        }

        .gallery-thumbnails {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 18px;
        }

        .gallery-thumbnails img {
            width: 90px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #d4af37;
            cursor: pointer;
        }

        .gallery-thumbnails img:hover {
            border-color: #3b2418;
            transform: scale(1.05);
        }

        .gallery-heading {
            margin-bottom: 15px;
        }
    </style>

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

        <h1>Book Details</h1>

        <p>Discover more about this book.</p>

    </section>

    <!-- BOOK DETAILS -->
    <section class="section">

        <div class="details-card">

            <!-- BOOK IMAGE GALLERY -->
            <?php if (!empty($mainImage)): ?>

                <div class="book-gallery">

                    <h3 class="gallery-heading">Book Image Gallery</h3>

                    <!-- MAIN COVER IMAGE -->
                    <img
                        id="mainBookImage"
                        src="<?php echo htmlspecialchars($mainImage); ?>"
                        alt="<?php echo htmlspecialchars($bookDetails['title']); ?>"
                        class="main-book-image"
                    >

                    <!-- GALLERY THUMBNAILS -->
                    <?php if (!empty($bookImages)): ?>

                        <div class="gallery-thumbnails">

                            <?php foreach ($bookImages as $image): ?>

                                <img
                                    src="<?php echo htmlspecialchars($image['image_path']); ?>"
                                    alt="Book image"
                                    class="gallery-thumbnail"
                                    onclick="changeBookImage(this)"
                                >

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="book-icon">📖</div>

            <?php endif; ?>

            <h2>
                <?php echo htmlspecialchars($bookDetails['title']); ?>
            </h2>

            <p>
                <strong>Author:</strong>
                <?php echo htmlspecialchars($bookDetails['author']); ?>
            </p>

            <p>
                <strong>Description:</strong><br>

                <?php
                echo htmlspecialchars(
                    $bookDetails['description'] ?? 'No description available.'
                );
                ?>
            </p>

            <p>
                <strong>Status:</strong>

                <?php if ($bookDetails['is_available']): ?>

                    <span class="available">Available</span>

                <?php else: ?>

                    <span class="unavailable">Unavailable</span>

                <?php endif; ?>

            </p>

            <!-- BORROWING SECTION -->
            <?php if ($bookDetails['is_available']): ?>

                <?php if (isset($_SESSION['member_id'])): ?>

                    <?php if ($hasPendingRequest): ?>

                        <div class="request-section">

                            <h3>Request Already Submitted</h3>

                            <p>
                                You already have a pending borrowing request
                                for this book. Please wait for the librarian
                                to review your request.
                            </p>

                            <a href="dashboard.php" class="book-link">
                                View My Dashboard
                            </a>

                        </div>

                    <?php else: ?>

                        <div class="request-section">

                            <h3>Request This Book</h3>

                            <form action="processes/borrow_process.php" method="POST">

                                <input
                                    type="hidden"
                                    name="book_id"
                                    value="<?php echo (int) $bookDetails['id']; ?>"
                                >

                                <label for="pickup_date">
                                    Preferred Pickup Date:
                                </label>

                                <input
                                    type="date"
                                    id="pickup_date"
                                    name="pickup_date"
                                    min="<?php echo date('Y-m-d'); ?>"
                                    required
                                    class="form-control"
                                >

                                <div class="terms-check">

                                    <input
                                        type="checkbox"
                                        id="terms_agreed"
                                        name="terms_agreed"
                                        value="1"
                                        required
                                    >

                                    <label for="terms_agreed">

                                        I agree to the

                                        <a href="terms.php" target="_blank">
                                            borrowing terms
                                        </a>.

                                    </label>

                                </div>

                                <button type="submit" class="btn-gold">
                                    Request Book
                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                <?php else: ?>

                    <p class="login-notice">

                        Please
                        <a href="login.php">login</a>
                        to request this book.

                    </p>

                <?php endif; ?>

            <?php else: ?>

                <p class="unavailable-notice">
                    This book is currently unavailable.
                    Please check back later.
                </p>

            <?php endif; ?>

            <!-- BACK TO BOOKS -->
            <a href="books.php" class="book-link">
                Back to Catalogue
            </a>

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

    <!-- GALLERY JAVASCRIPT -->
    <script>
        function changeBookImage(thumbnail) {
            document.getElementById('mainBookImage').src = thumbnail.src;
        }
    </script>

</body>

</html>