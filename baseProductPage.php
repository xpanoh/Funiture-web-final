<?php

session_start(); 
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";

function displayProducts($productType, $orderBy, $productID, $nameColumn, $imageColumn, $descriptionColumn, $stockColumn, $priceColumn, $searchTerm = "") {
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        return false;
    } else {
        $conn = new mysqli(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );
        // Check connection
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            return false;
        } else {
            // Sorting logic
            switch ($orderBy) {
                case 'price_asc':
                    $orderByClause = "ORDER BY $priceColumn ASC";
                    break;
                case 'price_desc':
                    $orderByClause = "ORDER BY $priceColumn DESC";
                    break;
                case 'name_asc':
                    $orderByClause = "ORDER BY $nameColumn ASC";
                    break;
                case 'name_desc':
                    $orderByClause = "ORDER BY $nameColumn DESC";
                    break;
                default:
                    $orderByClause = ''; // No sorting
                    break;
            }

            // Search logic
            $searchClause = "";
            if (!empty($searchTerm)) {
                $searchTerm = $conn->real_escape_string($searchTerm);
                $searchClause = "WHERE $nameColumn LIKE '%$searchTerm%' OR $descriptionColumn LIKE '%$searchTerm%'";
            }

            $sql = "SELECT $nameColumn, $imageColumn, $descriptionColumn, $stockColumn, $priceColumn FROM $productType $searchClause $orderByClause"; 
            $result = $conn->query($sql);
            
            // Check if there are any results
            if ($result->num_rows > 0) {
                // Open container
                echo "<div class='row'>";
                // Loop through each row and display the data
                $index = 1;
                while ($row = $result->fetch_assoc()) {
                    // If counter reaches 3, close the current row and open a new one
                    if ($counter % 3 == 0 && $counter != 0) {
                        echo "</div><div class='row'>";
                    }
                    echo "<div class='col-md-4'>";
                    echo "<div class='product-container' onclick='showProductModal(\"" . $index . "\", \"" . $row[$nameColumn] . "\", \"" . $row[$imageColumn] . "\", \"" . $row[$descriptionColumn] . "\", \"" . $row[$priceColumn] . "\", \"" . $row[$stockColumn] . "\", \"" . $row[$productID] . "\", \"" . $productType ."\")'>";

                    // Pass product details to JavaScript function
                    echo "<div class='product-img-container'>";
                    echo "<img class='product-image' src='". $row[$imageColumn] . "' alt='" . $row[$nameColumn] . "' />";
                    echo "</div>"; 
                    echo "<div class='product-details-container'>";
                    echo "<h6>" . $row[$nameColumn] . "</h6>";
                    echo "<p>S$" . $row[$priceColumn] . "</p>";
                    echo "<button class='add-to-cart-button' onclick='addToCart(\"" . $row[$nameColumn] . "\", \"" . $row[$priceColumn] . "\")'>Add to Cart</button>";

                    echo "</div>"; 
                    echo "</div>"; 
                    echo "</div>"; 
                    $counter++;
                }
                
                echo "</div>";
            } else {
                echo "No products found.";
            }
            
            // Close the database connection
            $conn->close();
            return true;
        }
    }
}



?>

<main class="container" style="margin-top:20px;">
    <div style="display: flex; align-items: center;">
        <!-- Sorting options -->
        <form id="sort-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" style="flex: 1;">
            <div class="sort-container">
                <select name="sort" aria-label="sort" id="sort">
                    <option value="none">None</option>
                    <option value="price_asc">Price - Low to High</option>
                    <option value="price_desc">Price - High to Low</option>
                    <option value="name_asc">Name - A to Z</option>
                    <option value="name_desc">Name - Z to A</option>
                </select>
                <input type="submit" value="Sort">
            </div>
        </form>

        <!-- Search form -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="search-form">
            <input type="text" name="search" id="search" placeholder="Search...">
            <button type="submit" aria-label="search"><i class="fa fa-search"></i></button>
        </form>
    </div>





