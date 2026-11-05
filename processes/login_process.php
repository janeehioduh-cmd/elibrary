<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Member.php";

function showLoginMessage($title, $message) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?> | Otukpo E-Library</title>
        <link rel="stylesheet" href="../assets/css/style.css">

        <style>
            body {
                min-height: 100vh;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                box-sizing: border-box;
                background: #F8F1E7;
                font-family: Arial, sans-serif;
            }

            .message-card {
                width: 100%;
                max-width: 550px;
                padding: 40px 30px;
                box-sizing: border-box;
                text-align: center;
                background: #FFFFFF;
                border-top: 7px solid #D4AF37;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(43, 22, 15, 0.15);
            }

            .message-card h1 {
                margin: 0 0 18px;
                color: #2B160F;
                font-size: 30px;
                font-weight: 800;
            }

            .message-card p {
                margin: 0 0 28px;
                color: #704936;
                font-size: 17px;
                line-height: 1.6;
            }

            .message-card a {
                display: inline-block;
                padding: 14px 28px;
                border-radius: 6px;
                background: #2B160F;
                color: #D4AF37;
                font-weight: bold;
                text-decoration: none;
            }

            .message-card a:hover {
                background: #704936;
                color: #FFFFFF;
            }
        </style>
    </head>
    <body>
        <main class="message-card">
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="../login.php">Back to Login</a>
        </main>
    </body>
    </html>
    <?php
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

// Receive login details
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate fields
if ($email === '' || $password === '') {
    showLoginMessage(
        "Missing Information",
        "Please enter your email address and password."
    );
}

// Connect to database
$db = new Database();
$conn = $db->connect();

$member = new Member($conn);

// Verify login details
$memberDetails = $member->login($email, $password);

if (!$memberDetails) {
    showLoginMessage(
        "Login Failed",
        "The email address or password you entered is incorrect."
    );
}

// Check account status
if ($memberDetails['status'] !== 'active') {
    showLoginMessage(
        "Your Account Has Been Suspended",
        "Your account is currently suspended. Please contact the library administrator for assistance."
    );
}

// Regenerate session ID
session_regenerate_id(true);

// Store member information in session
$_SESSION['member_id'] = $memberDetails['id'];
$_SESSION['member_name'] = $memberDetails['name'];

// Redirect to dashboard
header("Location: ../dashboard.php");
exit();

?>