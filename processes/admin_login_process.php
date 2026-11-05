<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Admin.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/login.php");
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

$admin = new Admin($conn);

// Verify administrator details
$adminDetails = $admin->login($email, $password);

if (!$adminDetails) {
    die("Invalid administrator email or password.");
}

// Regenerate session ID
session_regenerate_id(true);

// Store admin information
$_SESSION['admin_id'] = $adminDetails['id'];
$_SESSION['admin_name'] = $adminDetails['name'];

// Redirect to admin dashboard
header("Location: ../admin/dashboard.php");
exit();

?>
