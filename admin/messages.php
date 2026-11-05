<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Contact.php";

$db = new Database();
$conn = $db->connect();

$contact = new Contact($conn);
$messages = $contact->getAllMessages();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Messages | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <main class="section">

        <div class="admin-page-heading">
            <div>
                <h1>Contact Messages</h1>
                <p>View messages sent by visitors and members.</p>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Date Received</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($messages)): ?>

                        <?php $sn = 1; ?>

                        <?php foreach ($messages as $row): ?>

                            <tr>

                                <td><?php echo $sn++; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['phone'] ?? 'Not provided'); ?>
                                </td>

                                <td class="message-cell">
                                    <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['created_at']); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="empty-table">
                                No contact messages received yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </main>

</body>

</html>