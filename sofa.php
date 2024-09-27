<?php
include "baseProductPage.php";
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<?php
    displayProducts("sofas", isset($_GET['sort']) ? $_GET['sort'] : 'none', "sofaID", "sofaName", "sofaImagePath", "sofaDescription", "sofaStock", "sofaPrice", $searchTerm);
?>

</main>

<?php
include "inc/footer.inc.php";
?>