<?php
session_start(); // Ensure session is started

// Unset specific session variables
unset($_SESSION['fname']);
unset($_SESSION['lname']);
unset($_SESSION['email']);

// Optionally, you can destroy the whole session if you don't need it anymore
session_destroy();

// Redirect to home.php
header('Location: home.php');
exit();
?>
