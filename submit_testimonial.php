<?php

session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/config/database.php";

$db = new Database();
$conn = $db->connect();

$message = "";
$success = false;
$testimonial = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $testimonial = trim($_POST['message'] ?? '');

    if ($testimonial === '') {
        $message = "Please enter your testimonial.";

    } elseif (strlen($testimonial) < 10) {
        $message = "Your testimonial must contain at least 10 characters.";

    } else {

        $sql = "INSERT INTO testimonials (member_id, message, status)
                VALUES (:member_id, :message, 'pending')";

        $stmt = $conn->prepare($sql);

        $result = $stmt->execute([
            ':member_id' => $_SESSION['member_id'],
            ':message' => $testimonial
        ]);

        if ($result) {
            $success = true;
            $message = "Thank you! Your testimonial has been submitted and is awaiting admin approval.";
            $testimonial = "";
        } else {
            $message = "Something went wrong. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submit Testimonial | Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .testimonial-page {
            min-height: 70vh;
            padding: 40px 20px;
            background: #f5e9d5;
        }

        .testimonial-form-card {
            display: block;
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            padding: 30px;
            background: #ffffff;
            color: #3B2418;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .testimonial-form-card h2 {
            margin-bottom: 15px;
            color: #3B2418;
        }

        .testimonial-form-card p {
            margin-bottom: 18px;
        }

        .testimonial-form-card label {
            display: block;
            margin-top: 20px;
            font-weight: bold;
        }

        .testimonial-form-card textarea {
            display: block;
            width: 100%;
            min-height: 180px;
            padding: 15px;
            margin: 12px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            font: inherit;
            box-sizing: border-box;
            background: #fff;
            color: #333;
        }

        .testimonial-form-card button {
            display: inline-block;
            padding: 12px 22px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .testimonial-message {
            padding: 12px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .testimonial-success {
            background: #e5f5e8;
            color: #17652b;
        }

        .testimonial-error {
            background: #fce8e8;
            color: #9b1c1c;
        }

        .testimonial-back {
            display: inline-block;
            margin-top: 25px;
            color: #3B2418;
            font-weight: bold;
        }
    </style>

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
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php" class="login-btn">Logout</a>

        </div>

    </nav>

    <!-- PAGE HEADER -->
    <section class="catalogue-hero">

        <h1>Share Your Experience</h1>

        <p>Tell us what you think about Otukpo E-Library.</p>

    </section>

    <!-- TESTIMONIAL FORM -->
    <section class="testimonial-page">

        <div class="testimonial-form-card">

            <h2>Write a Testimonial</h2>

            <p>
                Your feedback helps us improve our library services.
                Share your experience with us below.
            </p>

            <?php if ($message !== ''): ?>

                <div class="testimonial-message <?php echo $success ? 'testimonial-success' : 'testimonial-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>

            <?php if (!$success): ?>

                <form method="POST" action="submit_testimonial.php">

                    <label for="message">
                        Your Testimonial:
                    </label>

                    <textarea
                        name="message"
                        id="message"
                        placeholder="Share your experience with our library..."
                        minlength="10"
                        maxlength="1000"
                        required
                    ><?php echo htmlspecialchars($testimonial); ?></textarea>

                    <button type="submit" class="btn-gold">
                        Submit Testimonial
                    </button>

                </form>

            <?php endif; ?>

            <a href="dashboard.php" class="testimonial-back">
                ← Back to Dashboard
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