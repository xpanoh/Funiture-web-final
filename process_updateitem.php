<?php
// Example form handling in updateCart.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemIndex = $_POST['itemIndex'];
    $newQuantity = $_POST['stock'];
    $newPrice = $_POST['price'];

    if (isset($_SESSION['cart'][$itemIndex])) {
        $_SESSION['cart'][$itemIndex]['stock'] = $newQuantity;
        $_SESSION['cart'][$itemIndex]['price'] = $newPrice;
    }

    header('Location: viewCart.php');
    exit;
}
