<?php

session_start();

function addToCart()
{
    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Initialize the cart if not set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Retrieve item details from POST data
        $name = $_POST['name'];
        $stock = $_POST['stock'];
        $price = $_POST['price'];
        $desc = $_POST['desc'];
        $image = $_POST['image'];
        $productType = $_POST['productType'];

        // Construct the item array
        $item = array(
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'desc' => $desc,
            'image' => $image,
            'productType' => $productType
        );

        // Flag to track if item is found
        $itemFound = false;

        // Check if the item already exists in the cart
        foreach ($_SESSION['cart'] as $index => $checkItem) {
            if ($checkItem['name'] == $item['name']) {
                // Update the item quantity if it exists
                $_SESSION['cart'][$index]['stock'] += $item['stock'];
                $_SESSION['cart'][$index]['price'] += $item['price'];
                $itemFound = true;
                break; // Stop the loop once item is found and updated
            }
        }

        // If the item was not found, add it to the cart
        if (!$itemFound) {
            $_SESSION['cart'][] = $item;
        }

        // Redirect after adding item to cart
        header('Location: viewCart.php');
        exit;
    }
}

addToCart();
