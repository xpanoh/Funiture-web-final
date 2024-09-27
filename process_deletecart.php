<?php
session_start(); // Ensure session is started

// Unset specific session variables
unset($_SESSION['cart']);

// Optionally, you can destroy the whole session if you don't need it anymore
session_destroy();

// Redirect to home.php
header('Location: viewCart.php');
exit();
