<?php

session_start();

// Protect the admin dashboard
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

// Count total books
$books = $conn->query("SELECT COUNT(*) FROM books")->fetchColumn();

// Count total members
$members = $conn->query("SELECT COUNT(*) FROM members")->fetchColumn();

// Count pending requests
$pending = $conn->query(
    "SELECT COUNT(*) FROM borrowings WHERE status = 'pending'"
)->fetchColumn();

// Count borrowed books
$borrowed = $conn->query(
    "SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed'"
)->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Otukpo E-Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <nav class="site-nav">
        <a href="../index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="../index.php">View Website</a>
            <a href="logout.php" class="login-btn">Logout</a>
        </div>
    </nav>

    <section class="catalogue-hero">
        <h1>Admin Dashboard</h1>
        <p>Manage your library from one place.</p>
    </section>

    <section class="section">

        <h2>Welcome, Administrator!</h2>

        <p class="section-description">
            Here is an overview of your library activities.
        </p>

        <div class="stats-grid">

            <div class="stat-card">
                <h3>Total Books</h3>
                <p><?php echo $books; ?></p>
            </div>

            <div class="stat-card">
                <h3>Total Members</h3>
                <p><?php echo $members; ?></p>
            </div>

            <div class="stat-card">
                <h3>Pending Requests</h3>
                <p><?php echo $pending; ?></p>
            </div>

            <div class="stat-card">
                <h3>Currently Borrowed</h3>
                <p><?php echo $borrowed; ?></p>
            </div>

        </div>

        <h2 style="margin-top: 50px;">Management Menu</h2>

        <p class="section-description">
            Select what you want to manage.
        </p>

        <div class="management-grid">

            <a href="books.php" class="management-card">
                <span>📚</span>
                <h3>Manage Books</h3>
                <p>Add, edit and delete library books.</p>
            </a>

            <a href="categories.php" class="management-card">
                <span>📂</span>
                <h3>Manage Categories</h3>
                <p>Organise books into categories.</p>
            </a>

            <a href="members.php" class="management-card">
                <span>👥</span>
                <h3>Manage Members</h3>
                <p>View, suspend and reactivate members.</p>
            </a>

            <a href="borrowings.php" class="management-card">
                <span>📖</span>
                <h3>Borrowing Requests</h3>
                <p>Approve, reject and record book returns.</p>
            </a>

            <a href="messages.php" class="management-card">
                <span>✉️</span>
                <h3>Contact Messages</h3>
                <p>View messages sent by visitors.</p>
            </a>

        </div>

    </section>

    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>
        <p>Knowledge, Learning and Discovery.</p>
        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>