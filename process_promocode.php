<body>
    <?php include "inc/head.inc.php";            
            include "inc/header.inc.php";
            include "inc/nav.inc.php"; ?>


<?php

    // Helper function to write the member data to the database
    function saveCodeToDB($promocode, $discount, &$errorMsg) {
        // Create database connection.
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
                // Prepare the statement
                $stmt = $conn->prepare("INSERT INTO promocodes (promocode, discount) VALUES (?, ?)");
                // Bind & execute the query statement
                $stmt->bind_param("ss", $promocode, $discount);
                if (!$stmt->execute()) {
                    $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                    return false;
                }
                $stmt->close();
                $conn->close();
                return true;
            }
        }
    }
    $promocode = $discount = $errorMsg = "";
    $promocode = $_POST["promocode"];
    $discount = $_POST["promocodedisc"];

        if (saveCodeToDB($promocode, $discount, $errorMsg)) {
            $_SESSION['success_message'] = "Promo code has been added.";
            header("Location: profile.php");
            exit();
        } else {
            echo "<br><div class=\"container\"><hr><h2>Oops!</h2><h4>An error occurred while saving to the database:</h4>";
            echo "<p>" . $errorMsg . "</p>";
            echo '<button class="btn btn-primary" onclick="window.location.href=\'register.php\'">Return to Sign Up</button></div><br>';
        }
?>

<?php include "inc/footer.inc.php"; ?>
</body>
