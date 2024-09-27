<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
// include "inc/chatbot.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $desc = $_POST['desc'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];
    $productType = $_POST['product_type'];
}
$referenceType = $productType;
$referenceName = $name;
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
$currentStock = getStockQuantity($referenceType, $referenceName);
?>
<main>
    <div class="container">
        <h2>
            Purchasing.
        </h2>
        <div class="row" id="confirmation">
            <form action="process_addtocart.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">
                        Name of Product:
                        <?php echo htmlspecialchars($name); ?>
                    </label>
                    <input type="hidden" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
                </div>
                <div class="mb-3">
                    Description:
                    <?php echo htmlspecialchars($desc); ?>
                    <input type="hidden" name="desc" id="desc" value="<?php echo htmlspecialchars($desc); ?>">
                </div>
                <div class="mb-3">
                    Price per piece: $
                    <?php echo htmlspecialchars($price); ?>
                    <div class="total_price">
                        <p>Total : $<span id="total">
                                <?php echo htmlspecialchars($price); ?>
                            </span></p>
                    </div>
                    <input type="hidden" name="price" id="price" value="<?php echo htmlspecialchars($price); ?>">
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">
                        Select amount for purchase
                    </label>
                    <select class="form-select" name="stock" aria-label="Default select example" id="stock"
                        onchange="updatePrice(<?php echo htmlspecialchars($price); ?>)">
                        <?php
                        for ($i = 1; $i <= $currentStock; $i++) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="image" id="image" value="<?php echo $image; ?>">
                <input type="hidden" name="productType" id="productType"
                    value="<?php echo htmlspecialchars($productType); ?>">
                <input type="submit" value="Add item to Cart">
            </form>
        </div>
    </div>
</main>
<?php
include "inc/footer.inc.php";
?>