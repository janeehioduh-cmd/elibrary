<?php

require_once __DIR__ . "/config/email.php";

$sent = sendLibrarianEmail(
    "E-Library Email Test",
    "<h2>Email Test Successful!</h2>
     <p>Your Otukpo E-Library email notification system is working.</p>
     <p>You can now receive new borrowing request notifications.</p>"
);

if ($sent) {
    echo "Success! Email sent to the librarian.";
} else {
    echo "Email could not be sent. Please check the email configuration.";
}