<?php
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
include "process_retrievepromocodes.php";
function deductFromCart($success, $discount, $promoCodes)
{
    global $errorMsg, $success, $totalPrice, $originalPrice, $discountAmount;
    $productType = $_SESSION['cart'];
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');

    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        $success = false;
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
            $success = false;
        } else {
            // Check for discount code usage first
            $check = false;
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
                if ($discount !== '') {
                    // Discount code detected, now check if it is valid
                    foreach ($promoCodes as $code) {
                        if ($code['promocode'] === $discount) {
                            $check = true;
                            $discountAmount = $code['discount'];
                            break; // Exit the loop once a match is found
                        }
                    }
                    if ($check) {
                        // Now check if user has used code before
                        $queryForCode = $conn->prepare("SELECT promocodeusage FROM members WHERE email=?");
                        $queryForCode->bind_param("s", $email); // Bind parameter
                        $queryForCode->execute();
                        $queryForCode->store_result();
                        $queryForCode->bind_result($promocodeusage); // Bind result variable
                        $hasResult = $queryForCode->fetch();

                        $list = $hasResult && !empty($promocodeusage) ? explode(',', $promocodeusage) : [];

                        // If the discount code is not in the list or the promocodeusage is empty
                        if (!in_array($discount, $list)) {
                            // Append the code if promocodeusage is not empty, otherwise just set the discount
                            $updatedCodes = empty($promocodeusage) ? $discount : $promocodeusage . ',' . $discount;

                            // Update into database
                            $updateStmt = $conn->prepare("UPDATE members SET promocodeusage=? WHERE email=?");
                            $updateStmt->bind_param("ss", $updatedCodes, $email); // Bind parameters
                            $updateStmt->execute();
                        } else {
                            // Discount code has already been used
                            $errorMsg = "Discount code has already been used. Stopping transaction. To purchase without discount, leave the promocode field empty";
                            $check = false;
                            return;
                        }
                    }
                }
            }
            // deduct from database
            foreach ($_SESSION['cart'] as $index => $item) {
                $name = $item['name'];
                $cartStock = $item['stock'];
                $productType = $item['productType'];
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
                // get the current stock for the current item being processed
                $sql = "SELECT $stockRow FROM $productType WHERE $nameRow ='$name'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $currentStock = $row[$stockRow];
                $newStock = $currentStock - $cartStock;
                // check if over buying
                // if the new stock is < 0
                // stop the transaction break and set error message to reflect the change.
                if ($newStock < 0) {
                    $success = false;
                    $errorMsg = "Currently there is not enough stocks for an item. Consider checking our products to review your purchase.";
                    return;
                }
                // if ok, proceed to deduct
                // update the current costs
                $sql = "UPDATE $productType SET $stockRow ='$newStock' WHERE $nameRow = '$name'";
                $updatedStock = $conn->query($sql);
                $totalPrice += $item['price'];
            }
            $originalPrice = $totalPrice;
            $currentDateTime = date('Y-m-d H:i:s');

            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];

                // Prepare to get memberID securely
                $stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $id = $row['member_id'];

                    // Determine the total price based on whether a discount was applied
                    if ($check) {
                        $totalPrice = $originalPrice - ($originalPrice * $discountAmount / 100); // Assuming discountAmount is a percentage
                    }

                    // Prepare the transaction insertion with appropriate handling for optional discount
                    $sqlTransactions = $conn->prepare("INSERT INTO transactions (member_id, email, price, dateOfPurchase, discountUsed) VALUES (?, ?, ?, ?, ?)");
                    // Check if a discount was applied and adjust the parameters accordingly
                    $promoParam = $check ? $discount : null;
                    $sqlTransactions->bind_param("issss", $id, $email, $totalPrice, $currentDateTime, $promoParam);
                    $sqlTransactions->execute();
                }
            }
            $success = true;
            $conn->close();
            $errorMsg = 'Successfully Purchased, thank you!';
        }
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $discount = isset($_POST['discount']) ? $_POST['discount'] : null;
    // echo $discount;
    $promoCodes = getPromoCodes();
    // echo $promoCodes;
    //$name = isset($_POST['name']) ? $_POST['name'] : null;
    $success = false;
    deductFromCart($success, $discount, $promoCodes);
    // Unset specific session variables
    unset($_SESSION['cart']);
    unset($_SESSION['promoCodes']);
}

?>
<main>
    <?php if ($success): ?>
        <h2>
            <?php echo $errorMsg ?>
        </h2>
    <?php else: ?>
        <h2>Sorry, something went wrong.</h2>
        <h4>
            <?php echo $errorMsg ?>
        </h4>
    <?php endif; ?>
</main>
<?php
include "inc/footer.inc.php";
?>