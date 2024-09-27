<?php
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
// include "inc/chatbot.inc.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

?>
<main>
    <h2>View Cart</h2>
    <?php if (count($_SESSION['cart']) > 0): ?>
        <table class="table table-hover table-striped">
            <thead class="thead">
                <th scope="col">No.</th>
                <th scope="col">Name</th>
                <th scope="col">Quantity</th>
                <th scope="col">Price</th>
                <th scope="col">Description</th>
                <th scope="col">Update</th>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <?php echo "<tr>" ?>
                    <th scope='row'>
                        <?php echo $index + 1; ?>
                    </th>
                    <td>
                        <?php echo $item['name']; ?>
                    </td>
                    <td>
                        <?php echo $item['stock']; ?>
                    </td>
                    <td>
                        <?php echo $item['price']; ?>
                    </td>
                    <td>
                        <?php echo $item['desc']; ?>
                    </td>
                    <td>
                        <a href="updateCart.php?itemIndex=<?php echo $index; ?>">Update Item</a>
                        <br>
                        <a href="process_deleteitem.php?itemIndex=<?php echo $index; ?>">Delete Item</a>
                    </td>
                    <?php echo "</tr>" ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="process_deletecart.php">Empty this Cart</a></li>
                <li class="breadcrumb-item"><a href="checkout.php">Proceed to Checkout</li>
            </ol>
        </nav>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</main>
<?php
include "inc/footer.inc.php";
?>