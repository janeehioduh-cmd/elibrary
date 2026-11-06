<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/email.php";
require_once __DIR__ . "/../classes/Borrowing.php";
require_once __DIR__ . "/../classes/Fine.php";

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
        $fine = new Fine($conn);

        // Update overdue fine records
        $fine->updateOverdueFines();

        // Check outstanding fines
        $totalFine = $fine->getMemberOutstandingFine(
            $_SESSION['member_id']
        );

        if ($totalFine > 0) {

            $message = "You have outstanding fines of ₦"
                . number_format($totalFine, 2)
                . ". Please settle your fines with the library administrator before requesting another book.";

        } else {

            // Check for an existing pending request
            if ($borrowing->hasPendingRequest(
                $book_id,
                $_SESSION['member_id']
            )) {

                $message = "You already have a pending borrowing request for this book. Please wait for the librarian to review your existing request.";

            } else {

                // Check current borrowed books
                $activeCount = $borrowing->getActiveBorrowingCount(
                    $_SESSION['member_id']
                );

                if ($activeCount >= 3) {

                    $message = "You have reached the borrowing limit of 3 books. Please return a borrowed book before requesting another.";

                } else {

                    // Submit borrowing request
                    $result = $borrowing->requestBook(
                        $book_id,
                        $_SESSION['member_id'],
                        $pickup_date,
                        $terms_agreed
                    );

                    if ($result) {

                        $success = true;

                        $message = "Your borrowing request has been submitted successfully. Please wait for the librarian to approve your request.";

                        // Get member name and book title
                        $detailsQuery = $conn->prepare("
                            SELECT
                                members.name AS member_name,
                                books.title AS book_title
                            FROM members
                            CROSS JOIN books
                            WHERE members.id = :member_id
                            AND books.id = :book_id
                        ");

                        $detailsQuery->execute([
                            ':member_id' => $_SESSION['member_id'],
                            ':book_id' => $book_id
                        ]);

                        $details = $detailsQuery->fetch(PDO::FETCH_ASSOC);

                        // Send email notification to librarian
                        if ($details) {

                            $memberName = htmlspecialchars(
                                $details['member_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $bookTitle = htmlspecialchars(
                                $details['book_title'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $formattedDate = htmlspecialchars(
                                date('d F Y', strtotime($pickup_date)),
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $emailSubject = "New Book Borrowing Request - Otukpo E-Library";

                            $emailMessage = "
                                <h2>New Borrowing Request</h2>

                                <p>A member has submitted a new book borrowing request.</p>

                                <p><strong>Member Name:</strong> {$memberName}</p>

                                <p><strong>Book Title:</strong> {$bookTitle}</p>

                                <p><strong>Pickup Date:</strong> {$formattedDate}</p>

                                <p><strong>Member ID:</strong> "
                                . (int) $_SESSION['member_id'] .
                                "</p>

                                <p>Please log in to the E-Library admin dashboard
                                to review this request.</p>

                                <p>Otukpo E-Library Management System</p>
                            ";

                            $emailSent = sendLibrarianEmail(
                                $emailSubject,
                                $emailMessage
                            );

                            if (!$emailSent) {
                                error_log(
                                    "Borrowing request email notification failed."
                                );
                            }

                        } else {

                            error_log(
                                "Could not retrieve borrowing request details for email."
                            );
                        }

                    } else {

                        $message = "Request failed. The book may be unavailable or your account may not be active.";
                    }
                }
            }
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