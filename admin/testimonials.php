<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$message = $_SESSION['testimonial_message'] ?? "";
unset($_SESSION['testimonial_message']);

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $testimonial_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $new_status = $_POST['status'] ?? '';

    if (!$testimonial_id || !in_array($new_status, ['approved', 'rejected'], true)) {

        $_SESSION['testimonial_message'] = "Invalid testimonial action.";

    } else {

        $sql = "UPDATE testimonials
                SET status = :status
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $updated = $stmt->execute([
            ':status' => $new_status,
            ':id' => $testimonial_id
        ]);

        if ($updated && $stmt->rowCount() > 0) {
            $_SESSION['testimonial_message'] =
                "Testimonial successfully " . $new_status . ".";
        } else {
            $_SESSION['testimonial_message'] =
                "No testimonial was updated. It may already have this status.";
        }
    }

    header("Location: testimonials.php");
    exit();
}

// Get all testimonials
$sql = "SELECT id, member_id, message, status
        FROM testimonials
        ORDER BY
        CASE WHEN status = 'pending' THEN 0 ELSE 1 END,
        id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();

$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Testimonials | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .testimonial-admin-section {
            padding: 40px 20px;
            background: #f5e9d5;
            min-height: 60vh;
        }

        .testimonial-admin-card {
            max-width: 850px;
            margin: 20px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .testimonial-admin-card p {
            margin: 12px 0;
        }

        .admin-message {
            max-width: 850px;
            margin: 0 auto 20px;
            padding: 15px;
            background: #e5f5e8;
            color: #17652b;
            border-radius: 6px;
        }

        .testimonial-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .testimonial-actions button {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .approve-button {
            background: #237a3b;
            color: white;
        }

        .reject-button {
            background: #b52b2b;
            color: white;
        }

        .testimonial-status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .pending-status {
            color: #b7791f;
        }

        .approved-status {
            color: #237a3b;
        }

        .rejected-status {
            color: #b52b2b;
        }

    </style>

</head>

<body>

    <!-- NAVIGATION -->
    <nav class="site-nav">

        <a href="dashboard.php" class="brand">
            OTUKPO E-LIBRARY ADMIN
        </a>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>

    </nav>

    <!-- PAGE HEADER -->
    <section class="catalogue-hero">

        <h1>Manage Testimonials</h1>

        <p>Review and manage member feedback.</p>

    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonial-admin-section">

        <?php if ($message !== ''): ?>

            <div class="admin-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($testimonials)): ?>

            <?php foreach ($testimonials as $row): ?>

                <div class="testimonial-admin-card">

                    <h2>Member Testimonial</h2>

                    <p>
                        <strong>Member ID:</strong>
                        <?php echo (int)$row['member_id']; ?>
                    </p>

                    <p>
                        <strong>Message:</strong>
                    </p>

                    <p>
                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                    </p>

                    <p>
                        <strong>Status:</strong>

                        <span class="testimonial-status <?php echo htmlspecialchars($row['status']); ?>-status">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </p>

                    <?php if ($row['status'] === 'pending'): ?>

                        <div class="testimonial-actions">

                            <form method="POST" action="testimonials.php">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo (int)$row['id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="status"
                                    value="approved"
                                >

                                <button type="submit" class="approve-button">
                                    Approve
                                </button>

                            </form>

                            <form method="POST" action="testimonials.php"
                                  onsubmit="return confirm('Are you sure you want to reject this testimonial?');">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo (int)$row['id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="status"
                                    value="rejected"
                                >

                                <button type="submit" class="reject-button">
                                    Reject
                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="testimonial-admin-card">
                <p>No testimonials have been submitted yet.</p>
            </div>

        <?php endif; ?>

        <p style="text-align:center; margin-top:25px;">
            <a href="dashboard.php">← Back to Admin Dashboard</a>
        </p>

    </section>

    <footer class="site-footer">

        <h3>Otukpo E-Library</h3>

        <p>Knowledge, Learning and Discovery.</p>

        <p>
            &copy; <?php echo date('Y'); ?>
            Otukpo E-Library. All rights reserved.
        </p>

    </footer>

</body>

</html>