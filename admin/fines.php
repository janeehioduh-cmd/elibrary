<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Fine.php";

$db = new Database();
$conn = $db->connect();

$fine = new Fine($conn);

// Update overdue fines before displaying them
$fine->updateOverdueFines();

$fines = $fine->getAllFines();

$totalOutstanding = 0;
$totalCollected = 0;

foreach ($fines as $row) {

    if ($row['status'] === 'unpaid') {
        $totalOutstanding += $row['amount'];
    } else {
        $totalCollected += $row['amount'];
    }
}

// Get notification messages
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fine Management | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<main class="section">

    <div class="admin-page-heading">

        <div>
            <h1>Fine Management</h1>
            <p>Manage member overdue fines and payments.</p>
        </div>

        <a href="dashboard.php" class="btn-gold">
            Back to Dashboard
        </a>

    </div>

    <!-- NOTIFICATIONS -->

    <?php if (!empty($successMessage)): ?>

        <div class="limit-success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>

    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>

        <div class="limit-warning">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>

    <?php endif; ?>

    <!-- FINE SUMMARY -->
    <div class="stats-grid">

        <div class="stat-card">
            <h3>Outstanding Fines</h3>
            <p>₦<?php echo number_format($totalOutstanding, 2); ?></p>
        </div>

        <div class="stat-card">
            <h3>Fines Collected</h3>
            <p>₦<?php echo number_format($totalCollected, 2); ?></p>
        </div>

    </div>

    <!-- FINE TABLE -->
    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Book Title</th>
                    <th>Member Name</th>
                    <th>Fine Amount</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($fines)): ?>

                <?php $sn = 1; ?>

                <?php foreach ($fines as $row): ?>

                    <tr>

                        <td><?php echo $sn++; ?></td>

                        <td>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['member_name']); ?>
                        </td>

                        <td>
                            ₦<?php echo number_format($row['amount'], 2); ?>
                        </td>

                        <td>
                            <strong>
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['paid_at'] ?? 'Not paid'); ?>
                        </td>

                        <td>

                            <?php if ($row['status'] === 'unpaid'): ?>

                                <form action="../processes/fine_process.php" method="POST">

                                    <input
                                        type="hidden"
                                        name="fine_id"
                                        value="<?php echo (int) $row['id']; ?>"
                                    >

                                    <button type="submit" class="approve-btn">
                                        Mark as Paid
                                    </button>

                                </form>

                            <?php else: ?>

                                <span class="no-action">
                                    Payment Recorded
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="empty-table">
                        No fine records found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>