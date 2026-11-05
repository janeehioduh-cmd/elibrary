<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Book.php";

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
    <title>Manage Books | Otukpo E-Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
        <h1>Manage Books</h1>
        <p>Add, view, edit and manage library books.</p>
    </section>

    <section class="section">

        <div class="admin-page-heading">
            <h2>Library Books</h2>
            <a href="add_book.php" class="btn-gold">Add New Book</a>
        </div>

        <div class="admin-table-wrapper">

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($books)): ?>

                        <?php foreach ($books as $row): ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>

                                <td><?php echo htmlspecialchars($row['title']); ?></td>

                                <td><?php echo htmlspecialchars($row['author']); ?></td>

                                <td>
                                    <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>
                                </td>

                                <td>
                                    <?php if ($row['is_available']): ?>
                                        <span class="available">Available</span>
                                    <?php else: ?>
                                        <span class="unavailable">Borrowed</span>
                                    <?php endif; ?>
                                </td>

                                <td class="action-links">

                                    <a href="edit_book.php?id=<?php echo $row['id']; ?>">
                                        Edit
                                    </a>

                                    <form action="../processes/admin_book_process.php"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this book?');">

                                        <input type="hidden" name="action" value="delete">

                                        <input type="hidden" name="id"
                                               value="<?php echo $row['id']; ?>">

                                        <button type="submit" class="delete-btn">
                                            Delete
                                        </button>

                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6">No books have been added yet.</td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

        <p style="margin-top: 30px;">
            <a href="dashboard.php">Back to Admin Dashboard</a>
        </p>

    </section>

    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>
        <p>Knowledge, Learning and Discovery.</p>
        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>