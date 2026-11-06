<?php

session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/Member.php";
require_once __DIR__ . "/classes/Borrowing.php";
require_once __DIR__ . "/classes/Fine.php";

$db = new Database();
$conn = $db->connect();

$member = new Member($conn);
$borrowing = new Borrowing($conn);
$fine = new Fine($conn);

$memberDetails = $member->getMemberById($_SESSION['member_id']);

if (!$memberDetails || $memberDetails['status'] !== 'active') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$borrowings = $borrowing->getMemberBorrowings($_SESSION['member_id']);

$fine->updateOverdueFines();

$totalFine = $fine->getMemberOutstandingFine($_SESSION['member_id']);

$memberFines = $fine->getMemberFines($_SESSION['member_id']);

$activeBorrowings = $borrowing->getActiveBorrowingCount($_SESSION['member_id']);

$borrowingLimit = 3;
$remainingBooks = max(0, $borrowingLimit - $activeBorrowings);

$profileImage = $memberDetails['profile_image'] ?? null;

$profileImagePath = "uploads/profiles/" . basename($profileImage ?? '');

$hasProfileImage = !empty($profileImage)
    && is_file(__DIR__ . "/" . $profileImagePath);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Member Dashboard | Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- NAVIGATION -->
    <nav class="site-nav">

        <a href="index.php" class="brand">
            OTUKPO E-LIBRARY
        </a>

        <div class="nav-links">

            <a href="index.php">Home</a>
            <a href="books.php">Books</a>
            <a href="dashboard.php">My Dashboard</a>
            <a href="logout.php" class="login-btn">Logout</a>

        </div>

    </nav>

    <!-- PAGE HEADER -->
    <section class="catalogue-hero">

        <h1>Member Dashboard</h1>

        <p>
            Welcome back,
            <?php echo htmlspecialchars($memberDetails['name']); ?>!
        </p>

    </section>

    <!-- DASHBOARD CONTENT -->
    <section class="section">

        <div class="details-card">

            <h2>My Account</h2>

            <!-- PROFILE PICTURE -->
            <div class="profile-section">

                <?php if ($hasProfileImage): ?>

                    <img
                        src="<?php echo htmlspecialchars($profileImagePath); ?>"
                        alt="Member Profile Picture"
                        class="profile-picture"
                    >

                <?php else: ?>

                    <div class="profile-placeholder">
                        👤
                    </div>

                <?php endif; ?>

                <h3>
                    <?php echo htmlspecialchars($memberDetails['name']); ?>
                </h3>

                <p>Member Profile Picture</p>

                <form
                    action="processes/profile_image_process.php"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <label for="profile_image">
                        Upload or Change Profile Picture
                    </label>

                    <input
                        type="file"
                        id="profile_image"
                        name="profile_image"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                        required
                    >

                    <button type="submit" class="btn-gold">
                        Update Profile Picture
                    </button>

                </form>

            </div>

            <!-- ACCOUNT INFORMATION -->
            <div class="account-info">

                <p>
                    <strong>Full Name:</strong>
                    <?php echo htmlspecialchars($memberDetails['name']); ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?php echo htmlspecialchars($memberDetails['email']); ?>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <?php echo htmlspecialchars($memberDetails['phone']); ?>
                </p>

                <p>
                    <strong>Account Status:</strong>
                    <span class="available">Active</span>
                </p>

            </div>

            <!-- BORROWING LIMIT SUMMARY -->
            <div class="borrowing-limit">

                <h2>My Borrowing Allowance</h2>

                <div class="borrowing-count">
                    <?php echo $activeBorrowings; ?>
                    /
                    <?php echo $borrowingLimit; ?>
                </div>

                <p>Books currently borrowed</p>

                <p>
                    <strong><?php echo $remainingBooks; ?></strong>
                    borrowing slot(s) remaining.
                </p>

                <?php if ($activeBorrowings >= $borrowingLimit): ?>

                    <p class="limit-warning">
                        You have reached your borrowing limit.
                        Please return a book before requesting another.
                    </p>

                <?php else: ?>

                    <p class="limit-success">
                        You can borrow
                        <?php echo $remainingBooks; ?>
                        more book(s).
                    </p>

                <?php endif; ?>

            </div>

            <!-- OUTSTANDING FINES -->
            <div class="borrowing-limit">

                <h2>My Outstanding Fines</h2>

                <div class="borrowing-count">
                    ₦<?php echo number_format($totalFine, 2); ?>
                </div>

                <p>Fine rate: ₦500 per overdue day.</p>

                <?php if ($totalFine > 0): ?>

                    <p class="limit-warning">
                        You have unpaid fines. Please contact the library administrator about your fine.
                    </p>

                <?php else: ?>

                    <p class="limit-success">
                        You currently have no outstanding fines.
                    </p>

                <?php endif; ?>

            </div>

            <!-- FINE PAYMENT HISTORY -->
            <div class="borrowing-limit">

                <h2>My Fine History</h2>

                <?php if (!empty($memberFines)): ?>

                    <?php foreach ($memberFines as $row): ?>

                        <div class="borrowing-card">

                            <h3>
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h3>

                            <p>
                                <strong>Fine Amount:</strong>
                                ₦<?php echo number_format($row['amount'], 2); ?>
                            </p>

                            <p>
                                <strong>Payment Status:</strong>

                                <?php if ($row['status'] === 'paid'): ?>

                                    <span class="limit-success">Paid</span>

                                <?php else: ?>

                                    <span class="limit-warning">Unpaid</span>

                                <?php endif; ?>

                            </p>

                            <p>
                                <strong>Payment Date:</strong>
                                <?php echo htmlspecialchars($row['paid_at'] ?? 'Not paid'); ?>
                            </p>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>You have no fine records yet.</p>

                <?php endif; ?>

            </div>

            <!-- BORROWING ACTIVITIES -->
            <h2>My Borrowing Activities</h2>

            <?php if (count($borrowings) > 0): ?>

                <div class="borrowing-list">

                    <?php foreach ($borrowings as $row): ?>

                        <div class="borrowing-card">

                            <div class="book-icon">📚</div>

                            <h3>
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h3>

                            <p>
                                <strong>Author:</strong>
                                <?php echo htmlspecialchars($row['author']); ?>
                            </p>

                            <p>
                                <strong>Status:</strong>

                                <span class="status-<?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                </span>
                            </p>

                            <p>
                                <strong>Borrow Date:</strong>
                                <?php echo htmlspecialchars($row['borrow_date'] ?? 'Not yet approved'); ?>
                            </p>

                            <p>
                                <strong>Due Date:</strong>
                                <?php echo htmlspecialchars($row['due_date'] ?? 'Not yet approved'); ?>
                            </p>

                            <p>
                                <strong>Return Date:</strong>
                                <?php echo htmlspecialchars($row['return_date'] ?? 'Not yet returned'); ?>
                            </p>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p>You have not requested any books yet.</p>

            <?php endif; ?>

            <!-- SHARE YOUR EXPERIENCE -->
            <div class="borrowing-limit">

                <h2>Share Your Experience</h2>

                <p>
                    Tell us what you think about Otukpo E-Library.
                    Your feedback can help us improve our services.
                </p>

                <a href="submit_testimonial.php" class="btn-gold">
                    Share Your Experience
                </a>

            </div>

            <a href="books.php" class="book-link">
                Browse Books
            </a>

        </div>

    </section>

    <!-- FOOTER -->
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