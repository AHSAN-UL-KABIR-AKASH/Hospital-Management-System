<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the homepage
header("Location: homePage.php"); // Replace 'index.php' with your homepage file name
exit();
?>