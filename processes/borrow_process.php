<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Borrowing.php";

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../books.php");
    exit();
}

if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
$pickup_date = $_POST['pickup_date'] ?? '';
$terms_agreed = isset($_POST['terms_agreed']) ? 1 : 0;

if (!$book_id || $book_id < 1) {
    $message = "Invalid book selected.";

} else {

    $date = DateTime::createFromFormat('!Y-m-d', $pickup_date);

    if (!$date || $date->format('Y-m-d') !== $pickup_date) {
        $message = "Please enter a valid pickup date.";

    } elseif ($pickup_date < date('Y-m-d')) {
        $message = "Pickup date cannot be in the past.";

    } elseif (!$terms_agreed) {
        $message = "You must agree to the borrowing terms.";

    } else {

        $db = new Database();
        $conn = $db->connect();

        $borrowing = new Borrowing($conn);

        $result = $borrowing->requestBook(
            $book_id,
            $_SESSION['member_id'],
            $pickup_date,
            $terms_agreed
        );

        if ($result) {
            $success = true;
            $message = "Your borrowing request has been submitted successfully. Please wait for the librarian to approve your request.";
        } else {
            $message = "Request failed. The book may be unavailable or your account may not be active.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowing Request | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <nav class="site-nav">
        <a href="../index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../books.php">Books</a>
            <a href="../dashboard.php">My Dashboard</a>
        </div>
    </nav>

    <section class="section">

        <div class="details-card process-card">

            <?php if ($success): ?>

                <div class="process-icon">✓</div>

                <h2>Request Submitted!</h2>

                <p><?php echo htmlspecialchars($message); ?></p>

                <a href="../dashboard.php" class="book-link">
                    Go to Dashboard
                </a>

            <?php else: ?>

                <div class="process-error-icon">!</div>

                <h2>Request Not Completed</h2>

                <p><?php echo htmlspecialchars($message); ?></p>

                <a href="../books.php" class="book-link">
                    Return to Books
                </a>

            <?php endif; ?>

        </div>

    </section>

    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>
        <p>Knowledge, Learning and Discovery.</p>
        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>