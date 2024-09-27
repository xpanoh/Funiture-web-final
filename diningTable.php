<?php
include "baseProductPage.php";
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<?php
    displayProducts("diningTables", isset($_GET['sort']) ? $_GET['sort'] : 'none', "diningTableID", "diningTableName", "diningTableImagePath", "diningTableDescription", "diningTableStock", "diningTablePrice", $searchTerm);
?>
</main>

<?php
include "inc/footer.inc.php";
?>