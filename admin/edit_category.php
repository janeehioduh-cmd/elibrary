<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Category.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid category ID.");
}

$db = new Database();
$conn = $db->connect();

$category = new Category($conn);
$categoryDetails = $category->getCategoryById($id);

if (!$categoryDetails) {
    die("Category not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category | Otukpo E-Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <nav class="site-nav">
        <a href="../index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="categories.php">Manage Categories</a>
            <a href="logout.php" class="login-btn">Logout</a>
        </div>
    </nav>

    <section class="catalogue-hero">
        <h1>Edit Category</h1>
        <p>Update your library category.</p>
    </section>

    <section class="section">

        <div class="details-card">

            <h2>Update Category Information</h2>

            <form action="../processes/admin_category_process.php" method="POST">

                <input type="hidden" name="action" value="edit">

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo (int) $categoryDetails['id']; ?>"
                >

                <label for="name">Category Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="<?php echo htmlspecialchars($categoryDetails['name']); ?>"
                    required
                >

                <button type="submit" class="btn-gold">
                    Update Category
                </button>

            </form>

            <p style="margin-top: 25px;">
                <a href="categories.php">Back to Manage Categories</a>
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