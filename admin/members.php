<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Member.php";

$db = new Database();
$conn = $db->connect();

$member = new Member($conn);
$members = $member->getAllMembers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members | Otukpo E-Library</title>
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
        <h1>Manage Members</h1>
        <p>View and manage registered library members.</p>
    </section>

    <section class="section">

        <h2>Registered Members</h2>

        <p class="section-description">
            Manage member accounts and their access to the library.
        </p>

        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($members)): ?>

                        <?php foreach ($members as $row): ?>

                            <tr>

                                <td><?php echo (int) $row['id']; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['phone']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['date_joined']); ?>
                                </td>

                                <td>
                                    <?php if ($row['status'] === 'active'): ?>
                                        <span class="member-active">Active</span>
                                    <?php else: ?>
                                        <span class="member-suspended">Suspended</span>
                                    <?php endif; ?>
                                </td>

                                <td>

                                    <?php if ($row['status'] === 'active'): ?>

                                        <form
                                            action="../processes/admin_member_process.php"
                                            method="POST"
                                            class="member-action-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int) $row['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="suspend"
                                            >

                                            <label>Suspension Reason</label>

                                            <textarea
                                                name="reason"
                                                rows="3"
                                                class="form-control"
                                                placeholder="Enter reason for suspension"
                                                required
                                            ></textarea>

                                            <button
                                                type="submit"
                                                class="delete-btn"
                                                onclick="return confirm('Are you sure you want to suspend this member?');"
                                            >
                                                Suspend Member
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <form
                                            action="../processes/admin_member_process.php"
                                            method="POST"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int) $row['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="reactivate"
                                            >

                                            <button
                                                type="submit"
                                                class="reactivate-btn"
                                                onclick="return confirm('Reactivate this member?');"
                                            >
                                                Reactivate Member
                                            </button>

                                        </form>

                                        <p class="suspension-reason">
                                            <strong>Reason:</strong>
                                            <?php echo htmlspecialchars($row['suspension_reason'] ?? 'Not provided'); ?>
                                        </p>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7">No registered members found.</td>
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