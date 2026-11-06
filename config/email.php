<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Gmail login details
define('MAIL_USERNAME', 'janeehioduh@gmail.com');
define('MAIL_PASSWORD', 'jmxdaxzfkanmdlok');

// Librarian's email address
define('LIBRARIAN_EMAIL', 'janeehioduh@gmail.com');


function sendLibrarianEmail($subject, $message) {

    $mail = new PHPMailer(true);

    try {

        // Gmail SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;

        // Use SSL on port 465
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Sender and receiver
        $mail->setFrom(MAIL_USERNAME, 'Otukpo E-Library');
        $mail->addAddress(LIBRARIAN_EMAIL);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        // Send email
        $mail->send();

        return true;

   } catch (Exception $e) {

    echo "EMAIL ERROR: " . htmlspecialchars($mail->ErrorInfo);
    return false;
}
}