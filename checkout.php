<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
include "process_retrievepromocodes.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
if (isset($_SESSION['promoCodes'])) {
    $promoCodes = getPromoCodes();
}
$totalPrice = 0.00;
$totalQuantity = 0;
?>
<main>
    <div class="container">
        <div class="row">
            <h1>Checking out.</h1>
            <div class="col-sm">
                <h3>Summary of Cart</h3>
                <?php if (count($_SESSION['cart']) > 0): ?>
                    <table class="table table-hover table-striped">
                        <thead class="thead">
                            <th scope="col">No.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
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
                                    <?php echo "</tr>" ?>
                                    <?php $totalQuantity += $item['stock'] ?>
                                    <?php $totalPrice += $item['price'] ?>
                                <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <th scope="row">Total</th>
                            <td>-</td>
                            <td>
                                <?php echo $totalQuantity; ?>
                            </td>
                            <td>
                                <?php echo $totalPrice; ?>
                            </td>
                        </tfoot>
                    </table>
                    <a href="viewCart.php">Return to edit items.</a>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>
            <div class="col-sm">
                <h3>Payment Method</h3>
                <form action="process_checkout.php" method="post">
                    <h4 class="mb-3">Add a Credit/Debit Card</h4>
                    <div class="mb-3">
                        <label for="card-number" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="card-number" name="card-number" autocomplete="off"
                            placeholder="Card number" required>
                    </div>
                    <div class="mb-3">
                        <label for="expiration-date" class="form-label">Expiration Date</label>
                        <input type="text" class="form-control" id="expiration-date" name="expiration-date"
                            autocomplete="off" placeholder="MM/YY" required>
                    </div>
                    <div class="mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="password" class="form-control" id="cvv" name="cvv" autocomplete="off"
                            placeholder="CVV" required>
                    </div>
                    <?php if (!isset($_SESSION['email'])): ?>
                        <h4>Non-Member payment</h4>
                        <div class="mb-3">
                            <label for="card-holder-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="card-holder-name" name="card-holder-name"
                                autocomplete="off" placeholder="Card holder name" required>
                        </div>
                        <div class="mb-3">
                            <label for="card-billing-address-street" class="form-label">Billing Address</label>
                            <input type="text" class="form-control" id="card-billing-address-street"
                                name="card-billing-address-street" autocomplete="off" placeholder="Street & unit number"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="card-billing-address-zip" class="form-label">ZIP / Postal Code</label>
                            <input type="text" class="form-control" id="card-billing-address-zip"
                                name="card-billing-address-zip" autocomplete="off" placeholder="ZIP / Postal Code" required>
                        </div>
                    <?php else: ?>
                        <script type="text/javascript">
                            var promoCodes = <?php echo json_encode($promoCodes); ?>;
                        </script>
                        <?php $_SESSION['codes'] = $promoCodes ?>
                        <h4>Membership Perks</h4>
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount Code</label>
                            <input type="text" class="form-control" id="discount" name="discount" autocomplete="off"
                                placeholder="Discount Code" onchange="updatePromocodeValid()">
                        </div>
                        <p id="validity">Checking if promocode is valid...</p>
                        <figure>
                            <figcaption class="blockquote-footer">
                                Do note that promocodes are 1-time only.
                            </figcaption>
                        </figure>
                    <?php endif ?>
                    <input type="submit" value="Proceed with payment">
                </form>
            </div>
        </div>


    </div>
</main>
<?php
include "inc/footer.inc.php";
?>