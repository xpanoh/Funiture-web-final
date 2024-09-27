<?php
include "baseProductPage.php";
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>


<?php
displayProducts("chairs", isset($_GET['sort']) ? $_GET['sort'] : 'none', "chairID", "chairName", "chairImagePath", "chairDescription", "chairStock", "chairPrice", $searchTerm);
?>

</main>

<?php
include "inc/footer.inc.php";
?>