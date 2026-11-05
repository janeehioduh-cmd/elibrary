<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Contact.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../contact.php");
    exit();
}

// Receive form details
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate required fields
if ($name === '' || $email === '' || $message === '') {
    die("Please fill in all required fields.");
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}

// Connect to database
$db = new Database();
$conn = $db->connect();

$contact = new Contact($conn);

// Save message
if ($contact->saveMessage($name, $email, $phone, $message)) {

    echo "Your message has been sent successfully.";
    echo '<p><a href="../index.php">Return to Homepage</a></p>';

} else {

    die("Message could not be sent. Please try again.");

}

?>