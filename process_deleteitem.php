<?php
session_start();

if (isset($_GET['itemIndex']) && isset($_SESSION['cart'][$_GET['itemIndex']])) {
    // Remove the item from the cart
    array_splice($_SESSION['cart'], $_GET['itemIndex'], 1);
}

// Redirect back to the cart page
header('Location: viewCart.php');
exit;