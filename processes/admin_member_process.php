<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Member.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/members.php");
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['suspend', 'reactivate'], true)) {
    die("Invalid request.");
}

$db = new Database();
$conn = $db->connect();

$member = new Member($conn);

$success = false;
$message = '';

try {

    if ($action === 'suspend') {

        $reason = trim($_POST['reason'] ?? '');

        if ($reason === '') {
            throw new Exception("Please provide a reason for suspending this member.");
        }

        $success = $member->suspendMember($id, $reason);

        $message = $success
            ? "Member suspended successfully."
            : "Unable to suspend this member.";

    } elseif ($action === 'reactivate') {

        $success = $member->reactivateMember($id);

        $message = $success
            ? "Member reactivated successfully."
            : "Unable to reactivate this member.";

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

    <title>Member Account Update | Otukpo E-Library</title>

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

            <a href="../admin/members.php" class="btn-gold">
                Back to Manage Members
            </a>

            <br><br>

            <a href="../admin/dashboard.php" class="back-link">
                Admin Dashboard
            </a>

        </div>

    </main>

</body>

</html>