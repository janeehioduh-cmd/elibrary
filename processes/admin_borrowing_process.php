<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Borrowing.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/borrowings.php");
    exit();
}

$borrowing_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$borrowing_id || !in_array($action, ['approve', 'reject', 'return'], true)) {
    die("Invalid request.");
}

$db = new Database();
$conn = $db->connect();

$borrowing = new Borrowing($conn);

try {

    if ($action === 'approve') {

        $success = $borrowing->approveRequest($borrowing_id);

        $message = $success
            ? "Borrowing request approved successfully."
            : "Unable to approve this request.";

    } elseif ($action === 'reject') {

        $success = $borrowing->rejectRequest($borrowing_id);

        $message = $success
            ? "Borrowing request rejected successfully."
            : "Unable to reject this request.";

    } else {

        $success = $borrowing->returnBook($borrowing_id);

        $message = $success
            ? "Book returned successfully. The book is now available for other members."
            : "Unable to record this return.";
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Borrowing Update | Otukpo E-Library</title>

        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>

        <main class="section">

            <div class="details-card process-card">

                <?php if ($success): ?>

                    <div class="process-icon">✓</div>

                <?php else: ?>

                    <div class="process-error-icon">!</div>

                <?php endif; ?>

                <h2>
                    <?php
                    echo $success ? "Action Successful!" : "Action Unsuccessful";
                    ?>
                </h2>

                <p>
                    <?php echo htmlspecialchars($message); ?>
                </p>

                <a href="../admin/borrowings.php" class="btn-gold">
                    Back to Manage Borrowings
                </a>

                <br><br>

                <a href="dashboard.php" class="back-link">
                    Admin Dashboard
                </a>

            </div>

        </main>

    </body>

    </html>

    <?php

} catch (Exception $e) {

    echo "An error occurred while processing the borrowing request.";
    echo "<br><a href='../admin/borrowings.php'>Back to Borrowings</a>";

}

?>