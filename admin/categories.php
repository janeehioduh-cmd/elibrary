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
    <title>Manage Categories | Otukpo E-Library</title>
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
        <h1>Manage Categories</h1>
        <p>Organise your library books into categories.</p>
    </section>

    <section class="section">

        <div class="details-card">

            <h2>Add New Category</h2>

            <form action="../processes/admin_category_process.php" method="POST">

                <input type="hidden" name="action" value="add">

                <label for="name">Category Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="Enter category name"
                    required
                >

                <button type="submit" class="btn-gold">
                    Add Category
                </button>

            </form>

        </div>

        <h2 style="margin-top: 50px;">Existing Categories</h2>

        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($categories)): ?>

                        <?php foreach ($categories as $row): ?>

                            <tr>
                                <td>
                                    <?php echo (int) $row['id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </td>

                                <td class="action-links">

                                    <a href="edit_category.php?id=<?php echo (int) $row['id']; ?>">
                                        Edit
                                    </a>

                                    <form
                                        action="../processes/admin_category_process.php"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this category?');"
                                    >

                                        <input type="hidden" name="action" value="delete">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int) $row['id']; ?>"
                                        >

                                        <button type="submit" class="delete-btn">
                                            Delete
                                        </button>

                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="3">No categories found.</td>
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