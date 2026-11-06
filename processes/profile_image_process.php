<?php

session_start();

require_once __DIR__ . "/../config/database.php";

// Ensure member is logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard.php");
    exit();
}

// Check whether an image was selected
if (
    !isset($_FILES['profile_image']) ||
    $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE
) {
    die("Please select a profile picture.");
}

$file = $_FILES['profile_image'];

// Check upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    die("Profile picture upload failed. Please try again.");
}

// Maximum file size: 2 MB
if ($file['size'] > 2 * 1024 * 1024) {
    die("Profile picture must not exceed 2 MB.");
}

// Validate actual image
$imageInfo = getimagesize($file['tmp_name']);

if ($imageInfo === false) {
    die("Please upload a valid image.");
}

// Detect image MIME type
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

// Connect to database
$db = new Database();
$conn = $db->connect();

// Get the member's existing profile picture
$stmt = $conn->prepare("
    SELECT profile_image
    FROM members
    WHERE id = :member_id
");

$stmt->execute([
    ':member_id' => $_SESSION['member_id']
]);

$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    die("Member account not found.");
}

// Create upload directory if it does not exist
$uploadDirectory = __DIR__ . "/../uploads/profiles/";

if (!is_dir($uploadDirectory)) {
    if (!mkdir($uploadDirectory, 0755, true)) {
        die("Unable to create profile picture folder.");
    }
}

// Check folder permissions
if (!is_writable($uploadDirectory)) {
    die("Profile picture folder is not writable.");
}

// Generate unique filename
$extension = $allowedTypes[$mimeType];
$newImage = bin2hex(random_bytes(16)) . "." . $extension;

$uploadPath = $uploadDirectory . $newImage;

// Move uploaded image
if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    die("Unable to save profile picture.");
}

// Save image path in database
$imagePath = "uploads/profiles/" . $newImage;

$update = $conn->prepare("
    UPDATE members
    SET profile_image = :profile_image
    WHERE id = :member_id
");

$updated = $update->execute([
    ':profile_image' => $imagePath,
    ':member_id' => $_SESSION['member_id']
]);

if ($updated) {

    // Delete previous profile picture
    if (!empty($member['profile_image'])) {

        $oldImagePath = __DIR__ . "/../" . $member['profile_image'];

        if (
            is_file($oldImagePath) &&
            strpos(
                realpath($oldImagePath),
                realpath($uploadDirectory)
            ) === 0
        ) {
            unlink($oldImagePath);
        }
    }

    header("Location: ../dashboard.php?image=success");
    exit();

} else {

    // Delete newly uploaded image if database update fails
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
    }

    die("Unable to update your profile picture. Please try again.");
}

?>