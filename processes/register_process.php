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


// PROFILE PICTURE UPLOAD
$profileImage = null;
$uploadedFilePath = null;

if (isset($_FILES['profile_image']) &&
    $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {

    $file = $_FILES['profile_image'];

    // Check upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Profile picture upload failed. Please try again.");
    }

    // Maximum file size: 2 MB
    if ($file['size'] > 2 * 1024 * 1024) {
        die("Profile picture must not exceed 2 MB.");
    }

    // Check actual image type
    $imageInfo = getimagesize($file['tmp_name']);

    if ($imageInfo === false) {
        die("Please upload a valid image.");
    }

    // Detect MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Allowed image formats
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (
        !isset($allowedTypes[$mimeType]) ||
        $imageInfo['mime'] !== $mimeType
    ) {
        die("Only JPG, PNG and WEBP images are allowed.");
    }

    // Generate a unique filename
    $extension = $allowedTypes[$mimeType];

    $profileImage = bin2hex(random_bytes(16)) . "." . $extension;

    // Upload destination
    $uploadDirectory = __DIR__ . "/../uploads/profiles/";

    if (!is_dir($uploadDirectory) ||
        !is_writable($uploadDirectory)) {
        die("Profile picture folder is not available.");
    }

    $uploadedFilePath = $uploadDirectory . $profileImage;

    // Move uploaded image
    if (!move_uploaded_file($file['tmp_name'], $uploadedFilePath)) {
        die("Unable to save profile picture.");
    }
}


// REGISTER MEMBER
if ($member->register(
    $name,
    $email,
    $phone,
    $password,
    $profileImage
)) {

    header("Location: ../login.php");
    exit();

} else {

    // Delete uploaded image if registration fails
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
    }

    die("Registration failed. Please try again.");

}

?>