<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid book ID.");
}

$bookQuery = $conn->prepare("
    SELECT id, title
    FROM books
    WHERE id = :id
");

$bookQuery->execute([':id' => $id]);
$bookDetails = $bookQuery->fetch(PDO::FETCH_ASSOC);

if (!$bookDetails) {
    die("Book not found.");
}

$imageQuery = $conn->prepare("
    SELECT id, image_path
    FROM book_images
    WHERE book_id = :book_id
    ORDER BY id ASC
");

$imageQuery->execute([':book_id' => $id]);
$bookImages = $imageQuery->fetchAll(PDO::FETCH_ASSOC);

$message = $_SESSION['image_message'] ?? "";
unset($_SESSION['image_message']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Book Images | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .image-item {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .image-item img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
            background: #f5e9d5;
        }

        .image-item p {
            overflow-wrap: anywhere;
        }

        .image-delete-button {
            background: #a32626;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .image-message {
            margin: 15px 0;
            padding: 12px;
            background: #f5e9d5;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<nav class="site-nav">
    <a href="../index.php" class="brand">OTUKPO E-LIBRARY</a>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php" class="login-btn">Logout</a>
    </div>
</nav>

<section class="catalogue-hero">
    <h1>Manage Book Images</h1>
    <p>
        <?php echo htmlspecialchars($bookDetails['title']); ?>
    </p>
</section>

<section class="section">

    <h2>Book Image Gallery</h2>

    <?php if ($message !== ""): ?>
        <div class="image-message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($bookImages)): ?>

        <div class="image-gallery">

            <?php foreach ($bookImages as $image): ?>

                <div class="image-item">

                    <img
                        src="../<?php echo htmlspecialchars($image['image_path']); ?>"
                        alt="Book image"
                    >

                    <p>
                        Image ID:
                        <?php echo (int)$image['id']; ?>
                    </p>

                    <form
                        action="../processes/delete_book_image.php"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this image?');"
                    >

                        <input
                            type="hidden"
                            name="image_id"
                            value="<?php echo (int)$image['id']; ?>"
                        >

                        <input
                            type="hidden"
                            name="book_id"
                            value="<?php echo (int)$id; ?>"
                        >

                        <button
                            type="submit"
                            class="image-delete-button"
                        >
                            Delete Image
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <p>No gallery images have been added to this book yet.</p>

    <?php endif; ?>

    <p style="margin-top: 30px;">
        <a href="edit_book.php?id=<?php echo (int)$id; ?>">
            Edit Book / Add More Images
        </a>
    </p>

    <p>
        <a href="books.php">Back to Manage Books</a>
    </p>

</section>

<footer class="site-footer">
    <h3>Otukpo E-Library</h3>
    <p>Knowledge, Learning and Discovery.</p>
    <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
</footer>

</body>
</html>