<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Borrowing.php";

$db = new Database();
$conn = $db->connect();

$borrowing = new Borrowing($conn);
$overdueBooks = $borrowing->getOverdueBooks();

$finePerDay = 500;
$totalFine = 0;

foreach ($overdueBooks as $row) {
    $totalFine += (int) $row['days_overdue'] * $finePerDay;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Overdue Books | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<main class="section">

    <div class="admin-page-heading">
        <div>
            <h1>Overdue Books</h1>
            <p>View overdue books and their outstanding fines.</p>
        </div>

        <a href="dashboard.php" class="btn-gold">
            Back to Dashboard
        </a>
    </div>

    <div class="borrowing-limit">
        <h2>Total Outstanding Fines</h2>

        <div class="borrowing-count">
            ₦<?php echo number_format($totalFine, 2); ?>
        </div>

        <p>Fine rate: ₦500 per overdue day.</p>
    </div>

    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Member Name</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                    <th>Fine</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($overdueBooks)): ?>

                <?php $sn = 1; ?>

                <?php foreach ($overdueBooks as $row): ?>

                    <?php
                    $fine = (int) $row['days_overdue'] * $finePerDay;
                    ?>

                    <tr>

                        <td><?php echo $sn++; ?></td>

                        <td>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['author']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['member_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['due_date']); ?>
                        </td>

                        <td>
                            <strong>
                                <?php echo (int) $row['days_overdue']; ?> days
                            </strong>
                        </td>

                        <td>
                            <strong>
                                ₦<?php echo number_format($fine, 2); ?>
                            </strong>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="empty-table">
                        No overdue books found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>