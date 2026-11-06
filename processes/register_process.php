<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Member.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit();
}

// Receive form information
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate empty fields
if (
    $name === '' ||
    $email === '' ||
    $phone === '' ||
    $password === '' ||
    $confirm_password === ''
) {
    die("Please fill in all required fields.");
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}

// Check password length
if (strlen($password) < 8) {
    die("Password must contain at least 8 characters.");
}

// Check password confirmation
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Connect to database
$db = new Database();
$conn = $db->connect();

$member = new Member($conn);

// Check whether email already exists
if ($member->emailExists($email)) {
    die("This email address is already registered.");
}

// Register member without a profile picture
if ($member->register(
    $name,
    $email,
    $phone,
    $password,
    null
)) {

    header("Location: ../login.php");
    exit();

} else {

    die("Registration failed. Please try again.");

}

?>