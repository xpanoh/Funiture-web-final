<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
// include "inc/chatbot.inc.php";

if (isset($_GET['itemIndex'])) {
    $itemIndex = $_GET['itemIndex'];
    $item = $_SESSION['cart'][$itemIndex];
    // echo '<pre>' . print_r($_SESSION['cart'], true) . '</pre>';
    // echo '<pre>' . print_r($item, true) . '</pre>';
    // exit;
    $productType = $item['productType'];
    $name = $item['name'];
    function getStockQuantity($productType, $name)
    {
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
                switch ($productType) {
                    case 'chairs':
                        $stockRow = 'chairStock';
                        $nameRow = 'chairName';
                        break;
                    case 'coffeeTables':
                        $stockRow = 'coffeeTableStock';
                        $nameRow = 'coffeeTableName';
                        break;
                    case 'diningTables':
                        $stockRow = 'diningTableStock';
                        $nameRow = 'diningTableName';
                        break;
                    case 'sofas':
                        $stockRow = 'sofaStock';
                        $nameRow = 'sofaName';
                        break;
                }
                $sql = "SELECT $stockRow FROM $productType WHERE $nameRow = '$name'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                return $row[$stockRow];
            }
        }
    }
    $currentStock = getStockQuantity($productType, $name);

    ?>
    <main>
    <form action="process_updateitem.php" method="post">
        <input type="hidden" name="itemIndex" value="<?php echo $itemIndex; ?>">
        <div class="mb-3">
            Price per piece: $
            <?php echo htmlspecialchars($item['price']); ?>
            <div class="total_price">
                <p>Total : $<span id="total">
                        <?php echo htmlspecialchars($item['price']); ?>
                    </span></p>
            </div>
            <input type="hidden" name="price" id="price" value="<?php echo htmlspecialchars($item['price']); ?>">
        </div>
        <label for="newQuantity">New Quantity:</label>
        <div class="mb-3">
            <label for="stock" class="form-label">
                Select amount for purchase
            </label>
            <select class="form-select" name="stock" aria-label="Default select example" id="stock"
                onchange="updatePrice(<?php echo htmlspecialchars($item['price']); ?>)">
                <?php
                for ($i = 1; $i <= $currentStock; $i++) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit">Update</button>
    </form>
    </main>
    
    <?php
}
?>