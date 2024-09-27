<?php
// session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
include "process_viewHistory.php";
$history = getTransactionHistory();
$history = isset($history) && is_array($history) ? $history : [];
?>

<main>
    <div class="container">
        <h2>View Purchase History</h2>
        <?php if (count($history) > 0): ?>
            <div class="row">
                <h2>Date</h2>
                <div class="col-4">
                    <div id="list-example" class="list-group">
                        <?php foreach ($history as $index => $item): ?>
                            <a class="list-group-item list-group-item-action" href="#list-item-<?php echo $index ?>">
                                <?php echo $index + 1 ?>.
                                <?php
                                $incDate = $item['date'];
                                if ($incDate) {
                                    $date = strtotime($incDate);
                                    echo date('d/M/Y', $date);
                                } else {
                                    echo "Date is not set.";
                                }
                                ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-8">
                    <div data-bs-spy="scroll" data-bs-target="#list-example" data-bs-smooth-scroll="true"
                        class="scrollspy-example" tabindex="0">
                        <?php foreach ($history as $index => $item): ?>
                            <h4 id="list-item-<?php echo $index + 1 ?>">
                                <?php
                                $incDate = $item['date'];
                                if ($incDate) {
                                    $date = strtotime($incDate);
                                    echo date('d/M/Y', $date);
                                } else {
                                    echo "Date is not set.";
                                }
                                ?>
                            </h4>
                            <p>Spent :
                                <?php echo $item['price'] ?>
                            </p>
                            <p>Discount code :
                                <?php echo $item['discountUsed'] ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>You have made 0 purchases.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include "inc/footer.inc.php";
?>