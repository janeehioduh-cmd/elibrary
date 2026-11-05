<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Member.php";

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
    die("Please enter your email and password.");
}

// Connect to database
$db = new Database();
$conn = $db->connect();

$member = new Member($conn);

// Verify login details
$memberDetails = $member->login($email, $password);

if (!$memberDetails) {
    die("Invalid email or password.");
}

// Check account status
if ($memberDetails['status'] !== 'active') {
    die("Your account has been suspended.");
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