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
$borrowings = $borrowing->getAllBorrowings();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Borrowings | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<main class="section">

    <div class="admin-page-heading">
        <div>
            <h1>Manage Borrowings</h1>
            <p>Review and manage members' borrowing requests.</p>
        </div>

        <a href="dashboard.php" class="btn-gold">
            Back to Dashboard
        </a>
    </div>

    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Book Title</th>
                    <th>Member Name</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Pickup Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($borrowings)): ?>

                <?php $sn = 1; ?>

                <?php foreach ($borrowings as $row): ?>

                    <tr>

                        <td><?php echo $sn++; ?></td>

                        <td>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['member_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['borrow_date'] ?? 'Not yet approved'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['due_date'] ?? 'Not yet approved'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['preferred_pickup_date'] ?? 'Not specified'); ?>
                        </td>

                        <td>
                            <span class="borrowing-status status-<?php echo htmlspecialchars($row['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </span>
                        </td>

                        <td>

                            <div class="borrowing-actions">

                                <?php if ($row['status'] === 'pending'): ?>

                                    <form action="../processes/admin_borrowing_process.php" method="POST">

                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                        <input type="hidden" name="action" value="approve">

                                        <button type="submit" class="approve-btn">
                                            Approve
                                        </button>

                                    </form>

                                    <form action="../processes/admin_borrowing_process.php" method="POST">

                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                        <input type="hidden" name="action" value="reject">

                                        <button type="submit" class="delete-btn">
                                            Reject
                                        </button>

                                    </form>

                                <?php elseif ($row['status'] === 'borrowed'): ?>

                                    <form action="../processes/admin_borrowing_process.php" method="POST">

                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                        <input type="hidden" name="action" value="return">

                                        <button type="submit" class="return-btn">
                                            Mark Returned
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="no-action">No action</span>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="empty-table">
                        No borrowing records found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>