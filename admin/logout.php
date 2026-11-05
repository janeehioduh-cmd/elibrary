<?php

session_start();

// Remove all session variables
session_unset();

// Destroy the admin session
session_destroy();

// Redirect to admin login
header("Location: login.php");
exit();

?>