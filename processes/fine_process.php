<?php

session_start();

// Protect the process
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Fine.php";

// Accept POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/fines.php");
    exit();
}

// Validate fine ID
$fineId = filter_input(INPUT_POST, 'fine_id', FILTER_VALIDATE_INT);

if (!$fineId || $fineId < 1) {
    header("Location: ../admin/fines.php");
    exit();
}

// Connect to database
$db = new Database();
$conn = $db->connect();

$fine = new Fine($conn);

// Record payment
$result = $fine->markFineAsPaid($fineId);

if ($result) {
    $_SESSION['success_message'] = "Fine payment recorded successfully.";
} else {
    $_SESSION['error_message'] = "Unable to record payment. The fine may already be paid.";
}

header("Location: ../admin/fines.php");
exit();

?>