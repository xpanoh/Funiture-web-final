<?php
include "baseProductPage.php";
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<?php
displayProducts("coffeeTables", isset($_GET['sort']) ? $_GET['sort'] : 'none', "coffeeTableID", "coffeeTableName", "coffeeTableImagePath", "coffeeTableDescription", "coffeeTableStock", "coffeeTablePrice", $searchTerm);
?>

</main>

<?php
include "inc/footer.inc.php";
?>