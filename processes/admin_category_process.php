<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Category.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/categories.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$category = new Category($conn);

$action = $_POST['action'] ?? '';

$success = false;
$message = '';

try {

    if ($action === 'add') {

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            throw new Exception("Category name is required.");
        }

        $success = $category->addCategory($name);

        $message = $success
            ? "Category added successfully."
            : "Unable to add category.";

    } elseif ($action === 'edit') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');

        if (!$id) {
            throw new Exception("Invalid category ID.");
        }

        if ($name === '') {
            throw new Exception("Category name is required.");
        }

        $success = $category->updateCategory($id, $name);

        $message = $success
            ? "Category updated successfully."
            : "Unable to update category.";

    } elseif ($action === 'delete') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            throw new Exception("Invalid category ID.");
        }

        $success = $category->deleteCategory($id);

        $message = $success
            ? "Category deleted successfully."
            : "Unable to delete category.";

    } else {

        throw new Exception("Invalid action.");

    }

} catch (Exception $e) {

    $success = false;
    $message = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Category Update | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <main class="section">

        <div class="details-card process-card">

            <?php if ($success): ?>

                <div class="process-icon">✓</div>

                <h2>Action Successful!</h2>

            <?php else: ?>

                <div class="process-error-icon">!</div>

                <h2>Action Unsuccessful</h2>

            <?php endif; ?>

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

            <a href="../admin/categories.php" class="btn-gold">
                Back to Manage Categories
            </a>

            <br><br>

            <a href="../admin/dashboard.php" class="back-link">
                Admin Dashboard
            </a>

        </div>

    </main>

</body>

</html>